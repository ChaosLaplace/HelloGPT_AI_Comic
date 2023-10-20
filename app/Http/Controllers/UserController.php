<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// 用戶模塊
class UserController extends Controller
{
    // 1.1 用戶註冊
    public function register() {
        return 'register';
    }
    // 1.2 用戶登入
    public function login() {
        return 'login';
    }
    // 1.3 用戶忘記密碼
    public function resetpw() {
        return 'resetpw';
    }
    // 1.4 用戶資料獲取與修改
    public function profilePost() {
        return 'profilePost';
    }
    public function profileGet() {
        return 'profileGet';
    }
    // 1.5 用戶修改密碼
    public function changepwd() {
        return 'changepwd';
    }
    // 1.6 用戶第三方登入 Google api
    public function googlelogin() {
        return 'googlelogin';
    }
}
