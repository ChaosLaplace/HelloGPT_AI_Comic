<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

use Throwable;
// 工具模塊
class ToolController extends Controller
{
    // 取得 CSRF
    public function getCSRF() {
        return csrf_token();
    }
    // 3.1 繪本書名翻譯
    public function translate(Request $request) {
        // 參數驗證
        $paramValid = self::paramValid($request, [
            'translate_language' => 'bail|required|max:15',
            'input_text'         => 'bail|required|max:100',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證');
        }

        return $this->AITranslate($request);
    }
    // 3.2 文字生成故事
    public function textStory(Request $request) {
        // 參數驗證
        $paramValid = self::paramValid($request, [
            'user_input' => 'bail|required|max:255',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證');
        }
        
        return $this->AIStory($request);
    }
    // 3.3 故事生成參數
    public function storyPrompt(Request $request) {
        return 'storyPrompt';
    }
    // 3.4 參數生成圖片
    public function promptImg(Request $request) {
        return 'promptImg';
    }
    // 參數驗證
    private static function paramValid(Request $request, Array $data) {
        $validator = Validator::make($request->all(), $data);
        if ( $validator->fails() ) {
            return False;
        }
        return True;
    }
    // AI 翻譯
    private static function AITranslate(Request $request) {
        try {
            $translate = $request->input('translate_language');
            $content   = $request->input('input_text');
            $type      = explode('_', $translate);

            App::setLocale($type['0']);
            $lanBefore = __('messages.' . $type['0']);
            $lanAfter  = __('messages.' . $type['2']);
            $messages  = [
                ['role' => 'system', 'content' => "你是個善於將{$lanBefore}翻譯成{$lanAfter}的翻譯家"],
                ['role' => 'user', 'content' => "請把以下{$lanBefore}翻譯為{$lanAfter} \n {$content}"],
            ];

            return self::AICompletions($messages);
        } catch (Throwable $e) {
            return self::errorLog($e);
        }
    }
    // AI 生成故事
    private static function AIStory(Request $request) {
        try {
            $content  = $request->input('user_input');
            $messages = [
                ['role' => 'system', 'content' => "你是個善於將片段的文字，包裝成有教育意義情節的兒童繪本作家"],
                ['role' => 'user', 'content' => "請把以下片段的文字，延伸成一頁故事段落，不超過40字 \n {$content}"],
            ];

            return self::AICompletions($messages);
        } catch (Throwable $e) {
            return self::errorLog($e);
        }
    }
    // Open AI API
    private static function AICompletions($messages) {
        try {
            $url      = 'https://api.openai.com/v1/chat/completions';
            $response = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer test',
                // 'Authorization' => 'Bearer ' . env('OPEN_AI_KEY'),
            ])->withOptions([
                'verify' => false,
            ])->post($url, [
                'model'    => env('OPEN_AI_MODULE'),
                'messages' => $messages,
            ]);

            $json = $response->json();
            if ( isset($json['error']) ) {
                return self::responseFail( trim($json['error']['message']) );
            }

            if ( isset($json['choices'][0]['message']['content']) ) {
                $data = [
                    'translate_text' => trim($json['choices'][0]['message']['content'])
                ];
                return self::responseSuccess($data);
            }

            return self::responseFail( json_encode($json, JSON_UNESCAPED_UNICODE) );
        } catch (Throwable $e) {
            return self::errorLog($e);
        }
    }
}
