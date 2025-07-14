<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Courier extends Model
{
    use HasFactory;

protected $fillable = [
    'user_id',
    'national_id',
    'vehicle_type',
    'rating',
    'license_number',
    'vehicle_plate_number',
    'license_image',
    'vehicle_plate_image',
];
    protected $appends = ['license_image_fullsrc', 'vehicle_plate_image_fullsrc'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function ratings()
    {
        return $this->hasMany(CourierRating::class);
    }


        public function getLicenseImageFullsrcAttribute()
    {
        return $this->license_image
            ? Storage::disk('public')->url($this->license_image)
            : null;
    }

    public function getVehiclePlateImageFullsrcAttribute()
    {
        return $this->vehicle_plate_image
            ? Storage::disk('public')->url($this->vehicle_plate_image)
            : null;
    }
}
