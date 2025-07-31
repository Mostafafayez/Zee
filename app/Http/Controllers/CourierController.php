<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Order;
use App\Models\CourierRating;
use App\Models\User;
use Illuminate\Http\Request;

use App\Events\OrderAssigned;
class CourierController extends Controller
{
    // 1. Assign courier to order


public function assignOrderToCourier(Request $request, $track_number)
{
    $request->validate([
        'courier_id' => 'required|exists:users,id',
    ]);

    $order = Order::where('track_number', $track_number)->firstOrFail();

    $order->courier_id = $request->courier_id;
    $order->status = 'assigned';
    $order->save();

    // Fire the event to notify the courier in real-time
    broadcast(new OrderAssigned($order))->toOthers();
\Log::info('Broadcast was called for order: ' . $order->track_number);

    return response()->json(['message' => 'Order assigned to courier']);
}


    // 2. Get all couriers
    public function getAllCouriers()
    {
        $couriers = Courier::with('user')->get();

        return response()->json($couriers);
    }

    // 3. Get couriers with their orders
    public function getCouriersWithOrders()
    {
        $couriers = Courier::with(['user', 'orders'])->get();

        return response()->json($couriers);
    }

    // 4. Rate courier
    public function rateCourier(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $courier = Courier::findOrFail($id);

        CourierRating::create([
            'courier_id' => $courier->id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Optional: update courier avg rating
        $courier->rating = round($courier->ratings()->avg('rating'), 2);
        $courier->save();

        return response()->json(['message' => 'Courier rated']);
    }
}
