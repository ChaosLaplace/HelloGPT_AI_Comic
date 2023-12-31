<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Tymon\JWTAuth\Facades\JWTAuth;


use Throwable;
class UserController extends Controller
{
    public function register(Request $request) {
        $paramValid = self::paramValid($request, [
            'name'     => 'bail|required|max:50|string',
            'email'    => 'bail|required|max:50|string|email',
            'username' => 'bail|required|max:50|string',
            'password' => 'bail|required|max:50|string',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證錯誤');
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

    public function login(Request $request) {
        $paramValid = self::paramValid($request, [
            'username' => 'bail|required|max:50|string',
            'password' => 'bail|required|max:50|string',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證錯誤');
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
    // 用戶忘記密碼
    public function resetpw(Request $request) {
        $paramValid = self::paramValid($request, [
            'email' => 'bail|required|max:50|string|email',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證錯誤');
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
    #[
        Apidoc\Title("用戶資料修改"),
        Apidoc\Tag("用戶模塊"),
        Apidoc\Author("Ernest"),
        Apidoc\Method("POST"),
        Apidoc\Group("bese"),
        Apidoc\Url("api/user/profile"),
        Apidoc\Query(name: "name", type: "string", require: true, desc: "姓名"),
        Apidoc\Query(name: "email", type: "string", require: true, desc: "信箱"),
        Apidoc\Query(name: "age", type: "string", require: true, desc: "年齡"),
        Apidoc\Query(name: "birthday", type: "string", require: true, desc: "生日"),
        Apidoc\Query(name: "gender", type: "string", require: true, desc: "性別"),
        Apidoc\Query(name: "avatar", type: "string", require: true, desc: "頭像"),
        Apidoc\ResponseSuccess(name: "code", type: "int", desc: "成功狀態碼 0", default: 0, require: true),
        Apidoc\ResponseSuccess(name: "state", type: "int", desc: "1 為成功", default: 1, require: true),
        Apidoc\ResponseSuccess(name: "msg", type: "string", desc: "返回消息", require: true),
        Apidoc\ResponseSuccess(name: "data", type: "array", desc: "返回資料", require: true),
        Apidoc\ResponseError(name: "code", type: "int", desc: "失敗狀態碼 -1", default: -1, require: true),
        Apidoc\ResponseError(name: "state", type: "int", desc: "0 為失敗", default: 0, require: true),
        Apidoc\ResponseError(name: "msg", type: "string", desc: "返回消息", require: true),
        Apidoc\ResponseError(name: "data", type: "array", desc: "返回資料", require: true),
    ]
    public function saveProfile(Request $request) {
        $paramValid = self::paramValid($request, [
            'name'     => 'bail|required|max:50|string',
            'email'    => 'bail|required|max:50|string|email',
            'age'      => 'bail|required|max:3|string',
            'birthday' => 'bail|required|max:15|string',
            'gender'   => 'bail|required|max:1|string',
            'avatar'   => 'bail|required|max:200|string',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證錯誤');
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
    #[
        Apidoc\Title("用戶資料獲取"),
        Apidoc\Tag("用戶模塊"),
        Apidoc\Author("Ernest"),
        Apidoc\Method("GET"),
        Apidoc\Group("bese"),
        Apidoc\Url("api/user/profile"),
        Apidoc\ResponseSuccess(name: "code", type: "int", desc: "成功狀態碼 0", default: 0, require: true),
        Apidoc\ResponseSuccess(name: "state", type: "int", desc: "1 為成功", default: 1, require: true),
        Apidoc\ResponseSuccess(name: "msg", type: "string", desc: "返回消息", require: true),
        Apidoc\ResponseSuccess(name: "data", type: "array", desc: "返回資料", require: true, childrenType: "array", children: [
            ['name' => 'name', 'type' => 'string', 'require' => true, 'desc' => '姓名'],
            ['name' => 'email', 'type' =>'int', 'require' => true, 'desc' => '信箱'],
            ['name' => 'age', 'type' => 'string', 'require' => true, 'desc' => '年齡'],
            ['name' => 'birthday', 'type' =>'int', 'require' => true, 'desc' => '生日'],
            ['name' => 'gender', 'type' => 'string', 'require' => true, 'desc' => '性別'],
            ['name' => 'avatar', 'type' => 'string', 'require' => true, 'desc' => '頭像'],
        ]),
        Apidoc\ResponseError(name: "code", type: "int", desc: "失敗狀態碼 -1", default: -1, require: true),
        Apidoc\ResponseError(name: "state", type: "int", desc: "0 為失敗", default: 0, require: true),
        Apidoc\ResponseError(name: "msg", type: "string", desc: "返回消息", require: true),
        Apidoc\ResponseError(name: "data", type: "array", desc: "返回資料", require: true),
    ]
    public function getProfile() {
        return self::responseFail('無此註冊信箱！');
        try {
            $userInfo = Auth::user();
            if ( $userInfo ) {
                $data = [
                    'name'     => $userInfo['name'],
                    'email'    => $userInfo['email'],
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
    // 用戶修改密碼
    public function changepwd(Request $request) {
        $paramValid = self::paramValid($request, [
            'password_old' => 'bail|required|max:50|string',
            'password_new' => 'bail|required|max:50|string',
        ]);
        if ( !$paramValid ) {
            return self::responseFail('參數驗證錯誤');
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
    // 用戶第三方登入 Google api
    public function googlelogin(Request $request) {
        return 'googlelogin';
    }
}
