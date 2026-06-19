<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Room;
use App\Models\DoctorSchedule;
use Illuminate\Support\Str;

class BookingControllerBlackBoxTest extends TestCase
{
    use RefreshDatabase;

    protected $validAppointmentData;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. تجهيز طبيب لتمرير بياناته في الـ appointment_data
        $user = User::create([
            'fullName' => 'Test Doctor',
            'email' => 'doctor@test.com',
            'userName' => 'test_doctor',
            'password' => bcrypt('12345678'),
            'userRole' => 'Doctor',
            'gender' => 'male',
        ]);

        $doctor = Doctor::create([
            'userId' => $user->userId,
            'specialty' => 'اخصائي عام',
            'roomNumber' => '101',
        ]);

        // 2. تجهيز غرفة متاحة (لأن الكنترولر يبحث عن غرفة متاحة أثناء الحجز)
        Room::create([
            'roomNumber' => '102',
            'roomType' => 'Consultation',
            'roomStatus' => 'Available',
        ]);

        // صيغة البيانات المطلوبة في الكنترولر: (doctorId | time | date)
        $this->validAppointmentData = $doctor->doctorId . '|10:00:00|2026-10-10';
    }

    /**
     * TC_01: اسم عادي وجنس صحيح (فئة تكافؤ صالحة)
     */
    public function test_tc01_valid_name_and_gender()
    {
        $response = $this->post(route('bookings.store'), [
            'name' => 'Mawada',
            'phoneNumber' => '0912345678',
            'dateOfBirth' => '1990-01-01',
            'gender' => 'female',
            'appointment_data' => $this->validAppointmentData,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * TC_02: إدخال اسم طوله 255 حرف بالتمام (قيمة حدية صالحة)
     */
    public function test_tc02_name_on_boundary_accepted()
    {
        // توليد نص طوله 255 حرف
        $nameOnBoundary = Str::random(255);

        $response = $this->post(route('bookings.store'), [
            'name' => $nameOnBoundary,
            'phoneNumber' => '0912345678',
            'dateOfBirth' => '1990-01-01',
            'gender' => 'male',
            'appointment_data' => $this->validAppointmentData,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * TC_03: إدخال اسم طوله 256 حرف (قيمة حدية أعلى من المسموح)
     */
    public function test_tc03_name_above_boundary_rejected()
    {
        // توليد نص طوله 256 حرف (مرفوض)
        $nameAboveBoundary = Str::random(256);

        $response = $this->post(route('bookings.store'), [
            'name' => $nameAboveBoundary,
            'phoneNumber' => '0912345678',
            'dateOfBirth' => '1990-01-01',
            'gender' => 'female',
            'appointment_data' => $this->validAppointmentData,
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * TC_04: إدخال جنس غير موجود في النظام (فئة تكافؤ غير صالحة)
     */
    public function test_tc04_invalid_gender_rejected()
    {
        $response = $this->post(route('bookings.store'), [
            'name' => 'Ali',
            'phoneNumber' => '0912345678',
            'dateOfBirth' => '1990-01-01',
            'gender' => 'unknown_gender', // جنس غير معروف
            'appointment_data' => $this->validAppointmentData,
        ]);

        $response->assertSessionHasErrors(['gender']);
    }

    /**
     * TC_05: ترك حقل الاسم فارغاً (فئة تكافؤ غير صالحة)
     */
    public function test_tc05_empty_name_rejected()
    {
        $response = $this->post(route('bookings.store'), [
            'name' => '', // تركه فارغاً
            'phoneNumber' => '0912345678',
            'dateOfBirth' => '1990-01-01',
            'gender' => 'male',
            'appointment_data' => $this->validAppointmentData,
        ]);

        $response->assertSessionHasErrors(['name']);
    }
}
