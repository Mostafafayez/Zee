<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MerchantProduct;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MerchantController extends Controller
{
        public function index()
    {
        $merchants = User::role('merchant')->with('merchant')->get();
        return response()->json([
            // 'count' => $merchants->count(),
            'merchants' => $merchants
        ]);
    }

 public function product_merchant()
    {
        $merchant = Auth::user();

        $products = $merchant->merchantProducts()->get();

        return response()->json([
            'products' => $products
        ]);
    }



     public function store(Request $request)
    {
        $request->validate([
            'merchant_id' => 'required|exists:users,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
        ]);
   $merchant = User::find($request->merchant_id);

    if (! $merchant->hasRole('merchant')) {
        return response()->json([
            'error' => 'The selected user is not a merchant.'
        ], 422);
    }

        $product = MerchantProduct::create([
            'user_id'     => $request->merchant_id,
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }



public function uploadInvoice(Request $request, $track_number)
{
    $request->validate([
        'invoice' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ]);

    $order = Order::where('track_number', $track_number)->firstOrFail();

    if ($request->hasFile('invoice')) {
        $path = $request->file('invoice')->store('invoices', 'public');

        $order->update([
            'payment_status' => 'paid',
            'invoice' => $path,
        ]);

        return response()->json([
            'message' => 'Invoice uploaded successfully',
            'data' => $order,
            'invoice_url' => asset('storage/' . $path),
        ], 200);
    }

    return response()->json([
        'message' => 'No file uploaded'
    ], 400);
}





}
