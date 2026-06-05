<?php
namespace App\States;

use App\Models\Booking;
use Exception;

class PendingState implements BookingStateInterface
{
    public function confirmAttendanceAndPay(Booking $booking, $amount, $paymentMethod)
    {
        // بما أن الموعد معلق، نسمح له بالدفع والتحديث
        $booking->update([
            'status' => 'completed/paid',
            'amount_paid' => $amount,
            'payment_method' => $paymentMethod
        ]);

        return true;
    }
    //دالة الالغاء
     
    public function cancelBooking(Booking $booking)
    {
        // تغيير حالة الموعد المعلق إلى ملغي
        $booking->update([
            'status' => 'cancelled'
        ]);
        
        return true;
    }

}
