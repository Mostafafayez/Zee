<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SystemInfo extends Model
{
    protected $table = 'system_info';
    protected $fillable = [
        'system_name',
        'email',
        'phone',
        'logo',
        'address',
    ];

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute()
    {
        return $this->logo ? Storage::disk('public')->url($this->logo) : null;
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
