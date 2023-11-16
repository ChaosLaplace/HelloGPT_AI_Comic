<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

use Throwable;
// 工具模塊
class ToolController extends Controller
{
    // 繪本書名翻譯
    public function translate(Request $request) {
        $paramValid = self::paramValid($request, [
            'translate_language' => 'bail|required|max:15|string',
            'input_text'         => 'bail|required|max:100|string',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證錯誤');
        }

        return $this->AITranslate($request);
    }
    // 文字生成故事
    public function textStory(Request $request) {
        $paramValid = self::paramValid($request, [
            'user_input' => 'bail|required|max:255|string',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證錯誤');
        }
        
        return $this->AIStory($request);
    }
    // 故事生成提示詞
    public function storyPrompt(Request $request) {
        $paramValid = self::paramValid($request, [
            'where' => 'bail|required|max:25|string',
            'who'   => 'bail|required|max:25|string',
            'what'  => 'bail|required|max:25|string',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證錯誤');
        }
        
        return $this->AIPrompt($request);
    }
    // 提示詞生成圖片
    public function promptImg(Request $request) {
        return 'promptImg';
    }
    // AI 翻譯
    private static function AITranslate(Request $request) {
        try {
            $translate = $request->input('translate_language');
            $content   = $request->input('input_text');
            $type      = explode('_', $translate);

            App::setLocale($type['0']);
            $lanBefore = __('messages.' . $type['0']);
            $lanAfter  = __('messages.' . $type['1']);
            $messages  = [
                ['role' => 'system', 'content' => "你是個善於將{$lanBefore}翻譯成{$lanAfter}的翻譯家"],
                ['role' => 'user', 'content' => "請把以下{$lanBefore}翻譯為{$lanAfter} \n {$content}"],
            ];

            $json = self::AICompletions($messages);
            if ( isset($json['choices'][0]['message']['content']) ) {
                $data = [
                    'translate_text' => trim($json['choices'][0]['message']['content'])
                ];
                return self::responseSuccess($data);
            }
            return self::responseFail();
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

            $json = self::AICompletions($messages);
            if ( isset($json['choices'][0]['message']['content']) ) {
                $data = [
                    'ch_story_ai' => trim($json['choices'][0]['message']['content'])
                ];
                return self::responseSuccess($data);
            }
            return self::responseFail();
        } catch (Throwable $e) {
            return self::errorLog($e);
        }
    }
    // AI 生成提示詞
    private static function AIPrompt(Request $request) {
        try {
            $where    = $request->input('where');
            $who      = $request->input('who');
            $what     = $request->input('what');
            $messages = [
                ['role' => 'system', 'content' => "我正在使用一個名為 Midjourney 的 AI 繪圖工具，指定你成為 Midjourney 的提示生成器，你將在不同情況下用英文生成適合的 prompt，我會在主題前加上斜線 / 作為標記，當輸入 /運動鞋商品圖片，你將生成 prompt 提示詞 『Realistic true details photography of sports shoes, y2k, lively, bright colors, product photography, Sony A7R IV, clean sharp focus』"],
                ['role' => 'user', 'content' => "請生成三段prompt 提示詞，按照順序分別是，1.描述故事發生的地方，2.那裡有誰?有什麼?，3.發生什麼事?， \n /{$where}，/{$who}，/{$what}"],
            ];

            $json = self::AICompletions($messages);
            if ( isset($json['choices'][0]['message']['content']) ) {
                $data = [
                    'story_pic_prompt' => trim($json['choices'][0]['message']['content'])
                ];
                return self::responseSuccess($data);
            }
            return self::responseFail('參數生成失敗！');
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
                // 'Authorization' => 'Bearer test',
                'Authorization' => 'Bearer ' . env('OPEN_AI_KEY'),
            ])->withOptions([
                'verify' => false,
            ])->post($url, [
                'model'    => env('OPEN_AI_MODULE'),
                'messages' => $messages,
            ]);

            return $response->json();
        } catch (Throwable $e) {
            return self::errorLog($e);
        }
    }
}
