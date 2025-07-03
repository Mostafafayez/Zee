<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourierRating extends Model
{
    use HasFactory;

    protected $fillable = ['courier_id', 'user_id', 'rating', 'comment'];

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
