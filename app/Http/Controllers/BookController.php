<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// 繪本模塊
class BookController extends Controller
{
    // 上架繪本（類似創作者介面）& 定價可以自己定
    public function store() {
        return 'store';
    }
    // 審核繪本(通過或下架)
    public function verify() {
        return 'verify';
    }
}
