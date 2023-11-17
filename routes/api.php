<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SeleniumController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\UserController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 模擬用戶發文
Route::get('selenium/Eyny', [SeleniumController::class, 'Eyny']);







// 用戶註冊
Route::post('user/register', [UserController::class, 'register']);
// 用戶登入
Route::post('user/login', [UserController::class, 'login']);
// 用戶忘記密碼
Route::post('user/resetpw', [UserController::class, 'resetpw']);

// 驗證 Token 並刷新
Route::group(['middleware' => ['jwt.auth', 'refresh.token']], function () {
    // 用戶模塊
    Route::group(['prefix' => 'user'], function () {
        // 用戶資料修改
        Route::post('profile', [UserController::class, 'saveProfile']);
        // 用戶資料獲取
        Route::get('profile', [UserController::class, 'getProfile']);
        // 用戶修改密碼
        Route::post('changepwd', [UserController::class, 'changepwd']);
        // 用戶第三方登入 Google api
        Route::post('googlelogin', [UserController::class, 'googlelogin']);
    });
    // 繪本模塊
    Route::group(['prefix' => 'book'], function () {
        // 上架繪本（類似創作者介面）& 定價可以自己定
        Route::post('store', [BookController::class, 'store']);
        // 審核繪本(通過或下架)
        Route::get('verify', [BookController::class, 'verify']);
    });
    // 工具模塊
    Route::group(['prefix' => 'tool'], function () {
        // 繪本書名翻譯
        Route::get('translate', [ToolController::class, 'translate']);
        // 字生成故事
        Route::get('text-to-story', [ToolController::class, 'textStory']);
        // 故事生成提示詞
        Route::get('story-to-prompt', [ToolController::class, 'storyPrompt']);
        // 提示詞生成圖片
        Route::get('prompt-to-img', [ToolController::class, 'promptImg']);
    });
    // 金流模塊
    Route::group(['prefix' => 'payment'], function () {
        // 金流儲值
        Route::post('index', [PaymentController::class, 'index']);
        // 金流提現
        Route::post('withdraw', [PaymentController::class, 'withdraw']);
        // 金流成交紀錄(付款收款)
        Route::get('record', [PaymentController::class, 'record']);
    });
});
