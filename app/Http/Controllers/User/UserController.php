<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Plan;
use App\Models\Gacha;
use App\Models\Point;
use App\Models\Favorite;

use App\Models\Product;
use App\Models\Product_log;
use App\Models\Profile;
use App\Models\Gacha_lost_product;

use App\Models\Gacha_record;
use App\Models\Payment;
use App\Models\User;
use App\Models\Option;
use App\Models\Coupon;
use App\Models\Coupon_record;
use App\Models\Rank;

use App\Http\Controllers\PointHistoryController;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\DeliveryProductResource;
use App\Http\Resources\FavoriteListResource;
use App\Http\Resources\PointList;
use App\Http\Resources\GachaListResource;
use Str;
use File;

use \Exception;

use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index() {
        return inertia('User/Index');
    }

    public function dpexchange() {
        return inertia('User/Dpexchange');
    }

    // Gacha code

    public function gacha($id, Request $request) {
        $page_size = 100;
        $gachas = Gacha::where('id', $id)->where('status', 1)->get();
        $user = auth()->user();
        if (!count($gachas) || $gachas[0]->timeStatus() == 2) {
            $text = "ガチャカードは存在しません！";
            $hide_cat_bar = 1;
            return inertia('NoProduct', compact('text', 'hide_cat_bar'));
        }
        $gacha_log = [];
        $total = 0;
        $offset = 0;
        $search_cond = [];
        if (auth()->user() && auth()->user()->type == 1) {

            $gacha_log = Product_log::leftJoin('users', function($join) { $join->on('users.id', '=', 'product_logs.user_id'); })
            ->leftJoin('gacha_records', function($join) { $join->on('gacha_records.id', '=', 'product_logs.gacha_record_id'); });
        
            $gacha_log = $gacha_log->select('product_logs.name', 'product_logs.point', 'product_logs.image', 'product_logs.rare', 'users.email', 'product_logs.status', 'product_logs.created_at', 'product_logs.updated_at', 'gacha_records.created_at as gacha_time', 'product_logs.gacha_record_id')
                ->where('gacha_records.gacha_id', $id)
                ->where('product_logs.status', '!=', 5);
            
            $page = $request->page ? intval($request->page) : 1;
            $total = $gacha_log->count();
            $total = ceil($total / $page_size);
            $offset = ($page - 1) * $page_size;

            if ($total == 0) $total = 1;
            if ($page > $total) $page = $total;
            
            $search_cond = [
                'page' => $page
            ];
            
            $gacha_log = $gacha_log->offset($offset)->limit($page_size)->get();
            $gacha_record = Gacha_record::where('gacha_id', $id)->where('status', 1)->get();
            $gacha_record_sum = [];
            $running_sum = 0;
            foreach($gacha_record as $record) {
                $gacha_record_sum[$record->id] = $running_sum;
                $running_sum += $record->type;
            }
            foreach($gacha_log as $log) {
                $log->gacha_record_id = (++ $gacha_record_sum[$log->gacha_record_id]);
            }
        }
        foreach ($gacha_log as $log) {
            if (auth()->user() && auth()->user()->type == 1) $log->updated_at_time = $log->updated_at->format('Y-m-d H:i:s');
            $log->image = getProductImageUrl($log->image);
        }
        $gacha = $gachas[0]->getDetail();
        $gacha['products'] = Product::select('image', 'rank', 'is_last', 'rare', 'lost_type',
            DB::raw('category_id as marks'))
            ->where('gacha_id', $id)
            ->where('category_id', '>=', 0)
            ->where(function($query) {
                $query->where('rank', '>', 0)
                ->orWhere('is_last', 1);
            })
            ->orderBy('point', 'desc')->get();

        foreach($gacha['products'] as $product) {
            $product->image = getProductImageUrl($product->image);
            if ($product->rare == 'PSA') $product->badge = '/images/psa10.png';
            if ($product->rare == 'BOX' || $product->rare == 'パック') $product->badge = '/images/unopened.png';
        }
        $hide_cat_bar = 1;

        $is_admin = auth()->user() && auth()->user()->type == 1;
        return inertia('User/Gacha', compact('hide_cat_bar', 'gacha', 'gacha_log', 'is_admin', 'total', 'search_cond'));
    }

    public function reward($user, $gacha, $number, $token) {
        $user = User::find($user->id);
        $gacha = Gacha::find($gacha->id);
        $ableCount = $gacha->ableCount();  // Check Gacha Product   #3
        if ($ableCount==0) return 1;

        $point = $user->point - $gacha->point * $number;  // Check User Point   #4
        if ($point<0) return 4;

        $count_rest = $gacha->count_card - $gacha->count;
        $award_products = $gacha->getAward($number, $count_rest, $gacha->count);
        if ($award_products) {} else {
            return 1;  // Check Gacha Product   #3
        }

        foreach($award_products as $key) {
            $product_item = Product::find($key);
            if ($product_item->marks>0 || $product_item->is_last == 1) {
                $data = [
                    'product_id' => $product_item->id,
                    'point' => $product_item->point,
                    'rare' => $product_item->rare,
                    'image' => $product_item->image,
                    'name' => $product_item->name,
                    'gacha_record_id' => $token, 
                    'user_id' => $user->id,
                    'status' => 1,
                    'rank' => $product_item->rank,
                ];
                if ($product_item->lost_type == '1' || $product_item->lost_type == 'true') {
                    $data['status'] = 3;
                }
                Product_log::create($data);
                
                if ($product_item->is_last == 1) continue;
                $product_item->decrement('marks');

                if ($product_item->is_lost_product == 1) {
                    Gacha_lost_product::where('gacha_id', $gacha->id)
                    ->where('point', $product_item->point)
                    ->where('count','>',0)
                    ->first()?->decrement('count');
                }
            }
        
        }
        $gacha->update(['count'=> $gacha->count + $number ]);
        
        Gacha_record::find($token)->update(['status'=>1]);
        
        (new PointHistoryController)->create($user->id, $user->point, -$gacha->point * $number, 'gacha', $token);

        $rank = Rank::where('rank', $user->current_rank)->first();
        $dp_rate = 1;
        if ($rank) $dp_rate = $rank->dp_rate;

        $dp = intval($gacha->point * $number * $dp_rate / 100);
        $point = - $gacha->point * $number;
        $consume_point = $gacha->consume_point * $number;

        $user = computeUserRank($user);
        $user->update([
            'dp' => $user->dp + $dp,
            'point' => $user->point + $point,
            'consume_point' => $user->consume_point + $consume_point
        ]);
        $rank = Rank::where('rank', $user->current_rank)->first();
        $next_rank = Rank::where('rank', '>', $user->current_rank)->orderby('rank')->first();
        if ($next_rank && $next_rank->limit > 0 && $user->consume_point >= $next_rank->limit) {
            $user->current_rank ++;
            $rank = $next_rank;
            (new PointHistoryController)->create($user->id, $user->point, $rank->bonus, 'rank_up', $rank->rank);
            $user->point += $rank->bonus;
            $user->month = date('Y-m');
            $user->save();
        }

        return 0;
    }

    public function start(Request $request) {
        $id = $request->id;
        $number = $request->number;
        $gacha = Gacha::find($id);
        
        $user = auth()->user();
        if (Profile::where('user_id', $user->id)->count() == 0) {
            return redirect()->route('user.address')->with('message', '発送先住所を登録する必要があります。')->with('type', 'notification');
        }
        
        if ($gacha->plan_limit != 0) {
            if ($user->current_plan == 0) {
                return redirect()->route('main')->with('message', 'サブスクリプションの登録が必要です。')->with('type', 'dialog_error');
            }
            if ($gacha->plan_limit > 0 && $user->current_plan != $gacha->plan_limit) {
                $plan = Plan::find($gacha->plan_limit);
                return redirect()->route('main')->with('message', $plan->name.'プランに登録する必要があります。')->with('type', 'dialog_error');
            }
        }
        $userLock = Cache::lock('startGacha'.$user->id, 60);
        if (!$userLock->get()) {
            return redirect()->route('main'); 
        }
        try {
            if (!$gacha || $gacha->count_card == $gacha->count || $gacha->status == 0) {
                return redirect()->route('main');
            }
            if ($gacha->rank_limit > 0 && $gacha->rank_limit != $user->current_rank) {
                return redirect()->route('main'); 
            }
            // if ($gacha->need_line && $user->line_id == null) {
            //     return redirect()->back()->with('message', 'プロフィールページからLINE連携をお願いします。')->with('title', 'LINE連携が必要です。')->with('type', 'dialog');
            // }
            
            $current = date('Y-m-d H:i:s');
            if ($gacha->start_time && $gacha->start_time > $current) {
                $message = '開始時刻までお待ちください。';
                return redirect()->back()->with('message', $message)->with('title', 'ガチャの制限時間')->with('type', 'dialog');
            }
            if ($gacha->end_time && $gacha->end_time != '0000-00-00 00:00:00' && $gacha->end_time <= $current) {
                $message = '完了した。';
                return redirect()->back()->with('message', $message)->with('title', 'ガチャの制限時間')->with('type', 'dialog');
            }

            $show_days = $gacha->lost_product_type ?? '0';
            if ($show_days != '0') {
                if ($user->created_at < date('Y-m-d H:i:s', strtotime('-'.$show_days.' days'))) {
                    $message = 'このガチャは登録後'.$show_days.'日間限定です。';
                    return redirect()->back()->with('message', $message)->with('title', 'ガチャの制限時間')->with('type', 'dialog');
                }
            }
            
            $count_rest = $gacha->count_card - $gacha->count;
            
            if ($gacha->spin_limit == 0) $remainingSpin = $count_rest;
            else {
                $totalSpin = Gacha_record::where('user_id', $user->id)
                    ->where('gacha_id', $id)
                    ->where('status', 1)
                    ->sum('type');
                $remainingSpin = $gacha->spin_limit - $totalSpin;
            }
            
            if ($remainingSpin <= 0) {
                return redirect()->back()->with('message', 'おひとり様当たりの口数制限を超えました。')->with('title', 'ガチャ回数超過!')->with('type', 'dialog');
            }
            if ($number > $count_rest) $number = $count_rest;
            if ($number > $remainingSpin) $number = $remainingSpin;
            
            $status = $gacha->gacha_limit;
            
            if ($status == 1) {
                if ($number > 1) {
                    $message = '1日1回以上ガチャできません。';
                    return redirect()->back()->with('message', $message)->with('title', '1日1回ガチャ制限')->with('type', 'dialog');
                }
                $last = Gacha_record::where('user_id', $user->id)->where('gacha_id', $id)->where('status', 1)->latest()->first();
                if ($last) {
                    $now = $this->get_period_day(date('Y-m-d H:i:s'));
                    $record = $this->get_period_day($last->updated_at);
                    if ($now == $record) {
                        $message = '1日1回以上ガチャできません。';
                        return redirect()->back()->with('message', $message)->with('title', '1日1回ガチャ制限')->with('type', 'dialog');
                    }
                }
            }
            
            $gacha_point = $gacha->point * $number;
            $user_point = $user->point;
            if ($user_point< $gacha_point) {
                $userLock->release();
                return redirect()->route('user.point');
            }

            $lock = Cache::lock('startGacha', 60);
            try {
                $lock->block(10);
                
                $data = [
                    'user_id' => $user->id,
                    'gacha_id' => $gacha->id,
                    'type' => $number,
                ];
                $gacha_record = Gacha_record::create($data);
                
                $token = $gacha_record->id;
                $result = $this->reward($user, $gacha, $number, $token);
                
                $lock?->release();
            
                if ($result == 0) {
                    return redirect()->route('user.gacha.video', ['token' => $token]);
                }
                else {
                    return redirect()->route('user.error', ['id' => $result]);
                }
            } catch (LockTimeoutException $e) {
                return redirect()->route('user.error', ['id' => 3]);
            } 
        }
        finally {
            $userLock?->release();
        }
    }

    public function noProduct(Request $request) {
        $hide_cat_bar = 1;
        $text = "";
        $id = $request->id;

        if ($id==1) {
            $text = "サーバーが混み合っております。少し時間をおいて再度お試しください。";
        }

        if ($id==2) {
            $text = "ガチャ回数を超えました！";
        }

        if ($id==3) {
            $text = "ガチャ時間を超えました！";
        }

        if ($id==4) {
            $text = "ユーザーポイントが足りません。";
        }
        return inertia('NoProduct', compact('text', 'hide_cat_bar'));
    }

    public function video($token) {
        $max_point = Product_log::where('gacha_record_id', $token)->max('point');
        $gacha = Gacha::find(Gacha_record::find($token)?->gacha_id);

        if (!$gacha) {
            return redirect()->route('user.gacha.result', [ 'token' => $token ]);
        }
        $hide_cat_bar = 1;
        $video = getVideo($gacha->id, $gacha->point, $max_point);
        if (!File::exists('videos/'.$video)) {
            $video = "default.mp4";
            if (!File::exists('videos/'.$video)) {
                return redirect()->route('user.gacha.result', [ 'token' => $token ]);
            }
        }
        return inertia('User/Video', compact('hide_cat_bar', 'video', 'token'));
    }

    public function get_period_day($current) {
        $current_day = date('Y-m-d 20:00:00', strtotime($current));
        if ($current < $current_day) $current = date('Y-m-d', strtotime($current.' -1 days'));
        else $current = date('Y-m-d', strtotime($current));
        return $current;
    }

    public function result($token) {
        $user = auth()->user();
        $products = Product_log::leftJoin('products', 'products.id', '=', 'product_logs.product_id')
            ->select('product_logs.*', 'products.rank')->where('product_logs.gacha_record_id', $token)->where('product_logs.user_id', $user->id)->orderBy('status', 'desc')->orderBy('product_logs.point', 'DESC')->get();
        $show_review = false;
        foreach($products as $product) {
            if ($product->rank > 0 && $product->rank <=3) $show_review = true;
        }
        $products = ProductListResource::collection($products);
        $hide_cat_bar = 1;
        $hide_back_btn = 1;
        $show_result_bg = 1;
        return inertia('User/Result', compact('products', 'hide_cat_bar', 'hide_back_btn', 'show_result_bg', 'token', 'show_review'));
    }

    public function result_exchange(Request $request) {
        $token = $request->token;
        $checks = $request->checks;
        $user = auth()->user();
        $userLock = Cache::lock('startGacha'.$user->id, 60);

        if (!$userLock->get()) {
            return redirect()->route('main'); 
        }
        try {
            $logs = Product_log::where('gacha_record_id', $token)->where('user_id', $user->id)->where('status', 1)->get();
    
            $point = $user->point;
            foreach($logs as $log) {
                $key = "id" . $log->id;
                if (isset($checks[$key]) && $checks[$key]) {
                    $log->status = 2;
                    $log->save();
                    if ($product = Product::find($log->product_id)) {
                        if ($product->is_lost_product > 0)
                            $product->increment('marks');
                    }
                    (new PointHistoryController)->create($user->id, $point, $log->point, 'exchange', $log->id);
                    $point = $point + $log->point;
                }
            }
            $user->update(['point'=>$point]);
            return redirect()->route('user.gacha.end', ['token'=>$token]);
        } finally {
            $userLock?->release();
        }
    }

    public function result_deliver(Request $request) {
        $token = $request->token;
        $checks = $request->checks;
        $user = auth()->user();
        
        $delivery_limit = getOption('delivery_limit');
        $delivery_limit = intval($delivery_limit == "" ? "1000" : $delivery_limit);

        $products = Product_log::where('gacha_record_id', $token)->where('user_id', $user->id)->where('status', 1)->get();
        $point = 0;
        foreach($products as $product) {
            $key = "id" . $product->id;
            if (isset($checks[$key]) && $checks[$key]) {
                $point += $product->point;
            }
        }
        if ($point < $delivery_limit) {
            return redirect()->back()->with('message', '発送依頼ができる最低ポイントは'.$delivery_limit.'ptです。')->with('title', '発送依頼エラー')->with('type', 'dialog');
        }

        $count = 0;
        foreach($products as $product) {
            $key = "id" . $product->id;
            if (isset($checks[$key]) && $checks[$key] && $product->rare != 'ポイント') {
                $product->status = 3;
                $count += 1;
                $product->save(); 
            }
        }
        return redirect()->route('user.gacha.end', ['token'=>$token]);
    }

    public function gacha_end(Request $request) {
        $token = $request->token;
        $point = 0; $number_products = 0;
        if ($token) {
            $user = auth()->user();
            $products = Product_log::where('gacha_record_id', $token)->where('status', 2)
                ->select('point')->get();

            foreach($products as $product) {
                $point = ((int)$point) + ((int)$product->point);
                $number_products = $number_products + 1;
            }

            $deliver_products = Product_log::where('gacha_record_id', $token)->where('status', 3)->count();

            $gacha_record = Gacha_record::find($token);
            if ($gacha_record) {
                $gachas = Gacha::where('id', $gacha_record->gacha_id)->get();
                if (!count($gachas)) {
                    return redirect()->route('main');
                }
                $gacha = $gachas[0]->getDetail();
                $hide_cat_bar = 1;
                $hide_back_btn = 1;
                return inertia('User/GachaEnd', compact('point', 'number_products', 'gacha', 'hide_cat_bar', 'hide_back_btn', 'deliver_products'));
            } else {
                return redirect()->route('main');
            }
        }
        return redirect()->route('main');
    }

    // Gacha Code End

    public function point(Request $request) {
        $points = Point::orderBy('amount','ASC')->get();
        $rates = [];
        $coupon_id = 0;
        $user = auth()->user();
        if (isset($request->id)) {
            $coupon_id = intval($request->id);
            $record = Coupon_record::where('id', $request->id)->where('status', 0)->where('user_id', $user->id)->first();
            if ($record) {
                $coupon = Coupon::find($record->coupon_id);
                $discount_rate = $coupon?->discount_rate->toArray();
                if ($discount_rate && count($discount_rate) > 0 && $coupon->expiration > now()) {
                    foreach($discount_rate as $rate) $rates[$rate['point_id']] = $rate['rate'];
                    foreach($points as $point) {
                        $point->amount -= intval($point->amount * $rates[$point->id] / 100);
                        $point->discount_rate = $rates[$point->id];
                    }
                }
            }
        }
        
        $points = PointList::collection($points);
        $rank = Rank::where('rank', $user->current_rank)->first();
        $hide_cat_bar = 1;
        return inertia('User/Point/Index', compact('points', 'hide_cat_bar', 'coupon_id', 'rank'));
    }

    public function purchase_success() {
        $hide_cat_bar = 1;
        $hide_back_btn = 1;
        return inertia('User/Point/Success', compact('hide_cat_bar', 'hide_back_btn'));
    }

    public function favorite() {
        $user = auth()->user();
        $products = Favorite::where('user_id', $user->id)->orderBy('id', 'ASC')->get();
        $products = FavoriteListResource::collection($products);  
        $hide_cat_bar = 1;
        // return $products;
        $hide_back_btn = 1;
        return inertia('User/Favorite', compact('products', 'hide_cat_bar', 'hide_back_btn'));
    }

    public function favorite_add(Request $request) {
        $res = ['status'=>0];
        $id = $request->id;
        $value = $request->value;
        if ($id) {
            $user = auth()->user();
            if ($value) {
                $products = Favorite::where('user_id', $user->id)->where('product_id', $id)->get();
                if (!count($products)) {
                    Favorite::create(['user_id'=>$user->id, 'product_id'=>$id]);
                }
            } else {
                Favorite::where('user_id', $user->id)->where('product_id', $id)->delete();
            }
            $res['status'] = 1;
        }
        return redirect()->back()->with('message', '保存しました！')->with('title', 'お気に入り')->with('type', 'dialog');
    }

    public function address() {
        $hide_cat_bar = 1;
        $user = auth()->user();
        $profiles = Profile::where('user_id', $user->id)->get();
        return inertia('User/Address', compact('hide_cat_bar', 'profiles'));
    }

    public function address_post(Request $request) {
        $validated = $request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'first_name_gana'=>'required',
            'last_name_gana'=>'required',
            'postal_code'=>'required',
            'prefecture'=>'required',
            'address'=>'required',
            'phone' => 'required|numeric|digits:11',
        ]);
        
        $user = auth()->user();

        $profiles = Profile::where('user_id', $user->id)->get();
        if (count($profiles)>0) {
            $profiles[0]->update($validated);
        } else {
            $validated['user_id'] = $user->id;
            Profile::create($validated);
        }
        return redirect()->back()->with('message', '保存しました！')->with('title', '個人情報登録')->with('type', 'dialog');
    }

    public function products() {
        $user = auth()->user();
        $this->auto_product_point_exchange($user);
        $products = Product_log::where('user_id', $user->id)->where('status', 1)->orderBy('point', 'DESC')->get();
        $products = ProductListResource::collection($products); 

        $user = auth()->user();
        $profiles = Profile::where('user_id', $user->id)->get();
        $profile = [];
        if (count($profiles)) {
            $profile = $profiles[0];
        }

        $delivery_limit = getOption('delivery_limit');
        $delivery_limit = intval($delivery_limit == "" ? "1000" : $delivery_limit);

        $hide_cat_bar = 1;
        
        return inertia('User/Product/Index', compact('products', 'hide_cat_bar', 'profile', 'delivery_limit'));
    }

    public function product_point_exchange(Request $request) {
        $checks = $request->checks;
        $user = auth()->user();
        $userLock = Cache::lock('startGacha'.$user->id, 60);
        if (!$userLock->get()) {
            return redirect()->route('user.products'); 
        }
        try {
            $logs = Product_log::where('user_id', $user->id)->where('status', 1)->lockForUpdate()->get();
        
            $point = $user->point;
            foreach($logs as $log) {
                $key = "id" . $log->id;
                if (isset($checks[$key]) && $checks[$key]) {
                    $log->status = 2;
                    $log->save();
                    if ($product = Product::find($log->product_id)) {
                        if ($product->is_lost_product > 0)
                            $product->increment('marks');
                    }
                    (new PointHistoryController)->create($user->id, $point, $log->point, 'exchange', $log->id);
                    $point = $point + $log->point;
                }
            }
    
            $user->update(['point'=>$point]);
    
            return redirect()->back()->with('message', '変換しました！')->with('title', 'ポイント変換')->with('type', 'dialog')->with('data', ['user' => $user]);
        } finally {
            $userLock?->release();
        }
    }

    public function product_delivery_post(Request $request) {
        $user = auth()->user();
        $checks = $request->checks;

        $delivery_limit = getOption('delivery_limit');
        $delivery_limit = intval($delivery_limit == "" ? "1000" : $delivery_limit);

        $products = Product_log::where('user_id', $user->id)->where('status', 1)->get();
        $point = 0;
        foreach($products as $product) {
            $key = "id" . $product->id;
            if (isset($checks[$key]) && $checks[$key]) {
                $point += $product->point;
            }
        }
        if ($point < $delivery_limit) {
            return redirect()->back()->with('message', '発送依頼ができる最低ポイントは'.$delivery_limit.'ptです。')->with('title', '発送依頼エラー')->with('type', 'dialog');
        }

        $count = 0;
        foreach($products as $product) {
            $key = "id" . $product->id;
            if (isset($checks[$key]) && $checks[$key] && $product->rare != 'ポイント') {
                $product->status = 3;
                $count += 1;
                $product->save(); 
            }
        }
        
        if($count == 0) redirect()->back();

        return redirect()->back();
    }

    public function delivery_wait(Request $request) {
        $user = auth()->user();
        $this->auto_product_point_exchange($user);
        $products = Product_log::where('user_id', $user->id)->where('status', 3)->orderBy('point', 'DESC')->get();
        $products = ProductListResource::collection($products); 
        $hide_cat_bar = 1;
        return inertia('User/Product/Wait', compact('products', 'hide_cat_bar'));
    }


    public function delivered(Request $request) {
        $user = auth()->user();
        $this->auto_product_point_exchange($user);
        $products = Product_log::where('user_id', $user->id)->where('status', 4)->orderBy('point', 'DESC')->get();
        $products = DeliveryProductResource::collection($products);
        $hide_cat_bar = 1;
        return inertia('User/Product/Delivered', compact('products', 'hide_cat_bar'));
    }

    public function auto_product_point_exchange($user) {
        $logs = Product_log::where('user_id', $user->id)->where('status', 1)->whereRaw('updated_at < NOW() - INTERVAL 7 DAY')->get();
  
        $point = $user->point;
        foreach($logs as $log) {
            $log->status = 2;
            $log->save();
            if ($product = Product::find($log->product_id)) {
                if ($product->is_lost_product > 0)
                    $product->increment('marks');
            }
            (new PointHistoryController)->create($user->id, $point, $log->point, 'exchange', $log->id);
            $point = $point + $log->point;
        }
        $user->update(['point'=>$point]);
    }

    public function dp_detail($id) {
        $user = auth()->user();
        $products = Product::where('id', $id)->where('is_lost_product', 2)->get();
        if (!count($products)) {
            return redirect()->route('main.dp'); 
        }
        $product = $products[0];
        $favorite = Favorite::where('user_id', $user->id)->where('product_id', $product->id)->count();

        $products = ProductListResource::collection($products); 
        $productStatusTxt = getProductStatusTxt();
        
        $profiles = Profile::where('user_id', $user->id)->get();
        $profile = [];
        if (count($profiles)) {
            $profile = $profiles[0];
        }
        
        $hide_cat_bar = 1;
        return inertia('User/Dp/Detail', compact('products', 'favorite', 'hide_cat_bar', 'productStatusTxt', 'profile'));
    }

    public function dp_detail_post(Request $request) {
        $id = $request->id;
        if (!$id) {
            return redirect()->route('main.dp');
        }
        $products = Product::where('id', $id)->where('is_lost_product', 2)->get();
        if (!count($products) || $products[0]->marks <= 0) {
            return redirect()->route('main.dp');
        }

        $user = auth()->user();
        if ($user->dp<$products[0]->dp) {
            return redirect()->back()->with('message', 'TPが足りてないです！')->with('title', 'TP交換所 – 詳細')->with('type', 'dialog');
        }

        // $product = Product::where('id', $id)->where('is_lost_product', 2)->update(['status'=>1, 'user_id'=>$user->id]);
        $product = Product::find($id);
        $product->update(['marks' => $product->marks - 1]);
        $data = [
            'product_id' => $id,
            'name' => $product->name,
            'point' => $product->point,
            'image' => $product->image,
            'rare' => $product->rare,
            'status' => 3,
            'user_id'=>$user->id,
            'gacha_record_id' => 0,
            'rank' => $product->rank,
        ];
        Product_log::create($data);
            
        $dp = $user->dp - $product->dp;
        $user->update(['dp'=>$dp]);
        return redirect()->route('user.dp.detail.success');
    }

    public function dp_detail_success(Request $request) {
        $hide_cat_bar = 1;$hide_back_btn = 1;
        return inertia('User/Dp/Success', compact('hide_cat_bar', 'hide_back_btn'));
    }

    public function coupon() {
        $user = auth()->user();
        $hide_cat_bar = 1;
        $coupons = DB::table('coupon_records')->leftJoin('coupons', 'coupons.id', '=', 'coupon_records.coupon_id')
            ->select('coupon_records.id as record_id', 'coupons.title', 'coupons.id', 'coupons.type', 'coupons.point', 'coupons.expiration', 'coupon_records.updated_at', 'coupon_records.status')
            ->where('coupon_records.user_id', $user->id)
            ->orderBy('coupon_records.updated_at', 'desc')->get();
        $types = [
            'NORMAL' => '普通',
            'DISCOUNT' => '割引'
        ];
        foreach($coupons as $coupon) {
            $coupon->acquired_time = date('Y-m-d H:i:s', strtotime($coupon->updated_at));
            if ($coupon->status == 0 && $coupon->expiration <= now()) $coupon->status = 2;
            if ($coupon->type == 'DISCOUNT') {
                $rates = Coupon::find($coupon->id)->discount_rate->toArray();
                $coupon->min_rate = min(array_column($rates, 'rate'));
                $coupon->max_rate = max(array_column($rates, 'rate'));
            }
        }
        return inertia('User/Coupon', compact('hide_cat_bar', 'coupons', 'types'));
    }

    public function coupon_post(Request $request) {
        $user = auth()->user();
        $request->validate([
            'code' => 'required'
        ]);
        $coupon = Coupon::where('code', $request->code)->first();
        if ($coupon) {
            if ($coupon->expiration <= date('Y-m-d H:i:s')) {
                return redirect()->back()->with('message', '有効期間を超えました。')->with('title', '取得エラー')->with('type', 'dialog');
            }
            // if ($coupon->need_line && $user->line_id == null) {
            //     return redirect()->back()->with('message', 'プロフィールページからLINE連携をお願いします。')->with('title', 'LINE連携が必要です。')->with('type', 'dialog');
            // }
            $count = Coupon_record::where(['coupon_id' => $coupon->id, 'user_id' => $user->id])->count();
            if ($count >= $coupon->user_limit) {
                return redirect()->back()->with('message', 'すでにこのコードを制限回数分使用しました。')->with('title', '取得エラー')->with('type', 'dialog');
            }
            $records = Coupon_record::where(['coupon_id' => $coupon->id])->count();
            if ($records == $coupon->count) {
                return redirect()->back()->with('message', '使用可能な人数を超えました。')->with('title', '取得エラー')->with('type', 'dialog');
            }
            $coupon_record = Coupon_record::create([
                'coupon_id' => $coupon->id,
                'user_id' => $user->id,
                'status' => $coupon->type == 'NORMAL' ? 1 : 0
            ]);
            if ($coupon->type == 'NORMAL') {
                (new PointHistoryController)->create($user->id, $user->point, $coupon->point, 'coupon', $coupon_record->id);
                $user->update(['point' => $user->point + $coupon->point]);
            }
            $coupon->acquired_time = date('Y年m月d日 H時i分', strtotime($coupon->updated_at));
            return redirect()->back()->with('message', '取得に成功しました。')->with('title', '取得成功')->with('type', 'dialog')->with('data', ['coupon' => $coupon, 'user' => $user]);
        }
        else {
            return redirect()->back()->with('message', '有効なコードを入力してください。')->with('title', '取得エラー')->with('type', 'dialog');
        }
    }

    public function profile() {
        $ranks = Rank::select('rank', 'pt_rate', 'bonus', 'dp_rate', 'image', 'badge', 'title')->orderby('rank', 'desc')->get();
        $user = auth()->user();
        if (!$user) return redirect()->route('main');
        foreach ($ranks as $rank) {
            $rank->image = getRankImageUrl($rank->image);
            $rank->badge = getRankImageUrl($rank->badge);
        }
        $hide_cat_bar = 1;
        $current_rank = Rank::where('rank', $user->current_rank)->first();
        $next_rank = Rank::where('rank', '>', $user->current_rank)->orderby('rank')->first();
        $limit = $current_rank->limit / 2;
        if (!$next_rank || $next_rank->limit < 0) {
            $next_rank = $current_rank;
            $next_rank->limit *= 2;
        }
        $succeed = $user->consume_point >= $limit;
        $target = $next_rank->limit * $next_rank->limit / (2 * $next_rank->limit - $user->consume_point);
        $mark_pos = $limit / ($next_rank->limit * $next_rank->limit / (2 * $next_rank->limit - $limit)) * 100;
        $current_pos = $user->consume_point / $target * 100;
        return inertia('User/Profile', compact('hide_cat_bar', 'ranks', 'mark_pos', 'current_pos', 'succeed'));
    }

    public function userRankCronjob() {
        $users = User::get();
        foreach ($users as $user) {
            computeUserRank($user);
        }
    }
}
