<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Booking;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class BookingController extends Controller
{
 public function create(Request $request)
{
    // 1. تحديد التاريخ المختار (تاريخ اليوم كافتراضي)
    // $selectedDate = $request->get('date', date('Y-m-d'));
    
  try{
    // 2. مصفوفة لترجمة الأيام من الإنجليزية للعربية لتطابق قاعدة بياناتك
    $daysMapping = [
        'Sunday'    => 'الأحد',
        'Monday'    => 'الإثنين',
        'Tuesday'   => 'الثلاثاء',
        'Wednesday' => 'الأربعاء',
        'Thursday'  => 'الخميس',
        'Friday'    => 'الجمعة',
        'Saturday'  => 'السبت',
    ];

    // $dayNameEn = date('l', strtotime($selectedDate)); // يعطي مثلاً "Sunday"
    // $dayNameAr = $daysMapping[$dayNameEn]; // يحوله إلى "الأحد"

    // 3. جلب الأطباء وجداولهم بناءً على اسم اليوم بالعربي
    $doctors = Doctor::whereHas( 'schedules' , function($query) {
        $query->where('isAvailable', true);
    })->with(['user', 'schedules'])->get();

    $availableSlots = [];
    $startDate = \Carbon\Carbon::today();
    $endDate = \Carbon\Carbon::today()->addDays(7);

   for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
        
        // جلب اسم اليوم باللغة العربية مباشرة (مثلاً: "الأحد"، "الأربعاء")
        $dayNameEn = $date->format('l'); 
        $currentDayNameAr = $daysMapping[$dayNameEn]; 
        $formattedDate = $date->format('Y-m-d');

        foreach ($doctors as $doctor) {
            // مطابقة اسم اليوم العربي مع الحقل المخزن في الداتابيز
            $doctorSchedulesForDay = $doctor->schedules->where('day', $currentDayNameAr);

            foreach ($doctorSchedulesForDay as $schedule) {
                $start = \Carbon\Carbon::parse($schedule->startTime);
                $end = \Carbon\Carbon::parse($schedule->endTime);

                // تقسيم الوقت لساعات تلقائياً
                while ($start->copy()->addHour() <= $end) {
                    $slotTime = $start->format('H:i:s');
                   $fullDateTime = $formattedDate . ' ' . $slotTime;

                    // التحقق من أن الموعد في المستقبل وليس في ساعة قد مضت اليوم
                    if (\Carbon\Carbon::parse($fullDateTime)->isPast()) {
                        $start->addHour();
                        continue;
                    }

                    // التحقق من وجود حجز مسبق في هذه الساعة والتاريخ بالتحديد
                    $isBooked = Booking::where('doctorId', $doctor->doctorId)
                        ->where('appointmentDate', $fullDateTime)
                        ->where('status', 'confirmed')
                        ->exists();

                    if (!$isBooked) {
                        $doctorName = $doctor->user ? $doctor->user->fullName : 'طبيب بدون اسم';
                        
                        // تجميع المواعيد المتاحة
                        $availableSlots[$doctorName][] = [
                            'dateLabel' => $currentDayNameAr . ' (' . $formattedDate . ')',
                            'timeLabel' => $start->format('H:i'),
                            'fullTime'  => $slotTime,
                            'bookingDate' => $formattedDate,
                            'doctorId'  => $doctor->doctorId
                        ];
                    }
                    $start->addHour();
                }
            }
        }
    }

    return view('bookings.booking-form', compact('availableSlots'));
}
  catch (\Exception $e) {
        // تسجيل الخطأ في ملفات النظام (storage/logs/laravel.log) لمعرفة سبب المشكلة
        Log::error('حدث خطأ أثناء تحميل صفحة إنشاء الحجز: ' . $e->getMessage());

        // في حالة حدوث خطأ، نرسل المستخدم لنفس الصفحة ولكن بمصفوفة مواعيد فارغة ورسالة خطأ
        session()->flash('error', 'عذراً، حدث خطأ أثناء تحميل المواعيد المتاحة. يرجى المحاولة لاحقاً.');
        
        return view('bookings.booking-form', ['availableSlots' => []]);
}
}
public function store(Request $request)
{
    // 1. التحقق من البيانات
    $request->validate([
        'name' => 'required|string|max:255',
        'phoneNumber' => 'required|string',
        'dateOfBirth' => 'required|date',
        'gender' => 'required|in:male,female',
        'appointment_data' => 'required',
    ]);

    try {
        // 2. فك الدمج (رقم الدكتور | الساعة | التاريخ)
        $parts = explode('|', $request->appointment_data);
        $doctorId = $parts[0];
        $slotTime = $parts[1];
        $selectedDate = $parts[2]; 

        $fullAppointmentDate = $selectedDate . ' ' . $slotTime;

        // 3. إنشاء المريض
        $patient = \App\Models\Patient::create([
            'patientName' => $request->name,
            'phoneNumber' => $request->phoneNumber,
            'dateOfBirth' => $request->dateOfBirth,
            'gender' => $request->gender,
        ]);

        // 4. إنشاء الحجز
        $booking = \App\Models\Booking::create([
            'patientId' => $patient->id, // أو patientId لو كان هكي اسمه في مودل المريض
            'doctorId' => $doctorId,
            'appointmentDate' => $fullAppointmentDate,
            'roomNumber' => $request->roomNumber ?? '101',
            'status' => 'pending',
        ]);
         $bookingNumber = $booking->id;
        // 5. في حال نجاح كل شيء
        return redirect()->back()->with('success', "تم الحجز بنجاح وإضافة الموعد للمنظومة! (#{$bookingNumber})");

    } 
    
    
    
    catch (\Exception $e) {
        // 🛑 هذا السطر هو اللي حيصيد الخطأ لو الداتابيز رفضت الحفظ
        dd('فشل الحفظ في الداتابيز بسبب الخطأ التالي: ' . $e->getMessage());
    }


}


//بنبعتهم من اسراء 
public function update(Request $request, $id)// دالة تعديل حجز لمريض
    {
        try {
            // 1. التحقق من البيانات اللي تبي تسمح بتعديلها فقط
            $validated = $request->validate([
                'roomNumber'  => 'sometimes|string',
                // لو تبي تسمح بتعديل الاسم ورقم الهاتف ضيفهم هنا:
                // 'name'        => 'sometimes|string|max:255',
                // 'phoneNumber' => 'sometimes|string',
            ]);

            // 2. جلب الحجز مباشرة (من غير أي شروط للصلاحيات)
            $booking = Booking::findOrFail($id);

            // 3. تطبيق التعديلات
            $booking->update($validated);

            return redirect()->back()->with('success', 'تم تعديل بيانات الحجز بنجاح.');

        } catch (\Exception $e) {
            \Log::error('حدث خطأ أثناء التعديل: ' . $e->getMessage());
            return redirect()->back()->with('error', 'عذراً، حدث خطأ أثناء التعديل.');
        }
    }

    public function destroy($id)
{
    try {
        // 1. جلب الحجز باستخدام الـ ID أو إرجاع خطأ 404 إذا لم يُعثر عليه
        $booking = Booking::findOrFail($id);

        // 2. تحديث حالة الحجز إلى "ملغي" بدلاً من الحذف الفعلي
      
        $booking->update([
            'status' => 'cancelled'
        ]);

        // 3. إعادة توجيه المريض إلى صفحة إنشاء الحجوزات مع رسالة نجاح
        return redirect()->route('bookings.create')->with('success', 'تم إلغاء الموعد بنجاح.');

    } catch (\Exception $e) {
        // تسجيل الخطأ في ملف النظام إذا حدثت مشكلة غير متوقعة
        \Log::error('حدث خطأ أثناء إلغاء الحجز رقم ' . $id . ': ' . $e->getMessage());
        
        return redirect()->back()->with('error', 'عذراً، حدث خطأ أثناء محاولة إلغاء الموعد.');
    }
}
 // دالة لعرض واجهة البحث
    public function search()
    {
        return view('bookings.search');
    }

    // دالة لاستقبال رقم الحجز والبحث عنه
    public function findBooking(Request $request)
    {
        try {
            // التحقق من أن المريض أدخل رقم الحجز
            $request->validate([
                'booking_id' => 'required|numeric'
            ]);

            // البحث عن الحجز برقم الـ ID
            $booking = Booking::find($request->booking_id);

            // إذا لم يتم العثور على الحجز
            if (!$booking) {
                return redirect()->back()->with('error', 'عذراً، لم نتمكن من العثور على حجز بهذا الرقم. يرجى التأكد من الرقم والمحاولة مرة أخرى.');
            }

            // إذا كان الحجز ملغياً مسبقاً (اختياري: لتنبيه المريض)
            if ($booking->status == 'cancelled') {
                return redirect()->back()->with('error', 'هذا الحجز ملغي مسبقاً.');
            }

            // إذا تم العثور عليه، نقوم بتحويله لصفحة التعديل (التي برمجناها سابقاً)
            return redirect()->route('bookings.edit', $booking->id);

        } catch (\Exception $e) {
            \Log::error('حدث خطأ أثناء البحث عن الحجز: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ غير متوقع أثناء البحث.');
        }
    }
}
