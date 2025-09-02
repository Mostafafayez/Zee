<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SystemInfoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Price_listController;
use Illuminate\Support\Facades\Broadcast;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);//num 2
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/users', [AuthController::class, 'createUser']);   // admin only
    Route::post('/users/{user}/password', [AuthController::class, 'updatePassword']); // admin only
    Route::delete('/users/{userid}', [AuthController::class, 'destroy']); // admin only
    Route::get('/users/count-by-role', [AuthController::class, 'countUsersByRole']);

});


Route::middleware('auth:sanctum')->post('/user',[AuthController::class, 'store']); //used 1


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('areas')
    ->group(function () {
    Route::post('/', [Price_listController::class, 'store']);
    Route::get('/{id}', [Price_listController::class, 'show']);
    Route::post('/{id}', [Price_listController::class, 'update']);
    Route::delete('/{id}', [Price_listController::class, 'destroy']);
});

    Route::middleware('auth:sanctum')->get('/areas', [Price_listController::class, 'index']);

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin', fn () => 'Welcome Admin');
});
///

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);                        // Add order
    Route::get('orders/me', [OrderController::class, 'myOrders']);                   // Get my orders

    Route::get('/orders/filterstatus', [OrderController::class, 'filterMyOrders']);         // My orders by status
        Route::get('courier/myorders', [OrderController::class, 'myOrdersforCourier']);                   // Get my orders

    Route::middleware('role:admin')->group(function () {
            Route::get('orders/{user_id}', [OrderController::class, 'Orders_user']);                   // Get my orders
        Route::delete('/orders/{track_number}', [OrderController::class, 'destroy']); // Admin delete
        Route::get('/admin/orders', [OrderController::class, 'allOrders']);           // All users' orders
        Route::get('/admin/orders/status', [OrderController::class, 'filterAllOrders']); // All by status
        Route::post('/orders/{track_number}/confirm', [OrderController::class, 'confirmStatus']);

        Route::post('/orders/{track_number}', [OrderController::class, 'confirmed_status']); // Admin delete //not used

    });
});
    Route::middleware(['auth:sanctum'])->group(function () {
   // Admin update status
          Route::post('/assign/{track_number}', [CourierController::class, 'assignOrderToCourier']);


  });


Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    Route::get('/couriers', [CourierController::class, 'getAllCouriers']);
    Route::get('/couriers/with-orders', [CourierController::class, 'getCouriersWithOrders']);

    Route::post('/couriers/{id}/rate', [CourierController::class, 'rateCourier']);
});

     Route::middleware('auth:sanctum')->post('/change_status', [OrderController::class, 'updateStatus']);


Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/finance/merchant/{user_id}', [FinanceController::class, 'merchantReport']);//not used
    Route::get('/finance/summary', [FinanceController::class, 'overallReport']);//notused
    Route::get('/reports/orders-status',  [ReportController::class, 'ordersByStatus']);

});

    Route::get('/reports/orders-week', [ReportController::class, 'ordersThisWeek']);



Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/merchants', [MerchantController::class, 'index']);
    Route::post('/merchant/products', action: [MerchantController::class, 'store']);
    Route::post('/orders/{track_number}/invoice', [merchantController::class, 'uploadInvoice']);

});

Route::post('/orders/{track_number}/invoice', [merchantController::class, 'uploadInvoice']);



Route::apiResource('system-info', SystemInfoController::class);



Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/update_system-info/{id}', [SystemInfoController::class, 'update']);
});


Broadcast::routes(['middleware' => ['auth:sanctum']]);



Route::middleware(['auth:sanctum', 'role:courier'])
    ->get('/courier/orders', [CourierController::class, 'getOrdersByCourier']);

// routes/api.php
Route::post('/admin/orders-report', [ReportController::class, 'adminReport']);



Route::middleware(['auth:sanctum', 'role:merchant'])->group(function () {
Route::get('/merchant/products', [MerchantController::class, 'product_merchant']);
});



    Route::get('/orders/track/{track_number}', [OrderController::class, 'track']);    // Track by number



    Route::middleware('auth:sanctum')->group(function () {
    Route::post('/merchant/shipments/status', [ReportController::class, 'userShipmentsByStatus']);
    Route::get('/merchant/prices/total', [FinanceController::class, 'userTotalPrices']);
    Route::post('/merchant/prices/status', [FinanceController::class, 'userPricesByStatus']);
    Route::post('/courier/prices/status', [FinanceController::class, 'courierPricesByStatus']);
    Route::post('/merchant/orders/payment-status', [FinanceController::class, 'userOrdersByPaymentStatus']);
});
