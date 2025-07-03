<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class MerchantController extends Controller
{
        public function index()
    {
        $merchants = User::role('merchant')->get(); // باستخدام Spatie

        return response()->json([
            'count' => $merchants->count(),
            'merchants' => $merchants
        ]);
    }
}
