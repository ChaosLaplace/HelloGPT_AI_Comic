<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Tymon\JWTAuth\Facades\JWTAuth;

use Throwable;
// 用戶模塊
class UserController extends Controller
{
    // 1.1 用戶註冊
    public function register(Request $request) {
        // 參數驗證
        $paramValid = self::paramValid($request, [
            'name'     => 'bail|required|max:50|string',
            'email'    => 'bail|required|max:50|string|email',
            'username' => 'bail|required|max:50|string',
            'password' => 'bail|required|max:50|string',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證');
        }

        try {
            $credentials = request(['name', 'email', 'username', 'password']);
            if ( !User::checkAccountExist($credentials) ) {
                $user = User::create($credentials);
                if ( $user ) {
                    return self::responseSuccess([], '註冊成功！');
                }
            }
            return self::responseFail('此帳號已註冊！');
        } catch (Throwable $e) {
            return self::errorLog($e);
        }
    }
    // 1.2 用戶登入
    public function login(Request $request) {
        // 參數驗證
        $paramValid = self::paramValid($request, [
            'username' => 'bail|required|max:50|string',
            'password' => 'bail|required|max:50|string',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證');
        }

        try {
            $credentials = request(['username', 'password']);
            $userInfo    = User::getUserInfoByAccount($credentials);
            if ( $token  = JWTAuth::fromUser($userInfo) ) {
                $data = [
                    'token' => $token
                ];
                return self::responseSuccess($data, '登入成功！');
            }
            return self::responseFail('帳號或密碼錯誤！');
        } catch (Throwable $e) {
            return self::errorLog($e);
        }
    }
    // 1.3 用戶忘記密碼
    public function resetpw(Request $request) {
        // 參數驗證
        $paramValid = self::paramValid($request, [
            'email' => 'bail|required|max:50|string|email',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證');
        }

        try {
            $credentials = request(['email']);
            if ( User::checkAccountExist($credentials) ) {
                return self::responseSuccess([], '已寄至您的信箱！');
            }
            return self::responseFail('無此註冊信箱！');
        } catch (Throwable $e) {
            return self::errorLog($e);
        }
    }
    // 1.4 用戶資料獲取與修改
    public function profilePost(Request $request) {
        // 參數驗證
        $paramValid = self::paramValid($request, [
            'name'     => 'bail|required|max:50|string',
            'email'    => 'bail|required|max:50|string|email',
            'age'      => 'bail|required|max:3|string',
            'birthday' => 'bail|required|max:15|string',
            'gender'   => 'bail|required|max:1|string',
            'avatar'   => 'bail|required|max:200|string',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證');
        }

        try {
            $userInfo    = Auth::user();
            $credentials = request(['name', 'email', 'age', 'birthday', 'gender', 'avatar']);
            if ( User::updateUserProfileById($userInfo['id'], $credentials) ) {
                return self::responseSuccess([], '修改成功！');
            }
            return self::responseFail('修改失敗：....' . $userInfo);
        } catch (Throwable $e) {
            return self::errorLog($e);
        }
    }
    public function profileGet() {
        try {
            $userInfo = Auth::user();
            if ( $userInfo ) {
                $data = [
                    'username' => $userInfo['users_username'],
                    'email'    => $userInfo['users_email'],
                    'name'     => $userInfo['users_name'],
                    'age'      => $userInfo['users_age'],
                    'birthday' => $userInfo['users_birthday'],
                    'gender'   => $userInfo['users_gender'],
                    'avatar'   => $userInfo['users_avatar'],
                ];
                return self::responseSuccess($data);
            }
            return self::responseFail('無此註冊信箱！');
        } catch (Throwable $e) {
            return self::errorLog($e);
        }
    }
    // 1.5 用戶修改密碼
    public function changepwd(Request $request) {
        // 參數驗證
        $paramValid = self::paramValid($request, [
            'password_old' => 'bail|required|max:50|string',
            'password_new' => 'bail|required|max:50|string',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證');
        }

        try {
            $userInfo    = Auth::user();
            $credentials = request(['password_old', 'password_new']);
            if ( isset($userInfo['id']) ) {
                
                $userInfo = User::getUserInfoBId($userInfo['id']);
                if ( $credentials['password_old'] === $credentials['password_new'] ) {
                    return self::responseFail('新舊密碼相同！');
                }

                if ( $userInfo['users_password'] === $credentials['password_old'] ) {
                    
                    if ( User::updateUserPWDById($userInfo['id'],  $credentials['password_new']) ) {
                        return self::responseSuccess([], '修改成功！');
                    }
                }
            }
            return self::responseFail('舊密碼錯誤！');
        } catch (Throwable $e) {
            return self::errorLog($e);
        }
    }
    // 1.6 用戶第三方登入 Google api
    public function googlelogin(Request $request) {
        return 'googlelogin';
    }
}
