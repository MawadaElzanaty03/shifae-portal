<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Doctor;
use App\Models\DoctorSchedule;

class ScheduleControllerBlackBoxTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $doctor;
    protected $schedule;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. إنشاء حساب للطبيب وتسجيل الدخول به
        $this->user = User::create([
            'fullName' => 'Dr. Israa',
            'email' => 'israa@test.com',
            'userName' => 'dr_israa',
            'password' => bcrypt('12345678'),
            'userRole' => 'Doctor',
            'gender' => 'female',
        ]);

        $this->doctor = Doctor::create([
            'userId' => $this->user->id ?? $this->user->userId,
            'specialty' => 'طبيب عام',
            'roomNumber' => '101',
        ]);

        // 2. إنشاء جدول موعد وهمي للطبيب لكي نقوم بتحديثه
        $this->schedule = DoctorSchedule::create([
            'doctorId' => $this->doctor->doctorId ?? $this->doctor->id ?? $this->user->id, // الاعتماد على الـ ID لتجنب أخطاء الربط
            'day' => 'الأحد',
            'startTime' => '09:00:00',
            'endTime' => '14:00:00',
            'isAvailable' => true,
        ]);
    }

    /**
     * TC_01: إدخال وقت صحيح ضمن الدوام (فئة صالحة)
     */
    public function test_tc01_valid_schedule_update()
    {
        // يجب أن نكون مسجلين دخول كطبيب لكي لا يتم طردنا لصفحة الـ Login
        $response = $this->actingAs($this->user)->put(route('doctor.schedules.update', $this->schedule->scheduleId ?? $this->schedule->id), [
            'startTime' => '10:00',
            'endTime' => '14:00',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * TC_02: إدخال وقت على الحدود المسموحة (09:00 و 19:00)
     */
    public function test_tc02_schedule_on_boundaries()
    {
        $response = $this->actingAs($this->user)->put(route('doctor.schedules.update', $this->schedule->scheduleId ?? $this->schedule->id), [
            'startTime' => '09:00',
            'endTime' => '19:00',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * TC_03: إدخال وقت بداية قبل الدوام (قيمة حدية غير صالحة)
     */
    public function test_tc03_start_time_below_boundary_rejected()
    {
        $response = $this->actingAs($this->user)->put(route('doctor.schedules.update', $this->schedule->scheduleId ?? $this->schedule->id), [
            'startTime' => '08:59', // قبل التاسعة
            'endTime' => '14:00',
        ]);

        // النظام يرجع خطأ في حقل startTime
        $response->assertSessionHasErrors(['startTime']);
    }

    /**
     * TC_04: إدخال وقت نهاية بعد الدوام (قيمة حدية غير صالحة)
     */
    public function test_tc04_end_time_above_boundary_rejected()
    {
        $response = $this->actingAs($this->user)->put(route('doctor.schedules.update', $this->schedule->scheduleId ?? $this->schedule->id), [
            'startTime' => '10:00',
            'endTime' => '19:01', // بعد السابعة مساءً
        ]);

        // النظام يرجع خطأ في حقل endTime
        $response->assertSessionHasErrors(['endTime']);
    }

    /**
     * TC_05: خطأ منطقي.. النهاية قبل البداية (فئة تكافؤ غير صالحة)
     */
    public function test_tc05_end_time_before_start_time_rejected()
    {
        $response = $this->actingAs($this->user)->put(route('doctor.schedules.update', $this->schedule->scheduleId ?? $this->schedule->id), [
            'startTime' => '12:00',
            'endTime' => '11:00', // النهاية قبل البداية
        ]);

        // النظام يرجع خطأ في حقل endTime
        $response->assertSessionHasErrors(['endTime']);
    }
}
