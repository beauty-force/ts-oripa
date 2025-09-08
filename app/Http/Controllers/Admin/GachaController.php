<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Plan;
use App\Models\Gacha;
use App\Models\Product;
use App\Models\Rank;
use App\Models\Gacha_lost_product;
use App\Models\Gacha_video;

use App\Http\Resources\GachaListResource;
use App\Http\Resources\ProductListResource;
use App\Http\Controllers\User\UserController;

use Str;
use File;

class GachaController extends Controller
{
    public function index(Request $request) {
        $cat_id = 1;
        if ($request->cat_id) {
            $cat_id = $request->cat_id;
        }
        $GachaObj = Gacha::where('category_id', $cat_id)->where('status', '!=', 2);
        if (auth()->user()->getType()=="staff") {
            $GachaObj->where('status', 0);
        }
        $gachas = $GachaObj->orderBy('order_level', 'DESC')->orderBy('id', 'DESC')->get();

        $gachas = GachaListResource::collection($gachas);
        return inertia('Admin/Gacha/Index', compact('gachas'));
    }

    public function store(Request $request) {
        $validatored = $request->validate([
            'point' => 'required',
            'count_card' => 'required|numeric',
            'thumbnail' => 'required|image|max:4096',
            'category_id' => 'required',
        ]);

        if ($request->image) {
            $image = saveImage('images/gacha', $request->file('image'), false);
        }
        else $image = '';
        $thumbnail = saveImage('images/gacha/thumbnail', $request->file('thumbnail'), false);
        $data = [
            'point' => $request->point,
            'consume_point' => $request->point,
            'count_card' => $request->count_card,
            'lost_product_type' => "0",
            'thumbnail' => $thumbnail,
            'image' => $image,
            'category_id' => $request->category_id,
            'spin_limit' => 0,
            // 'title' => $request->title,
        ];
        $gacha = Gacha::create($data);

        return redirect(combineRoute(route('admin.gacha.edit', $gacha->id), $request->category_id) ); 
    }

    public function create() {
        return inertia('Admin/Gacha/Create');
    }

    public function copy(Request $request) {
        $id = $request->id;
        $gacha = Gacha::find($id);
        $data = [
            'title' => $gacha->title,
            'point' => $gacha->point,
            'consume_point' => $gacha->point,
            'count_card' => $gacha->count_card,
            'lost_product_type' => $gacha->lost_product_type,
            'thumbnail' => $gacha->thumbnail,
            'image' => $gacha->image,
            'category_id' => $gacha->category_id,
            'spin_limit' => $gacha->spin_limit,
        ];
        $new_gacha = Gacha::create($data);
        $videos = Gacha_video::where('gacha_id', $id)->get();
        foreach ($videos as $video) {
            Gacha_video::create([
                'level' => $video['level'],
                'gacha_id' => $new_gacha->id,
                'point' => $video['point'],
                'file' => $video['file']
            ]);
        }
        
        $cards = Gacha_lost_product::select('point', 'count')->where('gacha_id', $id)->get();
        foreach ($cards as $card) {
            $data = ['gacha_id'=>$new_gacha->id, 'point'=>$card->point, 'count'=>$card->count];
            Gacha_lost_product::create($data);
        }
        
        $products = Product::where('is_lost_product', 0)->where('gacha_id', $id)->get();
        foreach ($products as $product) {
            $data = [
                'name' => $product->name,
                'point' => $product->point,
                'rare' => $product->rare,
                'gacha_id' =>$new_gacha->id,
                'marks' => $product->marks,
                'image' => $product->image,
                'is_last' => 0,
                'rank' => $product->rank,
                'order' => $product->order,
            ];
            Product::create($data);
        }
        return redirect(combineRoute(route('admin.gacha.edit', $new_gacha->id), $request->category_id) ); 
    }

    public function edit($id) {
        $gacha = Gacha::find($id);
        if (auth()->user()->getType()=="staff" && $gacha->status!=0) {
            $text = "権限がありません！";
            return inertia('NoProduct', compact('text'));
        }

        $gacha->image = getGachaImageUrl($gacha->image);
        $gacha->thumbnail = getGachaThumbnailUrl($gacha->thumbnail);
        $product_last = $gacha->getProductLast();

        $products = $gacha->getProducts();
        $products = ProductListResource::collection($products);
        $productsLostSetting = $gacha->productsLostSetting;

        $ranks = Rank::orderby('rank')->get();
        $videos = Gacha_video::where('gacha_id', $id)->orderBy('point')->get();

        $lost_types = [
            'PSA',
            'シングル',
            'BOX',
            'パック',
            'ポイント',
        ];
        $plans = Plan::orderBy('amount', 'ASC')->get();
        $video_names = Gacha_video::where('gacha_id', 0)->pluck('level')->toArray();
        
        $points = Gacha_lost_product::where('count', '>', 0)->orderBy('point')->select('point')->get();
        $point = 0; $products_status = [];
        foreach($points as $point1) {
            if ($point1->point!=$point) {
                $point = $point1->point;
                $gacha_products_count = Gacha_lost_product::leftJoin('gachas', function($join) { $join->on('gachas.id', '=', 'gacha_lost_products.gacha_id'); })
                ->where('gacha_lost_products.point', $point)
                ->where('gachas.category_id', $gacha->category_id)
                ->where('gachas.id', '!=', $id)
                ->sum('gacha_lost_products.count');
                $products_lost_count = Product::where('point', $point)->where('is_lost_product', 1)->where('category_id', $gacha->category_id)->where('marks', '>', 0)->sum('marks');
                $arr['point'] = $point;
                $arr['count'] = $products_lost_count - $gacha_products_count;
                array_push( $products_status , $arr);
            }  
        }

        return inertia('Admin/Gacha/Edit', compact('gacha', 'product_last', 'products', 'productsLostSetting', 'videos', 'ranks', 'lost_types', 'video_names', 'plans', 'products_status'));
    }

    public function update(Request $request) { 
        $rules = [
            'point' => 'required',
            'count_card' => 'required|numeric',
            'thumbnail' => 'required|image|max:4096',
            'image' => 'required|image',
            'category_id' => 'required',
            'spin_limit' => 'required|numeric',
            'rank_limit' => 'required|numeric',
            'plan_limit' => 'required|numeric',
            // 'need_line' => 'required',
        ];
        
        if (!$request->thumbnail) {
            $rules['thumbnail'] = '';
        } 
        if (!$request->image) {
            $rules['image'] = ''; 
        }
        $validatored = $request->validate($rules);

        $data = [
            // 'title' => $request->title,
            'point' => $request->point,
            'consume_point' => $request->point,
            'count_card' => $request->count_card,
            'lost_product_type' => $request->lost_product_type,
            'category_id' => $request->category_id,
            'spin_limit' => $request->spin_limit,
            'rank_limit' => $request->rank_limit,
            'plan_limit' => $request->plan_limit,
            // 'need_line' => $request->need_line,
        ];

        if ($request->start_time) 
        $data['start_time'] = date('Y-m-d H:i', strtotime($request->start_time));
        else $data['start_time'] = null;
        
        if ($request->end_time)
        $data['end_time'] = date('Y-m-d H:i', strtotime($request->end_time));
        else $data['end_time'] = null;
    
        if ($request->thumbnail) {
            $thumbnail = saveImage('images/gacha/thumbnail', $request->file('thumbnail'), false);
            $data['thumbnail'] = $thumbnail;
        }
        if ($request->image) {
            $image = saveImage('images/gacha', $request->file('image'), false);
            $data['image'] = $image;
        }
        $gacha = Gacha::find($request->id);

        if (auth()->user()->getType()=="staff" && $gacha->status!=0) {
            $text = "権限がありません！";
            return inertia('NoProduct', compact('text'));
        }

        $gacha->update($data);

        Gacha_video::where('gacha_id', $gacha->id)->delete();
        if (isset($request->videos)) foreach($request->videos as $video) {
            Gacha_video::create([
                'level' => $video['level'],
                'gacha_id' => $gacha->id,
                'point' => $video['point'],
                'file' => null
            ]);
        }
        
        // lost products
        Gacha_lost_product::where('gacha_id', $gacha->id)->delete();
        if($request->lostProducts) {
            foreach($request->lostProducts as $item) {
                if ($item['key']) {
                    $point = 0;
                    if ($item['point']) { $point = $item['point']; }
                    $count = 0;
                    if ($item['count']) { $count = $item['count']; };
                    $data = ['gacha_id'=>$gacha->id, 'point'=>$point, 'count'=>$count];
                    Gacha_lost_product::create($data);
                }
            }
        }
        // lost products end

        return redirect()->back()->with('message', '保存しました！')->with('title', 'ガチャ 編集')->with('type', 'dialog');
    }

    public function sorting(Request $request) {
        $cat_id = 1;
        if ($request->cat_id) {
            $cat_id = $request->cat_id;
        }
        $GachaObj = Gacha::where('category_id', $cat_id);
        if (auth()->user()->getType()=="staff") {
            $GachaObj->where('status', 0);
        }
        $gachas = $GachaObj->orderBy('order_level', 'DESC')->orderBy('id', 'DESC')->get();
        $shuffle_mode = getOption('shuffle_mode') == '1';

        $gachas = GachaListResource::collection($gachas);
        return inertia('Admin/Gacha/Sorting', compact('gachas', 'shuffle_mode'));
    }

    public function sorting_store(Request $request) {
        $data = $request->all();
        $order_level = 1;
        $data['gachas'] = array_reverse($data['gachas']);
        foreach($data['gachas'] as $key=>$item) {
            Gacha::where('id', $item['id'])->update([
                'order_level'=>$order_level + ($item['fixed'] ? 1000000 : 0)
            ]);
            $order_level += 1;
        }
        return redirect()->back()->with('message', '保存しました！')->with('title', 'ガチャ編集')->with('type', 'dialog');
    }

    public function gachaArrangeCronjob() {
        if (getOption('shuffle_mode') == '1') {
            $gachas = Gacha::where('order_level', '<', 1000000)->inRandomOrder()->get();
            $order_level = 1;
            foreach($gachas as $gacha) {
                $gacha->update([
                    'order_level' => $order_level
                ]);
                $order_level += 1;
            }
        }
    }

    public function shuffle_mode(Request $request) {
        if ($request->shuffle_mode) {
            setOption('shuffle_mode', '1');
            return redirect()->back()->with('message', 'ガチャランダム整列方式を設定しました。<br/>毎時0分ごとに再配置されます。')->with('type', 'dialog');
        }
        else {
            setOption('shuffle_mode', '0');
            return redirect()->back()->with('message', 'ガチャランダムソート方式の設定を解除しました。')->with('type', 'dialog');
        }
    }

    public function product_last_create(Request $request) {
        $rules = [
            'last_name' => 'required',
            'last_point' => 'required|numeric',
            // 'last_rare' => 'required',
            'last_image' => 'required|image|max:4096',
            'gacha_id' => 'required',
        ];
        if ($request->is_update==1) {
            if(!$request->last_image){
                $rules['last_image'] = '';
            }
        }

        $validatored = $request->validate($rules); 
        
        $data = [
            'name' => $request->last_name,
            'point' => $request->last_point,
            'rare' => $request->last_rare,
            'gacha_id' => $request->gacha_id,
            'is_last' => 1,
        ];
        if($request->last_image){
            $image = saveImage('images/products', $request->file('last_image'), false);
            $data['image'] = $image;
        }

        if ($request->is_update==1) { 
            $products = Product::where('is_last', 1)->where('is_lost_product', 0)->where('gacha_id', $request->gacha_id)->get();
            if (count($products)>0) {
                $products[0]->update($data);
            } else {
                return redirect()->back()->with('message', '失敗しました！')->with('title', 'ガチャ 編集')->with('type', 'dialog');
            }
        } else {
            Product::create($data);
        }
        return redirect()->back()->with('message', '保存しました！')->with('title', 'ガチャ 編集')->with('type', 'dialog');
    }

    public function product_last_destroy($id) {
        Product::where("id", $id)->delete();
        return redirect()->back()->with('message', '削除しました！')->with('title', '編集')->with('type', 'dialog');
    }

    public function product_create(Request $request) {
        $rules = [
            'last_name' => 'required',
            'last_point' => 'required|numeric',
            'last_rare' => 'required',
            'last_image' => 'required|image|max:4096',
            'gacha_id' => 'required',
            'rank' => 'required',
        ];
        if ($request->is_update==1) {
            if(!$request->last_image){
                $rules['last_image'] = '';
            }
        }

        $validatored = $request->validate($rules);
        
        $data = [
            'name' => $request->last_name,
            'point' => $request->last_point,
            'rare' => $request->last_rare,
            'gacha_id' => $request->gacha_id,
            'marks' => $request->last_marks,
            'order' => $request->last_order,
            'lost_type' => $request->last_lost_type,
            'is_last' => 0,
            'rank' => $request->rank,
            'category_id' => $request->category_id,
        ];
        if($request->last_image){
            $image = saveImage('images/products', $request->file('last_image'), false);
            $data['image'] = $image;
        }
        
        if ($request->is_update==1) {
            $product = Product::where('id', $request->last_id);
            $result = $product->update($data);
            if (!$result) {
                return redirect()->back()->with('message', '失敗しました！')->with('title', 'ガチャ 編集')->with('type', 'dialog');        
            }
        } else {
            Product::create($data);
        }
        return redirect()->back()->with('message', '保存しました！')->with('title', 'ガチャ 編集')->with('type', 'dialog');
    }

    public function to_public (Request $request) {
        $gacha_id = $request->gacha_id;
        if (!$gacha_id) {
            return redirect()->back();
        }

        $gacha = Gacha::find($gacha_id);
        if ( !$gacha ) {
            return redirect()->back();
        }
       

        $gacha->status = $request->to_status;
        $gacha->save();

        $string = "非公開にしました！";
        if ($request->to_status) {
            $string = "公開にしました！";
        }
        return redirect()->back()->with('message', $string)->with('title', 'ガチャ 編集')->with('type', 'dialog');
    }

    public function gacha_limit (Request $request) {
        $gacha_id = $request->gacha_id;
        if (!$gacha_id) {
            return redirect()->back();
        }

        $gacha = Gacha::find($gacha_id);
        if ( !$gacha ) {
            return redirect()->back();
        }
        
        $gacha->gacha_limit = $request->to_status;
        $gacha->save();

        $string = "1日1回制限設定をキャンセルしました。";
        if ($request->to_status) {
            $string = "1日1回制限設定を完了しました。";
        }
        return redirect()->back()->with('message', $string)->with('type', 'dialog');
    }

    public function destroy($id) {
        Product::where('gacha_id', $id)->where('is_lost_product', 0)->delete();
        Gacha_video::where('gacha_id', $id)->delete();
        Gacha::where('id', $id)->update(['status' => 2]);
        Gacha_lost_product::where('gacha_id', $id)->delete();
        // Favorite::where('product_id', $id)->delete();
        // Product::where("id", $id)->where('is_lost_product', 2)->delete();
        return redirect()->back()->with('message', '削除しました！')->with('title', 'ガチャ')->with('type', 'dialog');
    }
}
