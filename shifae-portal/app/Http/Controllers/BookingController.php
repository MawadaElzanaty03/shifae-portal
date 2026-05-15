<?php
use App\Models\Patient;
use App\Models\Booking;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class BookingController extends Controller
{
public function store(Request $request)
{
    // جلب بيانات الجدول الزمني باستخدام المفتاح scheduleId
    $schedule = DoctorSchedule::where('scheduleId', $request->scheduleId)->firstOrFail();

    // 1. التحقق اللحظي من توفر الطبيب [cite: 219]
    if (!$schedule->isAvailable) {
        return response()->json(['message' => 'عذراً، الموعد غير متاح'], 422);
    }

    // 2. التحقق اللحظي من توفر الغرفة [cite: 221]
    if (!Booking::checkRoomAvailability($schedule->day, $schedule->startTime, $request->roomNumber)) {
        return response()->json(['message' => 'الغرفة مشغولة حالياً'], 422);
    }

    return DB::transaction(function () use ($request, $schedule) {
        // إنشاء سجل المريض (Guest)
        $patient = Patient::create([
            'name' => $request->name,
            'phoneNumber' => $request->phoneNumber,
            'age' => $request->age
        ]);

        // إنشاء الحجز وربطه بالدكتور عبر doctorId
        $booking = Booking::create([
            'patientId' => $patient->id,
            'doctorId'  => $schedule->doctorId, // الحقل من صورتك
            'appointmentDate' => $schedule->day,
            'startTime' => $schedule->startTime,
            'roomNumber'=> $request->roomNumber,
            'status'    => 'confirmed'
        ]);

        // تحديث حالة التوفر في جدول المواعيد
        $schedule->update(['isAvailable' => false]);

        return response()->json(['status' => 'success', 'bookingId' => $booking->id]);
    });
}}
php?>