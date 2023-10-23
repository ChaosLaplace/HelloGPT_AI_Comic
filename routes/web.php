<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'api'], function () {
    // 用戶模塊
    Route::group(['prefix' => 'user'], function () {
        // 1.1 用戶註冊
        Route::post('register', [UserController::class, 'register']);
        // 1.2 用戶登入
        Route::post('login', [UserController::class, 'login']);
        // 1.3 用戶忘記密碼
        Route::post('resetpw', [UserController::class, 'resetpw']);
        // 1.4 用戶資料獲取與修改
        Route::post('profile', [UserController::class, 'profilePost']);
        Route::get('profile', [UserController::class, 'profileGet']);
        // 1.5 用戶修改密碼
        Route::post('changepwd', [UserController::class, 'changepwd']);
        // 1.6 用戶第三方登入 Google api
        Route::post('googlelogin', [UserController::class, 'googlelogin']);
    });
    // 繪本模塊
    Route::group(['prefix' => 'book'], function () {
        // 2.1 獲取繪本列表
        Route::get('booklist', [BookController::class, 'booklist']);
        // 2.2 預覽繪本內容
        Route::get('read', [BookController::class, 'read']);
        // 2.3 存取繪本資訊
        Route::post('info', [BookController::class, 'infoPost']);
        Route::get('info', [BookController::class, 'infoGet']);
        // 2.4 存取故事內容
        Route::post('data', [BookController::class, 'dataPost']);
        Route::get('data', [BookController::class, 'dataGet']);
        // 2.5 繪本輸出下載
        Route::get('download', [BookController::class, 'download']);
    });
    // 工具模塊
    Route::group(['prefix' => 'tool'], function () {
        // 取得 CSRF
        Route::get('getCSRF', [ToolController::class, 'getCSRF']);
        // 3.1 繪本書名翻譯
        Route::get('translate', [ToolController::class, 'translate']);
        // 3.2 文字生成故事
        Route::get('text-to-story', [ToolController::class, 'textStory']);
        // 3.3 故事生成參數
        Route::get('story-to-prompt', [ToolController::class, 'storyPrompt']);
        // 3.4 參數生成圖片
        Route::get('prompt-to-img', [ToolController::class, 'promptImg']);
    });
});
