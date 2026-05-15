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
    public function store(Request $request)
    {
        // 1. التحقق من صحة البيانات المدخلة
        $request->validate([
            'days' => 'required|array|min:1',
            'days.*' => [
            'required',
            'string',
            Rule::in(['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'])
        ],
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime',
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
}}
