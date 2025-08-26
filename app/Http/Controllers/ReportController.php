<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function adminReport(Request $request)
{
    $year = $request->input('year');   // مثال: 2024
    $month = $request->input('month'); // مثال: 12

    // -------------------------
    // 1️⃣ لو مفيش year => تقرير سنوي
    // -------------------------
    if (!$year) {
        $byYear = Order::selectRaw('YEAR(created_at) as year, COUNT(*) as total')
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->get();

        return response()->json([
            'type'   => 'yearly',
            'report' => $byYear,
        ]);
    }

    // -------------------------
    // 2️⃣ لو فيه year ومفيش month => تقرير شهري
    // -------------------------
    if ($year && !$month) {
        $byMonth = Order::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        return response()->json([
            'type'   => 'monthly',
            'year'   => $year,
            'report' => $byMonth,
        ]);
    }

    // -------------------------
    // 3️⃣ لو فيه year + month => تقرير أسبوعي + يومي
    // -------------------------
    if ($year && $month) {
        // by week
        $byWeek = Order::selectRaw('WEEK(created_at, 1) as week, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy('week')
            ->orderBy('week', 'asc')
            ->get();

        // by day
        $byDay = Order::selectRaw('DAY(created_at) as day, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy('day')
            ->orderBy('day', 'asc')
            ->get();

        return response()->json([
            'type'   => 'daily-weekly',
            'year'   => $year,
            'month'  => $month,
            'byWeek' => $byWeek,
            'byDay'  => $byDay,
        ]);
    }
}

}
