<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id' , 'order_type','address','country',
        'shipping_price', 'order_price', 'total_price',
        'track_number', 'payment_method', 'status',
        'failure_reason', 'delay_reason', 'delay_date',
        'courier_id' , 'receiver_name', 'receiver_address',
         'note','estimated_delivery',
    ];

    public function user()
    {
        return $this->belongsTo(user::class);
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
