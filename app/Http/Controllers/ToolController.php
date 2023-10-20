<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// 工具模塊
class ToolController extends Controller
{
    // 取得 CSRF
    public function getCSRF(Request $request) {
        return csrf_token();
    }
    // 3.1 繪本書名翻譯
    public function translate() {
        return 'translate';
    }
    // 3.2 文字生成故事
    public function textStoryPost() {
        return 'textStoryPost';
    }
    public function textStoryGet() {
        return 'textStoryGet';
    }
    // 3.3 故事生成參數
    public function storyPromptPost() {
        return 'storyPromptPost';
    }
    public function storyPromptGet() {
        return 'storyPromptGet';
    }
    // 3.4 參數生成圖片
    public function promptImgPost() {
        return 'promptImgPost';
    }
    public function promptImgGet() {
        return 'promptImgGet';
    }
}
