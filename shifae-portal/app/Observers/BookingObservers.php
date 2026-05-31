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
            // ملاحظة: لو كان اسم عمود الحالة مختلف عندك، استبدلي 'status' بالاسم الصحيح
            if ($targetBooking->isDirty('status')) { 
                
                // جلب الحجرة مباشرة عبر العلاقة التي أنشأتيها في مودل Booking
                $associatedRoom = $targetBooking->room; 
                
                if ($associatedRoom) {
                    // تغيير حالة الحجرة بناءً على حالة الحجز الجديدة
                    if ($targetBooking->status === 'Pending' || $targetBooking->status === 'Confirmed') {
                        $associatedRoom->roomStatus = 'Occupied';
                        // فقط في حالة الإلغاء تعود الغرفة متاحة
                    } elseif ($targetBooking->status === 'Cancelled') {
                        $associatedRoom->roomStatus = 'Available';
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