<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

use App\Models\Gacha;
use App\Http\Resources\GachaListResource;
use App\Http\Resources\ProductListResource;
use App\Models\Gacha_record;
use App\Models\Point_history;
use Illuminate\Support\Facades\Cache;

use App\Models\Product;
use Str;
use Illuminate\Support\Facades\DB;


class HomeController extends Controller
{
    public function get_period_day($current) {
        return date('Y-m-d', strtotime($current));
        // $current_day = date('Y-m-d 20:00:00', strtotime($current));
        // if ($current < $current_day) $current = date('Y-m-d', strtotime($current.' -1 days'));
        // else $current = date('Y-m-d', strtotime($current));
        // return $current;
    }

    public function index(Request $request) {
        $cat_id = 1;
        if ($request->cat_id) {
            $cat_id = $request->cat_id;
        }
        $user = auth()->user();
        $rank = $user && $user->type == 0 ? $user->current_rank : 0;
        $gachas = Gacha::where('category_id', $cat_id)
            ->where('status', 1)
            ->whereRaw('(ISNULL(end_time) || end_time>NOW())')
            ->where(function($query) use ($rank) {
                $query->where('rank_limit', '=', $rank)
                      ->orWhere('rank_limit', 0);
            });
        
        $gachas = $gachas->orderBy('order_level', 'DESC')->orderBy('id', 'DESC')->get();
        
        if ($user && $user->type == 0) {
            $gachas_ = [];
            $pulled = [];
            foreach($gachas as $gacha) {
                $gacha->status = 0;
                if ($gacha->gacha_limit == 1) {
                    $last = Gacha_record::where('user_id', $user->id)->where('gacha_id', $gacha->id)->where('status', 1)->latest()->first();
                    if ($last) {
                        $gacha->status = 1;
                        $now = $this->get_period_day(date('Y-m-d H:i:s'));
                        $record = $this->get_period_day($last->updated_at);
                        if ($now == $record) {
                            array_push($pulled, $gacha);
                            continue;
                        }
                    }
                }
                array_push($gachas_, $gacha);
            }
            $gachas = array_merge($gachas_, $pulled);
        }
        $gachas = GachaListResource::collection($gachas);
        $hide_back_btn = 1;
        $show_notification = 0;
        $banners = DB::table('banner')->orderBy('order')->get()->toArray();
        
        return inertia('Home', compact('gachas', 'hide_back_btn', 'show_notification', 'banners'));   
    }

    public function dp(Request $request) {
        $cat_id = 1;
        if ($request->cat_id) {
            $cat_id = $request->cat_id;
        }
        $cats = getCategories();
        $categories = [];
        $id = 0;
        $flag = 0;
        foreach($cats as $cat) {
            if (str_contains($cat->title, '無限確率')) {
                if ($cat->id == $cat_id) $flag = 1;
            }
            else {
                $categories[] = $cat;
                if ($id == 0) $id = $cat->id;
            }
        }
        if ($flag == 1) $cat_id = $id;
        
        $products = Product::where('is_lost_product', 2)->where('category_id', $cat_id)->orderBy(DB::raw('marks>0'), 'desc')->orderBy('dp', 'desc')->get();
        $products = ProductListResource::collection($products);

        $hide_back_btn = 1; 
        $banners = DB::table('banner')->orderBy('order')->get()->toArray();

        $category_share = [
            'cat_id' => $cat_id,
            'categories' => $categories,
            'cat_route_appendix' => "?cat_id=". $cat_id
        ];

        return inertia('HomeDp', compact('products', 'hide_back_btn', 'banners', 'category_share'));
    }

    public function dashboard() {
        if(Auth::check()) {
            if (auth()->user()->getType() == 'admin') {
                // return redirect()->route('admin');
                return redirect()->route('admin.gacha');
            }else{
                return redirect()->route('main');
            }
        }
        return redirect()->route('main');
    }

    public function how_to_use() {
        $hide_cat_bar = 1;
        return inertia('Normal/HowToUse', compact('hide_cat_bar'));
    }

    public function privacy_police() {
        $hide_cat_bar = 1;
        $title = 'プライバシーポリシー';
        $content = getOption('privacy');
        return inertia('Normal/Page', compact('hide_cat_bar', 'title', 'content'));
    }

    public function terms_conditions() {
        $hide_cat_bar = 1;
        $title = '利用規約';
        $content = getOption('terms');
        return inertia('Normal/Page', compact('hide_cat_bar', 'title', 'content'));
    }

    public function contact_us() {
        $hide_cat_bar = 1;
        return inertia('Normal/ContactUs', compact('hide_cat_bar'));
    }

    public function notation_commercial() {
        $hide_cat_bar = 1;
        $title = '特定商取引法に基づく表記';
        $content = getOption('notation');
        return inertia('Normal/Page', compact('hide_cat_bar', 'title', 'content'));
    }

    public function status_estimate() {
        $hide_cat_bar = 1;
        return inertia('Normal/StatusEstimate', compact('hide_cat_bar'));
    }

    public function maintainance() {
        $maintainance = getOption('maintainance');
        if ($maintainance!="1") {
            return redirect()->route('main');
        }
        return inertia('Maintainance');
    }
}
