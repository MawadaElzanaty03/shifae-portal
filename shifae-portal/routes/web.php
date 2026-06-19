<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\PatientController;


Route::get('/', function () {
    // نجلب كل الدكاترة مباشرة مع جداول مواعيدهم بدون شرط الـ role 
    // لأننا أصلاً نبحث في جدول الدكاترة
    $doctorsList = \App\Models\Doctor::with('schedules')->get(); 
    
    return view('welcome', ['doctorsList' => $doctorsList]);
})->name('home');

// لعرض صفحة تسجيل الدخول
Route::get('/login', function () {
    return view('login');
})->name('login');





// لاستقبال البيانات من الفورم والتحقق منها
Route::post('/login', [AuthController::class, 'login']);

// لتسجيل الخروج
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//الصفحة الرئيسية لواجهة الدكتور
Route::middleware(['auth'])->group(function () {
    Route::get('/doctor/dashboard', function () {
        return view('doctor.dashboard');
    })->name('doctor.dashboard');
    // عرض صفحة إضافة الموعد
    Route::get('/doctor/add-schedule', [ScheduleController::class, 'create'])->name('doctor.schedule.create');
    Route::post('/doctor/store-schedule', [ScheduleController::class, 'addSchedule'])->name('doctor.schedule.add');
    
        // مسار البحث عن مريض (بواسطة الطبيب أو الاستقبال)
    Route::get('/patients/search', [PatientController::class, 'searchPatient'])->name('patients.search');


    //عرض المواعيد لغرض الحذف او التعديل
    Route::get('/doctor/manage-schedules', [ScheduleController::class, 'showSchedule'])->name('doctor.schedules.index');
    //تعديل موعد معين
    Route::put('/doctor/schedule/update/{id}', [ScheduleController::class, 'updateSchedule'])->name('doctor.schedules.update');
    // حذف موعد معين
    Route::delete('/doctor/schedule/delete/{id}', [ScheduleController::class, 'deleteSchedule'])->name('doctor.schedules.delete');
}

);
Route::get('/bookings/form', [BookingController::class, 'create'])->name('bookings.form');
Route::post('/bookings/store', [BookingController::class, 'store'])->name('bookings.store');

// تم حذف مسارات البحث الخاصة بالمرضى لأن الصلاحية نقلت للاستقبال



// مسار إنشاء السجل الطبي
Route::post('/doctor/records/create', [RecordController::class, 'createRecord'])->name('record.create');

// مسار تحديث الملاحظات السريرية
Route::put('/doctor/records/{recordId}/update-notes', [RecordController::class, 'updateNotes'])->name('record.updateNotes');

    // مسار لوحة تحكم الاستقبال
    Route::get('/receptionist/dashboard', function () {
        try {
            return view('receptionist.dashboard');
        } catch (\Exception $viewError) {
            return back()->withErrors(['systemError' => 'حدث خطأ في عرض الصفحة.']);
        }
    })->name('receptionist.dashboard');



Route::post('/receptionist/booking/{id}/pay', [App\Http\Controllers\PatientController::class, 'confirmPayment'])->name('receptionist.booking.pay');

    // مسار لعرض واجهة التعديل 
Route::get('/bookings/edit/{id}', [App\Http\Controllers\BookingController::class, 'edit'])->name('bookings.edit');

// مسار لإرسال بيانات التعديل
Route::put('/bookings/update/{id}', [App\Http\Controllers\BookingController::class, 'update'])->name('bookings.update');

// مسار لإلغاء الحجز
Route::delete('/bookings/delete/{id}', [App\Http\Controllers\BookingController::class, 'destroy'])->name('bookings.destroy');

Route::get('/api/recommend-doctor', [\App\Http\Controllers\BookingController::class, 'recommendDoctor'])->name('api.recommend.doctor');

// بدء مسارات إدارة الموارد البشرية لإضافة موظف جديد
Route::get('/hr/employees/create', [App\Http\Controllers\EmployeeController::class, 'createEmployeeForm'])->name('hr.employees.create');
Route::post('/hr/employees/store', [App\Http\Controllers\EmployeeController::class, 'addEmployee'])->name('hr.employees.store');
// نهاية مسارات إدارة الموارد البشرية

// مسار لوحة تحكم الإدارة
Route::get('/admin-dashboard', function () {
    // محاولة إرجاع واجهة الإدارة لتجنب توقف النظام
    try {
        return view('admin.dashboard');
    } catch (\Exception $viewError) {
        // في حال حدوث خطأ، نرجعه مع رسالة للمستخدم
        \Log::error('خطأ في واجهة الإدارة: ' . $viewError->getMessage());
        return back()->withErrors(['systemError' => 'حدث خطأ في عرض الصفحة.']);
    }
})->name('admin.dashboard')->middleware('auth');

// مسار التقارير السنوية (خاص بصلاحيات الإدارة)
Route::get('/admin/reports/annual', [App\Http\Controllers\ReportController::class, 'annualReport'])->name('admin.reports.annual')->middleware('auth');