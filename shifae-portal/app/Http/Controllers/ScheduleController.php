<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\DoctorSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class ScheduleController extends Controller
{
  // دالة لعرض صفحة إضافة المواعيد
    public function create()
    {
        return view('doctor_schedules_creat');
    }
    // دالة لاستقبال البيانات وحفظها في قاعدة البيانات
    public function addSchedule(Request $request)
    {
        // 1. التحقق من صحة البيانات المدخلة
        $request->validate([
            'days' => 'required|array|min:1',
            'days.*' => [
            'required',
            'string',
            Rule::in(['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'])
        ],
           'startTime' => 'required|date_format:H:i|after_or_equal:09:00|before:18:30',
'endTime'   => 'required|date_format:H:i|after:startTime|before_or_equal:19:00',
        ],
        [
            'days.required' => 'الرجاء اختيار يوم واحد على الأقل.',
        'days.*.in' => 'قيمة اليوم غير صالحة.',
        ]);

        // 2. الحصول على حساب الطبيب المرتبط بالمستخدم الحالي
        $doctor = Auth::user()->doctor;

        // 3. تخزين الموعد في قاعدة البيانات
      foreach ($request->days as $selectedDay) {
            DoctorSchedule::create([
                'doctorId'    => $doctor->doctorId,
                'day'         => $selectedDay, // لتخزين ايام عمل الطبيب
                'startTime'   => $request->startTime,
                'endTime'     => $request->endTime,
                'isAvailable' => true,
            ]);
        }

        // 4. إرجاع الطبيب للصفحة مع رسالة نجاح
        return redirect()->back()->with('success', 'تمت إضافة الموعد بنجاح!');
}
//1. عرض مواعيد الدكتور الحالي
public function showSchedule()
{
    $mySchedules = \App\Models\DoctorSchedule::where('doctorId', auth()->id())->get();
    return view('doctor.doctor_schedule_index', compact('mySchedules'));
}
//دالة لحذ موعد
public function deleteSchedule($id)
{
    $schedule = \App\Models\DoctorSchedule::where('scheduleId', $id)->where('doctorId', auth()->id())->firstOrFail();
    $schedule->delete();
    
    return back()->with('success', 'تم حذف الموعد بنجاح.');
}
// 3. دالة التعديل (تحديث الوقت)
public function updateSchedule(Request $request, $id)
{
    $rules=[
        'startTime' => 'required|date_format:H:i|after_or_equal:09:00',
        'endTime' => 'required|date_format:H:i|after:startTime|before_or_equal:19:00',
    ];
    $messages = [//عرض رسائل الخطاء بالعربية
        'startTime.required'    => 'يجب تحديد وقت بداية الدوام.',
        'startTime.date_format' => 'صيغة الوقت غير صحيحة، يرجى إدخال الوقت بشكل صحيح.',
        'startTime.after_or_equal' => 'يجب أن يبدأ الدوام من الساعة 09:00 صباحاً أو بعدها.',
        
        'endTime.required'      => 'يجب تحديد وقت نهاية الدوام.',
        'endTime.after'         => 'وقت النهاية يجب أن يكون بعد وقت البداية.',
        'endTime.before_or_equal' => 'يجب أن ينتهي الدوام في تمام الساعة 19:00 (07:00 مساءً) أو قبلها.',
    ];
    // تنفيذ التحقق مع الرسائل الجديدة
    $request->validate($rules, $messages);

    
    

    $schedule = \App\Models\DoctorSchedule::where('scheduleId', $id)->where('doctorId', auth()->id())->firstOrFail();
    $schedule->update([
        'startTime' => $request->startTime,
        'endTime' => $request->endTime,
    ]);

    return back()->with('success', 'تم تحديث الوقت بنجاح.');
}
}
