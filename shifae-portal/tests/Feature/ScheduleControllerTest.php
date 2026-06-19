<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Doctor;
use App\Models\DoctorSchedule;

class ScheduleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $doctor;

    protected function setUp(): void
    {
        parent::setUp();

        // مستخدم إضافي لضمان اختلاف المعرفات (User ID و Doctor ID)
        User::create([
            'userName' => 'dummyUser',
            'fullName' => 'Dummy',
            'email' => 'dummy@test.com',
            'password' => bcrypt('123'),
            'userRole' => 'patient',
        ]);

        // المستخدم الخاص بالطبيب
        $this->user = User::create([
            'userName' => 'testDoctor',
            'fullName' => 'Test Doctor',
            'email' => 'doctor@test.com',
            'password' => bcrypt('password123'),
            'userRole' => 'doctor',
        ]);

        // بيانات الطبيب المرتبطة بالمستخدم
        $this->doctor = Doctor::create([
            'userId' => $this->user->userId, 
            'specialty' => 'عام',
            'profitPercentage' => 50,
        ]);
    }

    // اختبار دالة عرض صفحة إنشاء المواعيد
    public function test_doctor_can_view_create_schedule_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('/doctor/add-schedule'); 
        $response->assertStatus(200);
        $response->assertViewIs('doctor_schedules_creat');
    }

    // اختبار رفض إضافة الموعد إذا كان وقت النهاية خاطئاً
    public function test_add_schedule_validation_fails_if_end_time_before_start_time()
    {
        $this->actingAs($this->user);

        $invalidData = [
            'days' => ['الأحد', 'الإثنين'],
            'startTime' => '10:00',
            'endTime' => '09:00', 
        ];

        $response = $this->post('/doctor/store-schedule', $invalidData);
        $response->assertSessionHasErrors(['endTime']);
        
        $this->assertDatabaseMissing('doctor_schedules', [
            'doctorId' => $this->doctor->doctorId,
        ]);
    }

    // اختبار إضافة المواعيد وحفظها في قاعدة البيانات بنجاح
    public function test_add_schedule_successfully()
    {
        $this->actingAs($this->user);

        $validData = [
            'days' => ['الإثنين', 'الثلاثاء'],
            'startTime' => '10:00',
            'endTime' => '14:00',
        ];

        $response = $this->post('/doctor/store-schedule', $validData);
        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('doctor_schedules', [
            'doctorId' => $this->doctor->doctorId,
            'day' => 'الإثنين',
            'startTime' => '10:00',
            'endTime' => '14:00',
            'isAvailable' => 1,
        ]);

        $this->assertDatabaseHas('doctor_schedules', [
            'doctorId' => $this->doctor->doctorId,
            'day' => 'الثلاثاء',
        ]);
    }

    // اختبار تمكين الطبيب من استعراض مواعيده الخاصة فقط
    public function test_doctor_can_view_his_schedules()
    {
        $this->actingAs($this->user);

        DoctorSchedule::create([
            'doctorId' => $this->doctor->doctorId,
            'day' => 'الأربعاء',
            'startTime' => '10:00:00',
            'endTime' => '12:00:00',
            'isAvailable' => true,
        ]);

        $response = $this->get('/doctor/manage-schedules');
        $response->assertStatus(200);
        $response->assertViewIs('doctor.doctor_schedule_index');
        
        $response->assertViewHas('mySchedules');
        $schedules = $response->original->getData()['mySchedules'];
        $this->assertCount(1, $schedules);
    }

    // اختبار عملية حذف موعد محدد
    public function test_doctor_can_delete_his_schedule()
    {
        $this->actingAs($this->user);

        $schedule = DoctorSchedule::create([
            'doctorId' => $this->doctor->doctorId,
            'day' => 'الخميس',
            'startTime' => '10:00:00',
            'endTime' => '12:00:00',
            'isAvailable' => true,
        ]);

        $this->assertDatabaseHas('doctor_schedules', [
            'scheduleId' => $schedule->scheduleId,
        ]);

        $response = $this->delete('/doctor/schedule/delete/' . $schedule->scheduleId);
        $response->assertStatus(302);
        
        $this->assertDatabaseMissing('doctor_schedules', [
            'scheduleId' => $schedule->scheduleId,
        ]);
    }

    // اختبار رفض تعديل الموعد عند إدخال أوقات غير صالحة
    public function test_update_schedule_validation_fails()
    {
        $this->actingAs($this->user);

        $schedule = DoctorSchedule::create([
            'doctorId' => $this->doctor->doctorId,
            'day' => 'الجمعة',
            'startTime' => '10:00:00',
            'endTime' => '12:00:00',
            'isAvailable' => true,
        ]);

        $invalidData = [
            'startTime' => '13:00',
            'endTime' => '11:00',
        ];

        $response = $this->put('/doctor/schedule/update/' . $schedule->scheduleId, $invalidData);
        $response->assertSessionHasErrors(['endTime']);
    }

    // اختبار تحديث وقت الموعد بنجاح
    public function test_doctor_can_update_his_schedule()
    {
        $this->actingAs($this->user);

        $schedule = DoctorSchedule::create([
            'doctorId' => $this->doctor->doctorId,
            'day' => 'الجمعة',
            'startTime' => '10:00:00',
            'endTime' => '12:00:00',
            'isAvailable' => true,
        ]);

        $validData = [
            'startTime' => '14:00',
            'endTime' => '16:00',
        ];

        $response = $this->put('/doctor/schedule/update/' . $schedule->scheduleId, $validData);
        $response->assertStatus(302);

        $this->assertDatabaseHas('doctor_schedules', [
            'scheduleId' => $schedule->scheduleId,
            'startTime' => '14:00',
            'endTime' => '16:00',
        ]);
    }
}
