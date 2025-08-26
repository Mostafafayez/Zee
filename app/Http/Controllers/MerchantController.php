<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MerchantController extends Controller
{
        public function index()
    {
        $merchants = User::role('merchant')->get();
        return response()->json([
            'count' => $merchants->count(),
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




}
