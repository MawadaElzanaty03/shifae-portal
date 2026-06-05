<?php
namespace App\States;

use App\Models\Booking;
use Exception;

class CompletedState implements BookingStateInterface
{
    public function confirmAttendanceAndPay(Booking $booking, $amount, $paymentMethod)
    {
        // الموعد مدفوع مسبقاً، نمنع العملية لنحمي النظام
        throw new Exception("هذا الموعد مكتمل ومدفوع مسبقاً، لا يمكن تأكيد الدفع مرة أخرى.");
    }

    public function cancelBooking(Booking $booking)
    {
        // نرمي خطأ لنمنع العملية
        throw new Exception("لا يمكن إلغاء هذا الموعد لأنه مكتمل ومدفوع مسبقاً.");
    }
}
