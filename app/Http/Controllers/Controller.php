<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

use Throwable;
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected static function responseSuccess (Array $data = [], String $msg = 'Success', Bool $state = True, Int $code = 0) {
        return [
            'data'  => $data,
            'msg'   => $msg,
            'state' => $state,
            'code'  => $code
        ];
    }

    protected static function responseFail (String $msg = '網路錯誤，請稍後再試！', Array $data = [], Bool $state = False, Int $code = -1) {
        return [
            'msg'   => $msg,
            'data'  => $data,
            'state' => $state,
            'code'  => $code
        ];
    }

    protected static function errorLog (Throwable $e) {
        $errorData = [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine()
        ];
        Log::error( json_encode($errorData, JSON_UNESCAPED_UNICODE) );
        return self::responseFail();
    }
}
