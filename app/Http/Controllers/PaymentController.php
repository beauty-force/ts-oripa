<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Point;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PointHistoryController;
use Illuminate\Support\Facades\Log;
use App\Models\Coupon_record;
use App\Models\Coupon;

use \Exception;
use Str;
use Auth;
use Amazon;

class PaymentController extends Controller
{
    protected $client;
    protected $amount_limit = 5000000;

    public $config = [
        'testOrLive' => 'live',
        'is3DSecure' => '1',
        'fincode_public_key' => '',
        'fincode_secret_key' => '',
        'apiDomain' => '',
    ];
    
    protected function set_config() {
        $this->config['testOrLive'] = getOption('testOrLive');
        $this->config['is3DSecure'] = getOption('is3DSecure');

        if ($this->config['testOrLive'] =="live") {
            $this->config['fincode_public_key'] = env('FINCODE_LIVE_API_KEY');
            $this->config['fincode_secret_key'] = env('FINCODE_LIVE_SECRET_KEY');
            $this->config['apiDomain'] = "https://api.fincode.jp";
        } else {
            $this->config['fincode_public_key'] = env('FINCODE_TEST_API_KEY');
            $this->config['fincode_secret_key'] = env('FINCODE_TEST_SECRET_KEY');
            $this->config['apiDomain'] = "https://api.test.fincode.jp";
        }
    }

    public function do_request($apiPath, $method, $requestParams) {
        $res = [
            'status' => 1,
            'response' => '',
            'httpcode' => '0',
            'error' => '',
        ];

        try{
            $session = curl_init();
            curl_setopt($session, CURLOPT_URL, $this->config['apiDomain'].$apiPath);
            curl_setopt($session, CURLOPT_CUSTOMREQUEST, $method);

            $headers = array(
                "Authorization: Bearer " . $this->config['fincode_secret_key'],
                "Content-Type: application/json"
                );
            curl_setopt($session, CURLOPT_HTTPHEADER, $headers);

            if ($requestParams) {
                $requestParamsJson = json_encode($requestParams);
                curl_setopt($session, CURLOPT_POSTFIELDS, $requestParamsJson);
            }
            

            curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($session);
            $httpcode = curl_getinfo($session, CURLINFO_HTTP_CODE);
            
            curl_close($session);
            $res['response'] = $response;
            $res['httpcode'] = $httpcode;

        } catch(Exception $e) {
            $text = "処理中に問題が発生しました！ " ;
            $res['status'] = 0;
            $res['error'] = $text;
        }

        return $res;
    }

    public function purchase(Request $request) {
        $coupon_id = isset($request->coupon_id) ? intval($request->coupon_id) : 0;
        $current_rate = $this->get_discount_rate($coupon_id, $request->id);
        
        $this->set_config();
        $hide_cat_bar = 1;

        $point = Point::where('id', $request->id)->first();
        if (!$point) {
            return redirect()->route('user.point');
        }

        $user = auth()->user();

        $today_start = now()->startOfDay();
        $today_purchase_amount = Payment::where('user_id', $user->id)
            ->where('created_at', '>=', $today_start)
            ->sum('amount');

        $amount_limit_error = false;
        $amount_limit_error_message = '1日購入上限超過';
        if ($today_purchase_amount + $point->amount > $this->amount_limit) {
            $amount_limit_error = true;
        }
        
        $is_admin = 0;
        if ($user) {
            if ( $user->type==1 ) {
                $is_admin = 1;
            }
        }
        
        $amount = $point->amount - intval($point->amount * $current_rate / 100);

        if ($user->id <= 2) $supported_pay_type = ['Card', 'Konbini', 'Virtualaccount'];
        else $supported_pay_type = ['Card', 'Konbini', 'Virtualaccount'];
        
        return inertia('User/Point/Purchase', compact('coupon_id', 'point', 'is_admin', 'amount', 'hide_cat_bar', 'supported_pay_type', 'amount_limit_error', 'amount_limit_error_message'));
    }

    public function get_discount_rate($coupon_id, $point_id) {
        $record = Coupon_record::where('id', $coupon_id)->where('status', 0)->where('user_id', auth()->user()->id)->first();
        if ($record) {
            $coupon = Coupon::find($record->coupon_id);
            $discount_rate = $coupon?->discount_rate->toArray();
            if ($discount_rate && count($discount_rate) > 0 && $coupon->expiration > now()) {
                foreach($discount_rate as $rate) if ($rate['point_id'] == $point_id) return $rate['rate'];
            }
        }
        return 0;
    }

    public function paymentRegister(Request $request) {
        $coupon_id = isset($request->coupon_id) ? intval($request->coupon_id) : 0;
        $current_rate = $this->get_discount_rate($coupon_id, $request->id);
        
        $this->set_config();
        $hide_cat_bar = 1;

        $points = Point::where('id', $request->id)->get();
        if (!count($points)) {
            return redirect()->route('user.point');
        }
        $point = $points[0];

        $user = auth()->user();
        
        $today_start = now()->startOfDay();
        $today_purchase_amount = Payment::where('user_id', $user->id)
            ->where('created_at', '>=', $today_start)
            ->sum('amount');

        if ($today_purchase_amount + $point->amount > $this->amount_limit) {
            return [
                'status' => 0,
                'message' => '1日の購入金額制限を超えるため、購入することはできません。\n本日の購入金額は'.$today_purchase_amount.'円です。'
            ];
        }

        $is_admin = 0;
        if ($user) {
            if ( $user->type==1 ) {
                $is_admin = 1;
            }
        }
        
        $amount = $point->amount - intval($point->amount * $current_rate / 100);
        
        $pay_type = $request->pay_type;

        $profile = Profile::where('user_id', $user->id)->first();

        if ($pay_type == 'Card' || $pay_type == 'Paypay' || $pay_type == 'Konbini' || $pay_type == 'Virtualaccount') {

            $apiPath = "/v1/sessions";
            
            $expirationDate = now()->addDays(3)->format('Y/m/d H:i:s');
            $requestParams = [
                "success_url" => route('fincode_success'),
                "cancel_url" => route('fincode_cancel'),
                "expire" => $expirationDate,
                "receiver_mail" => auth()->user()->email,
                "mail_customer_name" => $profile ? $profile->first_name.$profile->last_name : auth()->user()->name,
                "guide_mail_send_flag" => "0",
                "thanks_mail_send_flag" => "0",
                // "shop_mail_template_id" => "test template id",
                "transaction" => [
                    "pay_type" => [
                        $pay_type
                    ],
                    "amount" => str($amount),
                    // "tax" => "0",
                    "client_field_1" => str($user->id),
                    "client_field_2" => str($point->id),
                    "client_field_3" => str($coupon_id)
                ],
                "konbini" => [
                    "payment_term_day" => "10",
                    "konbini_reception_mail_send_flag" => "1"
                ],
                "paypay" => [
                    "job_code" => "CAPTURE",
                ],
                "card" => [
                    "job_code" => "CAPTURE",
                    "tds_type" => $this->config['is3DSecure'] == "1" ? "2" : "0"
                ],
                "virtualaccount" => [
                    "payment_term_day" => "10",
                    "virtualaccount_reception_mail_send_flag" => "1"
                ],
            ];
            $res = $this->do_request($apiPath, 'POST', $requestParams);
            
            if ($res['httpcode'] == 200) {
                $res = json_decode($res['response']);
                
                return [
                    'status' => 1,
                    'link_url' => $res->link_url
                ];
            }
            $text = "決済登録エラー！\n";
            $text .= $this->getErrorText($res) ;
            return [
                'status' => 0,
                'message' => $text
            ];
        }
        else if ($pay_type == 'Applepay' || $pay_type == 'Googlepay') {
            $apiPath = "/v1/payments";
            $method = 'POST';
            $requestParams = array(
                "pay_type" => $pay_type,
                "amount" => strval($amount),
                "job_code" => "CAPTURE",
                "client_field_1" => str($user->id),
                "client_field_2" => str($point->id),
                "client_field_3" => str($coupon_id)
            );
            if ($pay_type == 'Googlepay') {
                $requestParams['tds_type'] = "0";
                if ($this->config['is3DSecure']=="1") {
                    $requestParams['tds_type'] = "2";  //   3DS2.0を利用
                    $requestParams['td_tenant_name'] = "トレしるオリパ";  //   3Dセキュア表示店舗名
                    // $requestParams['tds2_type'] = "3";  //   3DS2.0の認証なしでオーソリを実施し、決済処理を行う。
                    $requestParams['tds2_type'] = "2";  //   エラーを返し、決済処理を行わない。
                }
            }

            $res = $this->do_request($apiPath, $method, $requestParams);

            if ($res['httpcode']!='200') {
                $text = "決済登録エラー！\n";
                $text .= $this->getErrorText($res) ;
                return [
                    'status' => 0,
                    'message' => $text
                ];
            }

            $json_data = json_decode($res['response']);
            $order_id = $json_data->id;
            $access_id = $json_data->access_id;

            $json_data->label = "トレしるオリパ";
            $json_data->amount = str($json_data->amount);

            if ($pay_type == 'Googlepay') {
                $json_data->google_pay_merchant_id = env('GOOGLE_PAY_MERCHANT_ID');
                $json_data->google_pay_environment = ['test' => 'TEST', 'live' => 'PRODUCTION'][getOption('testOrLive')];
                $json_data->google_pay_merchant_name = env('GOOGLE_PAY_MERCHANT_NAME');
            }
            return [
                'status' => 1,
                'response' => $json_data
            ];
        }
    }

    protected function getErrorText($data) {
        $error_print = "";
        try{
            $json_data = json_decode($data['response']);
            if ($json_data->errors) {
                foreach($json_data->errors as $item) {
                    $error_print .= "\n";
                    $error_print .= "$item->error_code : ";
                    $error_print .= "$item->error_message";
                }
            }
        } catch(Exception $e) {
            
        }
        return $error_print;
    }

    public function webhook (Request $request) {
        $fincode_signature = env('FINCODE_WEBHOOK_SIGNATURE');
        if ($request->header('fincode-signature') != $fincode_signature) {
            $data = json_encode($request->all());
            setOption('fincode_webhook-'.now().Str::random(10), $data);
            return response()->json(['error' => 'Invalid signature'], 401);
        }
        
        if (isset($request->pay_type) && isset($request->status)) {
            $point = Point::find($request->client_field_2);
            $pt_amount = $point->point;

            $user = User::find($request->client_field_1);

            if ($point && $user) {
                if (str_ends_with($request->event, 'regist')) {
                    Payment::Create([
                        'user_id' => $user->id,
                        'point_id' => $point->id,
                        'access_id' => $request->access_id,
                        'order_id' => $request->order_id,
                        'pay_type' => $request->pay_type,
                        'coupon_id' => $request->client_field_3,
                        'amount' => intval($request->amount),
                    ]);
                }
                else if ($request->status == 'CAPTURED') {
                    $payment = Payment::where('order_id', $request->order_id)->first();
                    if (!$payment) {
                        return response()->json(['receive' => "1"]);
                    }
                    if ($payment->status == 1) {
                        return response()->json(['receive' => "0"]);
                    }
                    
                    $coupon_record = Coupon_record::find($request->client_field_3);
                    if ($point->amount != intval($request->amount)) {
                        if (!$coupon_record || $coupon_record->status != 0) {
                            $pt_amount = $request->amount;
                        }
                        if ($request->pay_type == 'Virtualaccount' && $request->amount != $request->billing_amount) {
                            $pt_amount = $request->amount;
                        }
                    }
                    if ($request->pay_type == 'Virtualaccount') {
                        $payment->update(['amount' => intval($request->amount)]);
                    }
                    if ($payment->status == 1) {
                        return response()->json(['receive' => "0"]);
                    }
                    $payment->update(['status' => 1]);
                    $coupon_record?->update(['status' => 1]);

                    (new PointHistoryController)->create($user->id, $user->point, $pt_amount, 'purchase', $payment->id);
                    
                    $user->increment('point', $pt_amount);
                }
            }

            return response()->json(['receive' => "0"]);
        }
        else {
            return response()->json(['receive' => "1"]);
        }
    }

    public function fincode_success(Request $request) {
        return redirect()->route('purchase_success');
    }

    public function fincode_cancel(Request $request) {
        return redirect()->route('user.point');
    }
}
