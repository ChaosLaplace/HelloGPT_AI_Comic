<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Tymon\JWTAuth\Facades\JWTAuth;

use hg\apidoc\annotation as Apidoc;

use Throwable;
#[Apidoc\Title("用戶模塊")]
class UserController extends Controller
{
    #[
        Apidoc\Title("用戶註冊"),
        Apidoc\Tag("用戶模塊"),
        Apidoc\Method("POST"),
        Apidoc\Group("bese"),
        Apidoc\Url("api/user/register"),
        Apidoc\Query(name: "name", type: "string", require: true, desc: "姓名"),
        Apidoc\Query(name: "email", type: "string", require: true, desc: "信箱"),
        Apidoc\Query(name: "username", type: "string", require: true, desc: "用戶帳號"),
        Apidoc\Query(name: "password", type: "string", require: true, desc: "用戶密碼"),
        Apidoc\ResponseSuccess(name: "code", type: "int", desc: "成功狀態碼 0", default: 0, require: true),
        Apidoc\ResponseSuccess(name: "state", type: "int", desc: "1 為成功", default: 1, require: true),
        Apidoc\ResponseSuccess(name: "msg", type: "string", desc: "返回消息", require: true),
        Apidoc\ResponseSuccess(name: "data", type: "array", desc: "返回資料", require: true),
        Apidoc\ResponseError(name: "code", type: "int", desc: "失敗狀態碼 -1", default: -1, require: true),
        Apidoc\ResponseError(name: "state", type: "int", desc: "0 為失敗", default: 0, require: true),
        Apidoc\ResponseError(name: "msg", type: "string", desc: "返回消息", require: true),
        Apidoc\ResponseError(name: "data", type: "array", desc: "返回資料", require: true),
    ]
    public function register(Request $request) {
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
                $credentials['password'] = Hash::make($credentials['password']);

                $user = User::create($credentials);
                if ( $user ) {
                    auth()->login($user);
                    return self::responseSuccess([], '註冊成功！');
                }
            }
            return self::responseFail('此帳號已註冊！');
        } catch (Throwable $e) {
            return self::errorLog($e);
        }
    }
    #[
        Apidoc\Title("用戶登入"),
        Apidoc\Tag("用戶模塊"),
        Apidoc\Method("POST"),
        Apidoc\Group("bese"),
        Apidoc\Url("api/user/login"),
        Apidoc\Query(name: "username", type: "string", require: true, desc: "用戶帳號"),
        Apidoc\Query(name: "password", type: "string", require: true, desc: "用戶密碼"),
        Apidoc\ResponseSuccess(name: "code", type: "int", desc: "成功狀態碼 0", default: 0, require: true),
        Apidoc\ResponseSuccess(name: "state", type: "int", desc: "1 為成功", default: 1, require: true),
        Apidoc\ResponseSuccess(name: "msg", type: "string", desc: "返回消息", require: true),
        Apidoc\ResponseSuccess(name: "data", type: "array", desc: "返回資料", require: true),
        Apidoc\ResponseError(name: "code", type: "int", desc: "失敗狀態碼 -1", default: -1, require: true),
        Apidoc\ResponseError(name: "state", type: "int", desc: "0 為失敗", default: 0, require: true),
        Apidoc\ResponseError(name: "msg", type: "string", desc: "返回消息", require: true),
        Apidoc\ResponseError(name: "data", type: "array", desc: "返回資料", require: true),
    ]
    public function login(Request $request) {
        $paramValid = self::paramValid($request, [
            'username' => 'bail|required|max:50|string',
            'password' => 'bail|required|max:50|string',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證');
        }

        try {
            $credentials = request(['username', 'password']);
            $valid       = JWTAuth::attempt($credentials);
            if ( $valid ) {
                $user = Auth::user();
                $data = [
                    'token' => JWTAuth::fromUser($user)
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
                    'username' => $userInfo['name'],
                    'email'    => $userInfo['email'],
                    'name'     => $userInfo['name'],
                    'age'      => $userInfo['age'],
                    'birthday' => $userInfo['birthday'],
                    'gender'   => $userInfo['gender'],
                    'avatar'   => $userInfo['avatar'],
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

                if ( $userInfo['password'] === $credentials['password_old'] ) {
                    
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
