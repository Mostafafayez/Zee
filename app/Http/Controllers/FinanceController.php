<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    // 1. تقرير مالي لتاجر معين
    public function merchantReport(Request $request, $user_id)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'status' => 'nullable|string'
        ]);

        $merchant = User::findOrFail($user_id);

        $orders = $merchant->orders()->when($request->status, function ($q) use ($request) {
            $q->where('status', $request->status);
        })->when($request->from, function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->from);
        })->when($request->to, function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->to);
        })->get();

        $totalOrders = $orders->count();
        $totalOrderPrice = $orders->sum('order_price');
        $totalShipping = $orders->sum('shipping_price');
        $totalRevenue = $orders->sum('total_price');
        $companyProfit = $totalShipping;

        return response()->json([
            'merchant' => $merchant->name,
            'total_orders' => $totalOrders,
            'total_order_price' => $totalOrderPrice,
            'total_shipping' => $totalShipping,
            'total_revenue' => $totalRevenue,
            'company_profit' => $companyProfit,
            'orders' => $orders
        ]);
    }

    // 2. تقرير شامل لكل التجار
    public function overallReport(Request $request)
    {
        $orders = Order::query()
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->from, fn($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to, fn($q) => $q->whereDate('created_at', '<=', $request->to))
            ->get();

        return response()->json([
            'total_orders' => $orders->count(),
            'total_order_price' => $orders->sum('order_price'),
            'total_shipping' => $orders->sum('shipping_price'),
            'total_revenue' => $orders->sum('total_price'),
            'company_profit' => $orders->sum('shipping_price'),
        ]);
    }

    public function userTotalPrices()
{
    $user = auth()->user();

    $data = Order::where('user_id', $user->id)
        ->selectRaw('
            SUM(shipment_price) as total_shipment_price,
            SUM(order_price) as total_order_price,
            SUM(total_price) as total_price
        ')
        ->first();

    return response()->json($data);
}

public function userPricesByStatus(Request $request)
{
    $request->validate([
        'status' => 'required|string'
    ]);

    $user = auth()->user();

    $data = Order::where('user_id', $user->id)
        ->where('status', $request->status)
        ->selectRaw('
            SUM(shipment_price) as total_shipment_price,
            SUM(order_price) as total_order_price,
            SUM(total_price) as total_price
        ')
        ->first();

    return response()->json([
        'status' => $request->status,
        'totals' => $data
    ]);
}



    public function courierPricesByStatus(Request $request)
{
    $request->validate([
        'status' => 'required|string'
    ]);

    $courier = auth()->user();

    $data = Order::where('courier_id', $courier->id)
        ->where('status', $request->status)
        ->selectRaw('
            SUM(shipment_price) as total_shipment_price,
            SUM(order_price) as total_order_price,
            SUM(total_price) as total_price
        ')
        ->first();

    return response()->json([
        'status' => $request->status,
        'totals' => $data
    ]);
}


public function userOrdersByPaymentStatus(Request $request)
{
    $request->validate([
        'payment_status' => 'required|string|in:paid,unpaid'
    ]);

    $user = auth()->user();

    $count = Order::where('user_id', $user->id)
        ->where('payment_status', $request->payment_status)
        ->count();

    return response()->json([
        'payment_status' => $request->payment_status,
        'total_orders' => $count
    ]);
}


}
