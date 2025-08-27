<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class MerchantProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'description', 'price'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function orderDetails()
{
    return $this->hasMany(OrderDetail::class);
}

}

