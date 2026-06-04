<?php
namespace App\States;

use App\Models\Booking;

interface BookingStateInterface
{
    // دالة الدفع وتأكيد الحضور
    public function confirmAttendanceAndPay(Booking $booking, $amount, $paymentMethod);
    
}
