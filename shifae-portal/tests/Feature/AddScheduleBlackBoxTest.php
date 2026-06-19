<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Doctor;

class AddScheduleBlackBoxTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $doctor;

    protected function setUp(): void
    {
        parent::setUp();

        // إنشاء طبيب وتسجيل الدخول كمتطلب مسبق (Execution Condition)
        $this->user = User::create([
            'fullName' => 'Dr. Israa',
            'email' => 'israa2@test.com',
            'userName' => 'dr_israa2',
            'password' => bcrypt('12345678'),
            'userRole' => 'Doctor',
            'gender' => 'female',
        ]);

        $this->doctor = Doctor::create([
            'userId' => $this->user->id ?? $this->user->userId,
            'specialty' => 'طبيب عام',
            'roomNumber' => '101',
        ]);
    }

    /**
     * TC_01: مصفوفة صالحة وتحتوي على عنصرين (أعلى من الحد الأدنى)
     */
    public function test_tc01_array_above_boundary_accepted()
    {
        $response = $this->actingAs($this->user)->post(route('doctor.schedule.add'), [
            'days' => ['الأحد', 'الإثنين'], // مصفوفة صحيحة
            'startTime' => '09:00', // أوقات صحيحة لكي ينحصر الاختبار في الأيام
            'endTime' => '14:00',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * TC_02: مصفوفة صالحة وتحتوي على عنصر واحد (قيمة حدية)
     */
    public function test_tc02_array_on_boundary_accepted()
    {
        $response = $this->actingAs($this->user)->post(route('doctor.schedule.add'), [
            'days' => ['الثلاثاء'], // يوم واحد
            'startTime' => '09:00',
            'endTime' => '14:00',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * TC_03: مصفوفة فارغة (قيمة حدية غير صالحة - أقل من 1)
     */
    public function test_tc03_empty_array_below_boundary_rejected()
    {
        $response = $this->actingAs($this->user)->post(route('doctor.schedule.add'), [
            'days' => [], // مصفوفة فارغة
            'startTime' => '09:00',
            'endTime' => '14:00',
        ]);

        $response->assertSessionHasErrors(['days']);
    }

    /**
     * TC_04: إدخال يوم غير موجود في القائمة (فئة تكافؤ غير صالحة للمحتوى)
     */
    public function test_tc04_invalid_day_content_rejected()
    {
        $response = $this->actingAs($this->user)->post(route('doctor.schedule.add'), [
            'days' => ['Sunday'], // يوم بالإنجليزية بدل العربية
            'startTime' => '09:00',
            'endTime' => '14:00',
        ]);

        // الخطأ سيكون في الفهرس 0 من المصفوفة
        $response->assertSessionHasErrors(['days.0']);
    }

    /**
     * TC_05: إرسال نص بدلاً من مصفوفة (فئة تكافؤ غير صالحة للنوع)
     */
    public function test_tc05_string_instead_of_array_rejected()
    {
        $response = $this->actingAs($this->user)->post(route('doctor.schedule.add'), [
            'days' => 'الأحد', // إرسال نص بدلاً من مصفوفة []
            'startTime' => '09:00',
            'endTime' => '14:00',
        ]);

        $response->assertSessionHasErrors(['days']);
    }
}
