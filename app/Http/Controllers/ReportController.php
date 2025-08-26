<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{


public function ordersThisWeek()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek   = Carbon::now()->endOfWeek();


        $orders = DB::table('orders')
            ->select(DB::raw('DAYNAME(created_at) as day'), DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->groupBy('day')
            ->get();

        $daysOfWeek = [
            'Sunday' => 0,
            'Monday' => 0,
            'Tuesday' => 0,
            'Wednesday' => 0,
            'Thursday' => 0,
            'Friday' => 0,
            'Saturday' => 0,
        ];

        foreach ($orders as $order) {
            $daysOfWeek[$order->day] = $order->total;
        }

        return response()->json($daysOfWeek);
    }

}
