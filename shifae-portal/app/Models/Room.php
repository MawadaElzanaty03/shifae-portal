<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'roomNumber',
        'roomStatus',
    ];
    public function bookings()//علاقة الحجرة بالحجوزات الحجرة ليها اكثر من حجز
    {
        try {
            return $this->hasMany(Booking::class);
        } catch (\Exception $exceptionError) {
            Log::error('Error in Room bookings relation: ' . $exceptionError->getMessage());
            return null;
        }
    }
}
