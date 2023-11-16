<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// 金流模塊
class PaymentController extends Controller
{
    // 金流儲值
    public function index() {
        return 'index';
    }
    // 金流提現
    public function withdraw() {
        return 'withdraw';
    }
    // 金流成交紀錄(付款收款)
    public function record() {
        return 'record';
    }
}
