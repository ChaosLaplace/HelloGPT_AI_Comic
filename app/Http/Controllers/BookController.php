<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// 繪本模塊
class BookController extends Controller
{
    // 2.1 獲取繪本列表
    public function booklist() {
        return 'booklist';
    }
    // 2.2 預覽繪本內容
    public function read() {
        return 'read';
    }
    // 2.3 存取繪本資訊
    public function infoPost() {
        return 'infoPost';
    }
    public function infoGet() {
        return 'infoGet';
    }
    // 2.4 存取故事內容
    public function dataPost() {
        return 'dataPost';
    }
    public function dataGet() {
        return 'dataGet';
    }
    // 2.5 繪本輸出下載
    public function download() {
        return 'download';
    }
}
