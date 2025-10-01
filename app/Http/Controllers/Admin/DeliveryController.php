<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Product;
use App\Models\Product_log;
use App\Http\Controllers\PointHistoryController;

use App\Http\Resources\ProductListResource;
use App\Http\Resources\DeliveryProductResource;

use Str;
use Carbon\Carbon;
use Mail;
use DB;

class DeliveryController extends Controller
{
    public function __construct() {
        $this->page_size = 30;
    }

    public function admin (Request $request) {
        
        $name = $request->name ? $request->name : "";

        $products = Product_log::select(
            'product_logs.id as id',
            'product_logs.name as name',
            'product_logs.point as point',
            'product_logs.image as image',
            'product_logs.updated_at as updated_at',
            'product_logs.status as status',
            'product_logs.rare as rare',
            'product_logs.user_id as user_id',
            'profiles.address as address',
            DB::raw('concat(profiles.first_name, profiles.last_name) as user_name'),
            'users.email as email'
        )->leftJoin('profiles', function($join) { $join->on('product_logs.user_id', '=', 'profiles.user_id'); })
        ->leftJoin('users', function($join) { $join->on('product_logs.user_id', '=', 'users.id'); })
        ->where('product_logs.status', 3)
        ->where(function($query) use ($name) {
            $query->where(DB::raw('concat(profiles.first_name, profiles.last_name)'), 'like', '%'.$name.'%')
            ->orWhere('users.email', 'like', '%'.$name.'%')
            ->orWhere('profiles.phone', 'like', '%'.$name.'%')
            ->orWhere('product_logs.name', 'like', '%'.$name.'%');
        })
        ->orderBy('product_logs.user_id')
        ->orderBy('updated_at', 'ASC')->get();

        $products = DeliveryProductResource::collection($products);
        $hide_cat_bar = 1;
        $search_cond = [
            "name" => $name,
        ];
        return inertia('Admin/Delivery/Index', compact('hide_cat_bar', 'products', 'search_cond')) ; 
    }

    public function acquired (Request $request) {
        $logs = Product_log::where('status', 1)->whereRaw('updated_at < NOW() - INTERVAL 7 DAY')->get();
  
        foreach($logs as $log) {
            $log->status = 2;
            $log->save();
            if ($product = Product::find($log->product_id)) {
                if ($product->is_lost_product > 0)
                    $product->increment('marks');
            }
            $user = User::find($log->user_id);
            if ($user) (new PointHistoryController)->create($user->id, $user->point, $log->point, 'exchange', $log->id);
            $user?->increment('point', $log->point);
        }

        $name = $request->name ? $request->name : "";

        $products = Product_log::select(
            'product_logs.id as id',
            'product_logs.name as name',
            'product_logs.point as point',
            'product_logs.image as image',
            'product_logs.updated_at as updated_at',
            'product_logs.status as status',
            'product_logs.rare as rare',
            'product_logs.user_id as user_id',
            'profiles.address as address',
            DB::raw('concat(profiles.first_name, profiles.last_name) as user_name'),
            'users.email as email'
        )->leftJoin('profiles', function($join) { $join->on('product_logs.user_id', '=', 'profiles.user_id'); })
        ->leftJoin('users', function($join) { $join->on('product_logs.user_id', '=', 'users.id'); })
        ->where('product_logs.status', 1)
        ->where(function($query) use ($name) {
            $query->where(DB::raw('concat(profiles.first_name, profiles.last_name)'), 'like', '%'.$name.'%')
            ->orWhere('users.email', 'like', '%'.$name.'%')
            ->orWhere('profiles.phone', 'like', '%'.$name.'%')
            ->orWhere('product_logs.name', 'like', '%'.$name.'%');
        })
        ->orderBy('product_logs.user_id')
        ->orderBy('updated_at', 'ASC')->get();

        $products = DeliveryProductResource::collection($products);
        $hide_cat_bar = 1;
        $search_cond = [
            "name" => $name,
        ];
        return inertia('Admin/Delivery/Acquired', compact('hide_cat_bar', 'products', 'search_cond')) ; 
    }

    public function getProductData(Request $request) {
        if (isset($request->user_id)) {
            $products = Product_log::where('user_id', $request->user_id)->where('status', 3)->get();
            $res = ['status' =>1 ];
            if(count($products) > 0) {
                $user = $products[0]->user;
                $profile = $products[0]->profile;
                $res['user'] = $user;
                $res['profile'] = $profile;
                $res['products'] = DeliveryProductResource::collection($products);
            } else {
                $res = ['status' => 0];
            }
            return $res;
        }
        $id = $request->id;
        $product = Product_log::find($id);
        $res = ['status' =>1 ];
        if($product) {
            $user = $product->user;
            $profile = $product->profile;
            $res['user'] = $user;
            $res['profile'] = $profile;
        } else {
            $res = ['status' =>0 ];
        }
        return $res;
    }

    public function deliver_post(Request $request) {
        $user_id = $request->user_id;
        $checks = $request->checks;
        $products = Product_log::where('user_id', $user_id)->where('status', 3)->get();

        $product_details = "";

        $count = 0;
        foreach($products as $product) {
            $key = "id" . $product->id;
            if (isset($checks[$key]) && $checks[$key]) {
                $product->status = 4;
                $product->tracking_number = $request->tracking_number;
                $count += 1;
                $product->save();
                $product_details .= "・" . $product->name . " (" . $product->rare . ")<br/>";
            }
        }

        if ($count > 0) {
            $email = User::find($user_id)->email;

            $content = "<p>この度は「トレしるオリパ」をご利用いただき、誠にありがとうございます。<br/>
お客様より発送依頼をいただきました商品を、本日発送いたしました。<br/><br/>
{$product_details}<br/>
追跡番号: {$request->tracking_number}<br/>
今後とも「トレしるオリパ」をよろしくお願いいたします。
</p>";
            Mail::send([], [], function ($message) use ($email, $content)
            {
                $message->to($email)
                    ->subject('トレしるオリパ 発送完了のお知らせ')
                    ->from(env('MAIL_FROM_ADDRESS'), 'トレしるオリパ')
                    ->html($content);
            });
        }

        return redirect()->back()->with('message', '発送済みにしました！')->with('title', '発送')->with('type', 'dialog');
    }

    public function completed (Request $request) {
        $page_size = $this->page_size;
        $page = 1;
        if (isset($request->page)) $page = intval($request->page);
        
        $name = $request->name ? $request->name : "";

        $products = Product_log::select(
            'product_logs.id as id',
            'product_logs.name as name',
            'product_logs.point as point',
            'product_logs.image as image',
            'product_logs.updated_at as updated_at',
            'product_logs.status as status',
            'product_logs.rare as rare',
            'product_logs.user_id as user_id',
            'product_logs.tracking_number as tracking_number',
            'profiles.address as address',
        )->leftJoin('profiles', function($join) { $join->on('product_logs.user_id', '=', 'profiles.user_id'); })
        ->leftJoin('users', function($join) { $join->on('product_logs.user_id', '=', 'users.id'); })
        ->where('product_logs.status', 4);

        $totalProducts = $products->count();
        $totalPoints = $products->sum('product_logs.point');

        $products = $products
        ->where(function($query) use ($name) {
            $query->where(DB::raw('concat(profiles.first_name, profiles.last_name)'), 'like', '%'.$name.'%')
            ->orWhere('users.email', 'like', '%'.$name.'%')
            ->orWhere('profiles.phone', 'like', '%'.$name.'%')
            ->orWhere('product_logs.name', 'like', '%'.$name.'%');
        });
        
        $total = ceil($totalProducts / $page_size);
        
        $products = $products->orderBy('updated_at', 'DESC')
        ->offset(($page-1)*$page_size)
        ->limit($page_size)->get();

        $products = DeliveryProductResource::collection($products);
        $hide_cat_bar = 1;
        
        $search_cond = [
            "name" => $name,
            "page" => $page,
        ];

        return inertia('Admin/Delivery/Completed', compact('hide_cat_bar', 'products', 'search_cond', 'total', 'totalProducts', 'totalPoints'));
    }

    public function unDeliver_post(Request $request) {
        $id = $request->id;
        $product = Product_log::find($id);
        $product->tracking_number = null;
        $product->status = 3;   // into waiting status
        $product->save();
        return redirect()->back()->with('message', '未発送にしました！')->with('title', '発送')->with('type', 'dialog');
    }


    public function csv_delivery(Request $request) {
        $hide_cat_bar = 1;
        return inertia('Admin/Delivery/CSV', compact('hide_cat_bar')) ; 
    }

    public function csv_delivery_post(Request $request) {
        $rules = [
            'email' => 'required|email',
        ];
        $validatored = $request->validate($rules);

        $email = $validatored['email'];
        
        $products = Product_log::select(
            'product_logs.updated_at as updated_at',
            'product_logs.user_id as user_id',
            'product_logs.name as name',
            'product_logs.rare as rare',
            'product_logs.point as point',
            'product_logs.image as image',
            
            'profiles.first_name as first_name',
            'profiles.last_name as last_name',
            'profiles.first_name_gana as first_name_gana',
            'profiles.last_name_gana as last_name_gana',
            'profiles.postal_code as postal_code',
            'profiles.prefecture as prefecture',
            'profiles.address as address',
            'profiles.phone as phone',
        )->leftJoin('profiles', function($join) { $join->on('product_logs.user_id', '=', 'profiles.user_id'); })
        ->where('product_logs.status', 3)
        ->orderBy('updated_at', 'ASC')->get();

        $columnNames = [
            '更新日時',
            'ユーザーID',
            '商品名',
            'レアリティ',
            '商品画像URL',
            'PT',
            '名前',
            '名前(カナ)',
            '郵便番号',
            '住所',
            '電話番号',
        ];
        
        $outputs = '';
        foreach ($columnNames as $item) {
            $outputs .= $item . ',';
        }
        $outputs = rtrim($outputs, ',') . "\n";

        
        foreach ($products as $item) {
            $update_at = $item->updated_at->format('Y年m月d日 H時i分');
            $arrInfo = [
                $update_at,
                $item->user_id,
                $item->name,
                $item->rare,
                'https://トレしるオリパ.com' . getProductImageUrl($item->image),
                $item->point,
                $item->first_name . ' '. $item->last_name,
                $item->first_name_gana . ' '. $item->last_name_gana,
                
                "〒" . $item->postal_code,
                $item->prefecture .' '.$item->address,
                " ". $item->phone,
            ];
            foreach ($arrInfo as $item) {
                $outputs .= $item . ',';
            }
            $outputs = rtrim($outputs, ',') . "\n";
        }
        $txt2 = pack('C*',0xEF,0xBB,0xBF). $outputs;
        $fileName = date('Y_m_d') .'_'. uniqid() . '.csv';
        $save_path = 'delivery_csv/' . $fileName;
        file_put_contents($save_path, $txt2);

        
        $info = array(
            'name' => "トレしるオリパ"
        );
        Mail::send('delivery_list', $info, function ($message) use ($save_path, $email)
        {
            $message->to($email)
                ->subject('発送依頼一覧');
            $message->attach(getcwd(). "/" . $save_path);
            $message->from(env('MAIL_FROM_ADDRESS'), 'トレしるオリパ');
        });

        return redirect()->back()->with('message', '送信しました！')->with('title', '発送依頼一覧')->with('type', 'dialog');
    }

}
