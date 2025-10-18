<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Point_history;

class PointHistoryController extends Controller
{
    public function create($user_id, $point, $point_diff, $point_type, $ref_id) {
        if ($point_type == 'purchase' || $point_type == 'exchange') {
            if (Point_history::where('user_id', $user_id)->where('point_type', $point_type)->where('ref_id', $ref_id)->exists()) {
                return 0;
            }
        }
        $item = Point_history::create([
            'point_before' => $point,
            'point_diff' => $point_diff,
            'point_type' => $point_type,
            'user_id' => $user_id,
            'ref_id' => $ref_id
        ]);
        return $item->id;
    }

    public function get_history($user_id) {
        
    }
}
