<?php

namespace App\Observers;

use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class BookingObserver
{
    /**
     * يتم استدعاء هذه الدالة تلقائياً عند تحديث أي حجز
     */
    public function updated(Booking $targetBooking)
    {
        
        try {
            // التحقق مما إذا كانت حالة الحجز هي التي تغيرت

            if ($targetBooking->wasChanged('status')) { 
                
                // جلب الحجرة مباشرة عبر العلاقة التي أنشأتها في مودل Booking
                $associatedRoom = $targetBooking->room; 
                
                if ($associatedRoom) {
                    // تغيير حالة الحجرة بناءً على حالة الحجز الجديدة
                    if ($targetBooking->status === 'pending' || $targetBooking->status === 'confirmed') {
                        $associatedRoom->roomStatus = 'occupied';
                        // فقط في حالة الإلغاء تعود الغرفة متاحة
                    } elseif ($targetBooking->status === 'cancelled') {
                        $associatedRoom->roomStatus = 'available';
                    }
                    
                    // حفظ حالة الحجرة الجديدة
                    $associatedRoom->save();
                }
            }
        } catch (\Exception $exceptionError) {
            Log::error('Error inside BookingObserver: ' . $exceptionError->getMessage());
        }
    }
}