<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Booking;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\States\PendingState;
use App\States\CompletedState;
use Exception;

class BookingController extends Controller
{
 public function create(Request $request)
{
    
    
  try{
    //  مصفوفة لترجمة الأيام من الإنجليزية للعربية لتطابق قاعدة بياناتك
    $daysMapping = [
        'Sunday'    => 'الأحد',
        'Monday'    => 'الإثنين',
        'Tuesday'   => 'الثلاثاء',
        'Wednesday' => 'الأربعاء',
        'Thursday'  => 'الخميس',
        'Friday'    => 'الجمعة',
        'Saturday'  => 'السبت',
    ];

    
// جلب الأطباء المتاحين فقط
            $doctors = \App\Models\Doctor::whereHas('schedules', function($query) {
                $query->where('isAvailable', true);
            })->with(['user', 'schedules'])->get();
            $doctorsData = [];

    $availableSlots = [];
    $startDate = \Carbon\Carbon::today();
    $endDate = \Carbon\Carbon::today()->addDays(7);


   foreach ($doctors as $doctor) {
                $doctorName = $doctor->user ? $doctor->user->fullName : 'طبيب بدون اسم';
                $doctorId = $doctor->doctorId;
                $doctorDays = [];
                for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                    $dayNameEn = $date->format('l'); 
                    $currentDayNameAr = $daysMapping[$dayNameEn]; 
                    $formattedDate = $date->format('Y-m-d');
                    $doctorSchedulesForDay = $doctor->schedules->where('day', $currentDayNameAr);
                    $availableSlotsForDay = [];
                    foreach ($doctorSchedulesForDay as $schedule) {
                        $start = \Carbon\Carbon::parse($schedule->startTime);
                        $end = \Carbon\Carbon::parse($schedule->endTime);
                        while ($start->copy()->addHour() <= $end) {
                            $slotTime = $start->format('H:i:s');
                            $fullDateTime = $formattedDate . ' ' . $slotTime;
                            if (\Carbon\Carbon::parse($fullDateTime)->isPast()) {
                                $start->addHour();
                                continue;
                            }
                            // التأكد أن الموعد غير محجوز
                            $isBooked = \App\Models\Booking::where('doctorId', $doctorId)
                                ->where('appointmentDate', $fullDateTime)
                                ->whereIn('status', ['confirmed', 'pending'])
                                ->exists();
                            if (!$isBooked) {
                                $availableSlotsForDay[] = $slotTime;
                            }
                            $start->addHour();
                        }
                    }
                    if (count($availableSlotsForDay) > 0) {
                        $doctorDays[$formattedDate] = [
                            'dateLabel' => $currentDayNameAr . ' (' . $formattedDate . ')',
                            'slots' => $availableSlotsForDay
                        ];
                    }
                }
                if (count($doctorDays) > 0) {
                    $doctorsData[$doctorId] = [
                        'name' => $doctorName,
                        'days' => $doctorDays
                    ];
                }
            }
            return view('bookings.booking-form', compact('doctorsData'));
        } catch (\Exception $e) {
            \Log::error('حدث خطأ: ' . $e->getMessage());
            return view('bookings.booking-form', ['doctorsData' => []]);
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

             $availableRoom = \App\Models\Room::where('roomStatus', 'Available')->first();//اي حجرة متاحة

              $assignedRoom = $availableRoom ? $availableRoom->id : 'غير محددة';
        // 4. إنشاء الحجز
        $booking = \App\Models\Booking::create([
            'patientId' => $patient->id, // أو patientId لو كان هكي اسمه في مودل المريض
            'doctorId' => $doctorId,
            'appointmentDate' => $fullAppointmentDate,
            'roomNumber' => $request->roomNumber ?? $assignedRoom, 
            'status' => 'pending',
        ]);
         $bookingNumber = $booking->id;
        // 5. في حال نجاح كل شيء
        return redirect()->back()->with('success', "تم الحجز بنجاح وإضافة الموعد للمنظومة! (#{$bookingNumber})");

    } 
    
    
    
    catch (\Exception $e) {
        return redirect()->back()->with('error', 'فشل الحفظ في الداتابيز بسبب الخطأ التالي: ' . $e->getMessage());
    }



}
    public function edit($id)
    {
        try {
            // جلب الحجز والمريض
            $booking = Booking::with('patient', 'doctor.user')->findOrFail($id);

            // التأكد من حالة الحجز
            if ($booking->status == 'cancelled') {
                return redirect()->back()->with('error', 'هذا الحجز ملغي ولا يمكن تعديله.');
            }

            // تجهيز المواعيد المتاحة للأطباء
            $daysMapping = [
                'Sunday'    => 'الأحد', 'Monday' => 'الإثنين', 'Tuesday' => 'الثلاثاء',
                'Wednesday' => 'الأربعاء', 'Thursday' => 'الخميس', 'Friday' => 'الجمعة', 'Saturday' => 'السبت',
            ];

            $doctors = \App\Models\Doctor::whereHas('schedules', function($query) {
                $query->where('isAvailable', true);
            })->with(['user', 'schedules'])->get();

            $availableSlots = [];
            $startDate = \Carbon\Carbon::today();
            $endDate = \Carbon\Carbon::today()->addDays(7);

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $dayNameEn = $date->format('l'); 
                $currentDayNameAr = $daysMapping[$dayNameEn]; 
                $formattedDate = $date->format('Y-m-d');

                foreach ($doctors as $doctor) {
                    $doctorSchedulesForDay = $doctor->schedules->where('day', $currentDayNameAr);
                    foreach ($doctorSchedulesForDay as $schedule) {
                        $start = \Carbon\Carbon::parse($schedule->startTime);
                        $end = \Carbon\Carbon::parse($schedule->endTime);

                        while ($start->copy()->addHour() <= $end) {
                            $slotTime = $start->format('H:i:s');
                            $fullDateTime = $formattedDate . ' ' . $slotTime;

                            if (\Carbon\Carbon::parse($fullDateTime)->isPast()) {
                                $start->addHour();
                                continue;
                            }

                            $isBooked = \App\Models\Booking::where('doctorId', $doctor->doctorId)
                                ->where('appointmentDate', $fullDateTime)
                                ->where('status', 'confirmed')
                                ->exists();

                            if (!$isBooked) {
                                $doctorName = $doctor->user ? $doctor->user->fullName : 'طبيب بدون اسم';
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

            // إرسال البيانات لواجهة التعديل
            return view('bookings.edit', compact('booking', 'availableSlots'));

        } catch (\Exception $e) {
            \Log::error('خطأ في صفحة التعديل: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل صفحة التعديل.');
        }
    }


//سيتم وضع هذه الدوال في واجهة الاستقبال
public function update(Request $request, $id)// دالة تعديل حجز لمريض
    {
        try {
            // التحقق من المدخلات
            $request->validate([
                'roomNumber'       => 'sometimes|string',
                'appointment_data' => 'sometimes|string', 
            ]);

            // جلب الحجز المستهدف
            $booking = Booking::findOrFail($id);

            // تحديث الموعد في حال تم تغييره
            if ($request->has('appointment_data') && !empty($request->appointment_data)) {
                $parts = explode('|', $request->appointment_data);
                if(count($parts) == 3) {
                    $doctorId = $parts[0];
                    $slotTime = $parts[1];
                    $selectedDate = $parts[2]; 

                    $fullAppointmentDate = $selectedDate . ' ' . $slotTime;

                    $booking->doctorId = $doctorId;
                    $booking->appointmentDate = $fullAppointmentDate;
                }
            }

            // تحديث الغرفة
            if ($request->has('roomNumber')) {
                $booking->roomNumber = $request->roomNumber;
            }

            $booking->save();

            return redirect()->back()->with('success', 'تم تعديل بيانات الموعد بنجاح.');

        } catch (\Exception $e) {
            \Log::error('حدث خطأ أثناء التعديل: ' . $e->getMessage());
            return redirect()->back()->with('error', 'عذراً، حدث خطأ أثناء التعديل.');
        }
    }

    public function destroy($id)
{
    try {
        // جلب الحجز
        $booking = Booking::findOrFail($id);

        // تحديد حالة الحجز
      
       $currentState = null;
        if ($booking->status === 'pending') {
            $currentState = new PendingState();
        } elseif ($booking->status === 'completed/paid') {
            $currentState = new CompletedState();
        }

        //design pattren to cancelled booking

           if ($currentState) {
           
            $currentState->cancelBooking($booking);
        } else {
           
             return redirect()->back()->with('error', 'لا يمكن إجراء هذه العملية على حالة الموعد الحالية.');
        }


        // العودة مع إشعار بالنجاح
        return redirect()->back()->with('success', 'تم إلغاء الموعد بنجاح.');

    }catch (Exception $e) {
        \Log::error('حدث خطأ أثناء إلغاء الحجز رقم ' . $id . ': ' . $e->getMessage());
        
        
        return redirect()->back()->with('error', $e->getMessage()); 
    }
}
    // تم حذف دوال البحث القديمة (search و findBooking) لأنها مهجورة ولم تعد تستخدم

    // دالة الاقتراح التلقائي
    public function recommendDoctor(\Illuminate\Http\Request $request)
    {
        try {
            $age = $request->input('age');
            $gender = $request->input('gender');
            
            // استدعاء المصنع لتحديد منطق التوصية المناسب
            $recommendationLogic = \App\Factories\RecommendationFactory::createRecommendation($age);
            
            // طلب الطبيب المناسب وتمرير البيانات إليه
            $recommendedDoctor = $recommendationLogic->recommend($age, $gender);

            if ($recommendedDoctor && $recommendedDoctor->user) {
             
                return response()->json([
                    'success' => true,
                    'doctor_id' => $recommendedDoctor->doctorId,
                    'doctor_name' => $recommendedDoctor->user->fullName,
                    'specialty' => $recommendedDoctor->specialty,
                    'message' => 'بناءً على بياناتك، نقترح لك هذا الأخصائي. يمكنك اعتماده أو اختيار طبيب آخر يدوياً.'
                ]);
            }

            return response()->json(['success' => false, 'message' => 'لم نتمكن من إيجاد طبيب متطابق تلقائياً، يرجى الاختيار من القائمة.']);
        } catch (\Exception $e) {
            // معالجة الخطأ لمنع ظهور رسالة خطأ صريحة للمستخدم
            \Log::error('حدث خطأ أثناء اقتراح الطبيب: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ غير متوقع أثناء محاولة اقتراح الطبيب.']);
        }
    }
}
