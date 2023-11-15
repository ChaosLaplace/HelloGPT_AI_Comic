<?php
/**
 * 工具
 *
 */
declare(strict_types=1);

namespace App\Http\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\UserPayRecord;

class PaymentECPayService
{
    /**
     * 產生檢查碼
     *
     * @param  array $source
     * @return string
     *
     * @throws RtnException
     */
    public static function generate($data) {
        try {
            $getSign    = self::getSign($data);
            $checkValue = self::urlEncode($getSign);
        } catch (\Exception $e) {
            Log::error( $e->getMessage() );
        }
        return $checkValue;
    }
    /**
     * 檢核檢查碼
     *
     * @param  array $source
     * @return boolean
     */
    public static function verify($data)
    {
        return ($data['test'] = self::generate($data));
    }

    public static function send_post($url, $post_data) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS => http_build_query($post_data),
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));
        $response = curl_exec($curl);
        if ( curl_errno($curl) ) {
            $response = 'Curl error: ' . curl_error($curl);
        }
        curl_close($curl);
        return $response;
    }

    private static function getSign($data) {
        $data = self::naturalSort($data);
        $str  = 'HashKey=' . env('PAYMENT_ECPAY_HASHKEY') . '&';
        foreach ($data as $k => $v) {
            if ( $k === 'CheckMacValue' ) {
                continue;
            }
            $str .= $k . '=' . $v . '&';
        }
        $str .= 'HashIV=' . env('PAYMENT_ECPAY_HASHIV');
        Log::info('[ECPay getSign] ' . $str);
        return $str;
    }

    /**
     * 自然排序
     *
     * @param  array $source
     * @return array
     */
    public static function naturalSort($source)
    {
        uksort($source, function ($first, $second) {
            return strcasecmp($first, $second);
        });
        return $source;
    }

    private static function urlEncode($str) {
        $str = urlencode($str);
        // Log::info('[ECPay urlencode] ' . $str);
        $str = self::toDotNetUrlEncode($str);
        // Log::info('[ECPay str_replace] ' . $str);
        $str = strtolower($str);
        // Log::info('[ECPay strtolower] ' . $str);
        $str = hash('sha256', $str);
        // Log::info('[ECPay Hash] ' . $str);
        $str = strtoupper($str);
        // Log::info('[ECPay strtoupper] ' . $str);
        return $str;
    }
    /**
     * 轉換為 .net URL 編碼結果
     *
     * @param  string $source
     * @return string
     */
    private static function toDotNetUrlEncode($str)
    {
        $search = [
            '%2d',
            '%5f',
            '%2e',
            '%21',
            '%2a',
            '%28',
            '%29',
        ];
        $replace = [
            '-',
            '_',
            '.',
            '!',
            '*',
            '(',
            ')',
        ];
        return str_replace($search, $replace, $str);
    }

    public static function createECPayOrder($data, $userId) {
        try {
            // 寫入用戶成功訂單
            $order_data = [
                'user_order_no'     => $data['MerchantTradeNo'],
                'user_id'           => $userId,
                'user_point'        => $data['TotalAmount'],
                'user_payment'      => $data['TotalAmount'],
                'user_payment_firm' => 'ECPay'
            ];
            if ( UserPayRecord::create($order_data) ) {
                return true;
            }
        } catch(\Exception $e) {
            Log::error('[ECPayQuery PostBefore] ' . $e->getMessage());
        }
        return false;
    }

    public static function updateECPayOrder($data) {
        DB::beginTransaction();
        try {
            if ( $data['TradeStatus'] === 0 ) {
                return 2;
            }

            $order_info = self::checkOrderByOrderId($data['MerchantTradeNo']);            
            if ( $order_info['return_state'] === 1 ) {
                return 1;
            }

            if ( $order_info['return_state'] === 2 ) {
                return 2;
            }
            // 更新下單紀錄
            $where = [
                ['id', $order_info['id']]
            ];
            if ( (int)$data['TradeStatus'] === 1 && $order_info['return_state'] !== 1 ) {
                Order::query()->where($where)->update(['return_state' => 1]);
                // 更新用戶訂單 & 用戶點數
                $user_info = self::getUserPayRecordByOrderId($data['MerchantTradeNo']);
                $where = [
                    ['id', $user_info['id']]
                ];
                UserPayRecord::query()->where($where)->update(['user_payment_status' => 1]);
                DB::commit();
                return 1;
            }

            if ( (int)$data['TradeStatus'] === 10200095 && $order_info['return_state'] !== 2 ) {
                Order::query()->where($where)->update(['return_state' => 2]);
                DB::commit();
            }
        } catch(\Exception $e) {
            Log::error('[updateECPayOrder err] ' . $e->getMessage());
            DB::rollback();
        }
        return 0;
    }

    public static function checkOrderByOrderId($id) {
        return Order::query()->where(function ($query) use ($id){
            $query->where('MerchantTradeNo', $id);
        })->first();
    }

    public static function getUserPayRecordByOrderId($id) {
        return UserPayRecord::query()->where(function ($query) use ($id){
            $query->where('user_order_no', $id);
        })->first();
    }
}
