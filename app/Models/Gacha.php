<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gacha extends Model
{
    use HasFactory;
    protected $fillable = [
        'point',
        'count_card',
        'count',
        'lost_product_type',
        'thumbnail',
        'image',
        'category_id',
        'status',
        'spin_limit',
        'gacha_limit',
        'starting_day',
        'start_time',
        'end_time',
        'order_level',
        'rank_limit',
        'consume_point',
        'need_line',
        'title',
        'plan_limit'
    ];

    public function getProductsLostSettingAttribute(){
        return Gacha_lost_product::where('gacha_id', $this->id)->get();
        // return [];
        // return $this->hasMany(Gacha_lost_product::class, 'gacha_id', 'id');
    }

    public function ableCount() {
        $res = 0; $rest_count = 0; $rest_product = 0; $is_error = 0;
        $rest_product = Product::where('gacha_id', $this->id)->where('is_lost_product', 0)->where('is_last', 0)->sum('marks'); 
        
            $productLostSettings = Gacha_lost_product::where('gacha_id', $this->id)->where('count', '>', 0)->get();
            foreach($productLostSettings as $productLostSetting) {
                $products_lost = Product::where('point', $productLostSetting->point)->where('is_lost_product', 1)->where('category_id', $this->category_id)->where('marks', '>', 0);
                $temp = $products_lost->sum('marks');
                
                
                if ($temp >= $productLostSetting->count) {
                    $rest_product = $rest_product + $productLostSetting->count;
                } else {
                    $is_error = 1; $rest_product = 0;
                    break;
                }
                
            }
        // }
        
        $rest_count = $this->count_card - $this->count;
        if ($rest_count<0) { $rest_count = 0; }

        if ($rest_count < $rest_product) {
            $is_error = 1;
        } else {
            $res = $rest_product;
        }
        
        if ($is_error) {
            return 0;
        } else {
            return $res;
        }
    }

    public function timeStatus() {
        $now = date('Y-m-d H:i:s');
        if ($this->start_time && $this->start_time > $now) return 0;
        if ($this->end_time && $this->end_time <= $now) return 2;
        return 1;
    }
    
    public function getProductLast(){
        $products = Product::where('gacha_id', $this->id)->where('is_last', 1)->where('is_lost_product', 0)->get();
        if (count($products)) {
            $products[0]->image = getProductImageUrl($products[0]->image);
            return $products[0];
        } else {
            return [];
        }
    }

    public function getProducts() {
        $products = Product::where('gacha_id', $this->id)->where('is_last', 0)->where('is_lost_product', 0)->orderBy('id', 'ASC')->get();
        return $products;
    }


    public function getAward($award_total, $rest_total, $current_order) {
        $award_products = []; $arr_select = [];
        $products = Product::where('gacha_id', $this->id)->where('is_lost_product', 0)->where('is_last', 0)->where('marks', '>', 0)->where('order', 0)->get();
        // $to = count($products)-1; $from = 0;
        // $item = ['from'=>0, 'to'=>$to, 'type'=>'product', 'point'=>0];
        // array_push($arr_select, $item);
        $to = -1;
        foreach($products as $item) {
            $from = $to + 1;
            $to = $from + $item->marks - 1;
            $item = ['from'=>$from, 'to'=>$to, 'type'=>'product', 'point'=>0, 'id'=>$item->id];
            array_push($arr_select, $item);
        }

        $productLostSettings = Gacha_lost_product::where('gacha_id', $this->id)->where('count', '>', 0)->get();
        foreach($productLostSettings as $productLostSetting) {
            $from = $to + 1;
            $to = $from + $productLostSetting->count - 1;
            $item = ['from'=>$from, 'to'=>$to, 'type'=>'lost', 'point'=>$productLostSetting->point];
            array_push($arr_select, $item);
        }
        
        $order_products = Product::where('gacha_id', $this->id)->where('is_lost_product', 0)->where('is_last', 0)->where('marks', '>', 0)->where('order', '>', 0)->orderby('order')->get();
        $mid = $to;
        
        foreach ($order_products as $item) {
            $from = $to + 1; $to = $from + $item->marks - 1;
            $item = ['from'=>$from, 'to'=>$to, 'type'=>'product', 'point'=>0, 'id'=>$item->id];
            array_push($arr_select, $item);
        }

        
        if ($rest_total != ($to+1)) {
            return [];
        }

        if ($award_total>$rest_total) {
            return [];
        }
        
        $rand_ids = [];
        $index = 0;
        
        for($i=0; $i<$award_total; $i++) {
            $current_order ++;
            if ($index < count($order_products) && $order_products[$index]->order == $current_order) {
                array_push($rand_ids, $mid + 1 + $index);
                $index ++;
                continue;
            }
            for($j=0; $j<10000; $j++) {
                $rand_val = rand(0, $mid);
                if (!in_array($rand_val, $rand_ids)) {
                    array_push($rand_ids, $rand_val);
                    break;
                }
            }
        }

        if (count($rand_ids)!=$award_total) {
            return [];
        }

        $res_product_ids = []; $count_product = 0;
        foreach($rand_ids as $rand_id) {
            $temp = 0;
            foreach($arr_select as $item) {
                if($item['from']<=$rand_id && $rand_id<=$item['to']) {
                    if($item['type']=='product') {
                        $temp = $item['id'];
                        $count_product = $count_product + 1;
                    } else {
                        $sql = Product::where('point', $item['point'])->where('is_lost_product', 1)->where('category_id', $this->category_id)->where('marks', '>', 0);
                        $values = $sql->inRandomOrder()->get();
                        foreach($values as $value) {
                            $value_id = $value->id;
                            $value_count = 0;
                            foreach($res_product_ids as $value1) {
                                if ($value_id==$value1) {
                                    $value_count = $value_count + 1;
                                }
                            }
                            if ($value_count<$value->marks) {
                                $temp = $value_id;
                                break; 
                            }
                        }
                    }
                    if ($temp) { break; }
                }
                if ($temp) { break; }
            }
            if (!$temp) {
                $res_product_ids = [];
                break;
            } else {
                array_push($res_product_ids, $temp);
            }
        }

        // if ($count_product!=0 && $count_product==count($products)) {
        if ($award_total == $rest_total) {
            $products_last = Product::where('gacha_id', $this->id)->where('is_last', 1)->where('is_lost_product', 0)->first();
            if ($products_last) {
                array_push($res_product_ids, $products_last->id);
            }
        }

        return $res_product_ids;
    }

    public function getDetail() {
        $count_rest = $this->count_card - $this->count; 
        $timeStatus = $this->timeStatus();
        $ableCount = $this->ableCount();

        return [
            'id'=>$this->id,
            'point'=>$this->point,
            'count_card'=>$this->count_card,
            'count'=>$this->count,
            'count_rest'=>$count_rest,
            'ableCount'=>$ableCount,
            'image'=>$this->image == '' ? '' : getGachaImageUrl($this->image),
            'thumbnail'=>getGachaThumbnailUrl($this->thumbnail),
            'status'=>$this->status,
            'start_time'=>$this->start_time,
            'end_time'=>$this->end_time,
            'timeStatus'=>$timeStatus,
            'order_level'=>$this->order_level,
            'spin_limit'=>$this->spin_limit,
            'remaining'=>$timeStatus == 0 ? (strtotime($this->start_time) - strtotime(date('Y-m-d H:i:s'))) : 
                ($this->end_time ? strtotime($this->end_time) - strtotime(date('Y-m-d H:i:s')) : 0),
            'plan_limit'=>$this->plan_limit,
            'consume_point'=>$this->consume_point,
            'gacha_limit'=>$this->gacha_limit,
        ];
    }
}

