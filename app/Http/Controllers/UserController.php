<?php

namespace App\Http\Controllers;

use App\Models\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Throwable;
// 用戶模塊
class UserController extends Controller
{
    // 1.1 用戶註冊
    public function register(Request $request) {
        // 參數驗證
        $paramValid = self::paramValid($request, [
            'name'     => 'bail|required|max:25',
            'email'    => 'bail|required|max:50|email',
            'username' => 'bail|required|max:50',
            'password' => 'bail|required|max:50',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證');
        }

        // // 從請求中獲取用戶提交的註冊資料
        // $data = $request->only(['name', 'email', 'password']);

        // // 創建新的用戶記錄
        // $user = User::create($data);

        // // 其他註冊相關邏輯...

        // // 返回註冊成功的頁面或其他操作
        if ( true ) {
            return self::responseSuccess([], '註冊成功！');
        }
        return self::responseFail('此帳號已註冊！');
    }
    // 1.2 用戶登入
    public function login(Request $request) {
        // 參數驗證
        $paramValid = self::paramValid($request, [
            'username' => 'bail|required|max:50',
            'password' => 'bail|required|max:50',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證');
        }

        if ( true ) {
            return self::responseSuccess([], '登入成功！');
        }
        return self::responseFail('帳號或密碼錯誤！');
    }
    // 1.3 用戶忘記密碼
    public function resetpw(Request $request) {
        // 參數驗證
        $paramValid = self::paramValid($request, [
            'email'    => 'bail|required|max:50|email',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證');
        }

        if ( true ) {
            return self::responseSuccess([], '已寄至您的信箱！');
        }
        return self::responseFail('無此註冊信箱！');
    }
    // 1.4 用戶資料獲取與修改
    public function profilePost(Request $request) {
        // 參數驗證
        $paramValid = self::paramValid($request, [
            'email'    => 'bail|required|max:50|email',
            'name'     => 'bail|required|max:50',
            'age'      => 'bail|required|max:10',
            'birthday' => 'bail|required|max:25',
            'gender'   => 'bail|required|max:10',
            'avatar'   => 'bail|required|max:50',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證');
        }

        if ( true ) {
            return self::responseSuccess([], '修改成功！');
        }
        return self::responseFail('修改失敗：....');
    }
    public function profileGet(Request $request) {
        if ( true ) {
            $data = [
                'username' => '',
                'email'    => '',
                'name'     => '',
                'age'      => '',
                'birthday' => '',
                'gender'   => '',
                'avatar'   => '',
            ];
            return self::responseSuccess($data);
        }
        return self::responseFail('無此註冊信箱！');
    }
    // 1.5 用戶修改密碼
    public function changepwd(Request $request) {
        // 參數驗證
        $paramValid = self::paramValid($request, [
            'password_old' => 'bail|required|max:50',
            'password_new' => 'bail|required|max:50',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證');
        }

        if ( true ) {
            return self::responseSuccess([], '修改成功！');
        }
        return self::responseFail('舊密碼錯誤！');
    }
    // 1.6 用戶第三方登入 Google api
    public function googlelogin(Request $request) {
        return 'googlelogin';
    }
}
