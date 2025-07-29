<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Area;
use App\Helpers\ShipmentHelper;



$transitions = shipment_transitions(); 

class OrderController extends Controller
{
   public function store(Request $request)
{
    $request->validate([
        'country' => 'required|string|max:100',
        'address' => 'required|string|max:255',


        'receiver_name' => 'required|string|max:255',
        'receiver_address' => 'required|string|max:255',
        'receiver_location' => 'required|string|max:255',
        'note' => 'nullable|string',
        'estimated_delivery' => 'required|date',

        'details' => 'required|array|min:1',
        'details.*.product_name' => 'required|string',
        'details.*.quantity' => 'required|integer',
        'details.*.price' => 'required|numeric',
    ]);

    $user = Auth::user();
    $role = $user->getRoleNames()->first();

    $order_price = collect($request->details)->sum('price');

    $area = Area::where('name', $request->country)->first();
    if (!$area) {
        return response()->json([
            'message' => 'Country not supported for shipping.',
        ], 400);
    }
    $shipping_price = $area->shipping_price;
    $total_price = $order_price + $shipping_price;


    $order = auth()->user()->orders()->create([
        'order_type' => $role,
        'shipping_price' => $shipping_price,
        'order_price' => $order_price,
        'total_price' => $total_price,
        'country' => $request->country,
        'address' => $request->address,
        'track_number' => strtoupper(Str::random(10)),
        'payment_method' => 'cash',
        'status' => 'pending',

        // ✅ New fields
        'receiver_name' => $request->receiver_name,
        'receiver_address' => $request->receiver_address,
        'receiver_location' => $request->receiver_location,
        'note' => $request->note,
        'estimated_delivery' => $request->estimated_delivery,
    ]);

    foreach ($request->details as $item) {
        $order->details()->create($item);
    }

    return response()->json([
        'message' => 'Order created',
        'track_number' => $order->track_number,
        'order' => $order
    ]);
}

    // 2. Admin delete
    public function destroy($track_number)
    {
        $order = Order::where('track_number', $track_number)->firstOrFail();
        $order->delete();

        return response()->json(['message' => 'Order deleted']);
    }

    // 3. Admin update status
    public function s(Request $request, $track_number)
    {
        $request->validate([
            'status' => 'required|in:confirmed_by_admin,assigned,on_the_way,delivered,failed,delayed,returned',
            'reason' => 'nullable|string'
        ]);

        $order = Order::where('track_number', $track_number)->firstOrFail();

        // لا يمكن الرجوع من approved أو delivered أو received
        if (in_array($order->status, ['returned', 'confirmed_by_admin','delivered'])) {
            return response()->json(['message' => 'Cannot change status after it is finalized.'], 400);
        }

        if (!in_array($request->status, ['returned', 'delivered']) && !$request->filled('reason')) {
            return response()->json(['message' => 'You must write a reason for this action.'], 422);
        }

        $order->status = $request->status;
        if ($request->status === 'failed') {
            $order->failure_reason = $request->reason;
        }
        if ($request->status === 'delayed') {
            $order->delay_reason = $request->reason;
            $order->delay_date = now()->addDay(); // مثال فقط
        }

        $order->save();

        return response()->json(['message' => 'Status updated', 'status' => $order->status]);
    }



    public function updateStatus_v2(Request $request, $track_number)
    {
        $request->validate([
            'status' => 'required|string',
            'reason' => 'nullable|string', // required only for returned/delayed
        ]);

        $order = Order::where('track_number', $track_number)->firstOrFail();
        $current = $order->status;
        $next = $request->status;

        $validTransitions = [
            'created' => ['received'],
            'received' => ['on_the_way'],
            'on_the_way' => ['delivered', 'returned', 'delayed'],
        ];

        // Check if the transition is allowed
        if (!isset($validTransitions[$current]) || !in_array($next, $validTransitions[$current])) {
            return response()->json([
                'message' => "Invalid status transition from '$current' to '$next'."
            ], 400);
        }

        // Extra rule: returned/delayed must include a reason
        if (in_array($next, ['returned', 'delayed']) && empty($request->reason)) {
            return response()->json([
                'message' => "A reason is required when changing status to '$next'."
            ], 400);
        }

        // Save status
        $order->status = $next;
        if ($next === 'returned' || $next === 'delayed') {
            $order->status_reason = $request->reason; // You must have this column in your DB
        }
        $order->save();

        return response()->json([
            'message' => "Order status updated to '$next' successfully.",
            'order' => $order,
        ]);
    }


    // 4. Track by track number
    public function track($track_number)
    {
        $order = Order::where('track_number', $track_number)->firstOrFail();

        return response()->json([
            'track_number' => $order->track_number,
            'status' => $order->status,
            'total_price' => $order->total_price
        ]);
    }

    // 5. Get orders for current user
    public function myOrders()
    {
        $orders = auth()->user()->orders()->with('details','user')->get();

        return response()->json($orders);
    }

    // 6. Admin update status
    public function filterMyOrders(Request $request)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $orders = auth()->user()->orders()
            ->where('status', $request->status)
            ->with('details')
            ->get();

        return response()->json($orders);
    }

    // 7. Admin: get all orders
    public function allOrders()
    {
        return response()->json(Order::with('details')->get());
    }

    // 8. Admin: get orders filtered by status
    public function filterAllOrders(Request $request)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $orders = Order::where('status', $request->status)
            ->with('details')
            ->get();

        return response()->json($orders);
    }


     public function confirmed_status(Request $request,$track_number)
    {

   $order = Order::where('track_number', $track_number)->firstOrFail();
        $order->status = 'confirmed';
        $order->save();
        return response()->json($order);
    }


    public function confirmStatus(Request $request, $track_number)
{
    $order = Order::where('track_number', $track_number)->first();

  if (!$order) {
        return response()->json([
            'message' => 'Order not found.'
        ], 404);
    }

    if ($order->status === 'approved') {
        return response()->json([
            'message' => 'Order already approved.',
            'order' => $order
        ], 200);
    }

    $order->status = 'approved';
    $order->save();

    return response()->json([
        'message' => 'Order approved successfully.',
        'order' => $order
    ], 200);
}



public function updateStatus(Request $request)
{
    $request->validate([
        'track_number' => 'required|string',
        'new_status' => 'required|string',
    ]);

    $order = Order::where('track_number', $request->track_number)->first();

    if (!$order) {
        return response()->json(['message' => 'Order not found.'], 404);
    }

    $currentStatus = $order->status;
    $newStatus = $request->new_status;
    $role = auth()->user()->getRoleNames()->first(); // e.g. 'merchant', 'courier', 'admin'

    if (!can_transition($currentStatus, $newStatus, $role)) {
        return response()->json(['message' => 'You are not allowed to make this transition.'], 403);
    }

    $order->status = $newStatus;
    $order->save();

    return response()->json([
        'message' => "Status changed to '$newStatus' successfully.",
        'order' => $order
    ]);
}


}
