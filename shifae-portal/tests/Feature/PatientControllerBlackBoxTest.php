<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Booking;
use App\Models\Patient;
use App\Models\User;
use App\Models\Doctor;

class PatientControllerBlackBoxTest extends TestCase
{
    // هذه الخاصية لضمان أن قاعدة البيانات يتم مسحها وإعادة بنائها بعد كل اختبار
    // لكي لا تتداخل الاختبارات مع بعضها
    use RefreshDatabase;

    protected $booking;

    // دالة setUp تعمل قبل كل اختبار لتهيئة بيئة الاختبار (Execution Condition)
    protected function setUp(): void
    {
        parent::setUp();

        // 1. إنشاء طبيب لربطه بالحجز
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
            'specialty' => 'اخصائي',
            'roomNumber' => '101',
        ]);

        // 2. إنشاء مريض
        $patient = Patient::create([
            'patientName' => 'Test Patient',
            'phoneNumber' => '0910000000',
            'dateOfBirth' => '1990-01-01',
            'gender' => 'male',
        ]);

        // 3. إنشاء حجز في حالة pending (وهذا هو شرط تنفيذ الدالة الأساسي)
        $this->booking = Booking::create([
            'patientId' => $patient->id,
            'doctorId' => $doctor->doctorId, 
            'appointmentDate' => '2026-10-10 10:00:00',
            'roomNumber' => '101',
            'status' => 'pending',
        ]);
    }

    /**
     * TC_01: دفع قيمة صحيحة (فئة التكافؤ الصالحة)
     * المبلغ 50، وطريقة الدفع كاش
     */
    public function test_tc01_valid_payment_accepted()
    {
        // إرسال الطلب عبر الـ POST
        $response = $this->post(route('receptionist.booking.pay', $this->booking->id), [
            'amount_paid' => 50,
            'payment_method' => 'Cash',
        ]);

        // التحقق من أن العملية نجحت ورجعت للخلف مع رسالة نجاح
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * TC_02: الدفع عند الحد المسموح به (تحليل القيم الحدية)
     * المبلغ 0 (وهو مسموح لأن الشرط min:0)
     */
    public function test_tc02_payment_on_boundary_accepted()
    {
        $response = $this->post(route('receptionist.booking.pay', $this->booking->id), [
            'amount_paid' => 0,
            'payment_method' => 'Cash',
        ]);

        // التحقق من القبول
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * TC_03: الدفع تحت الحد المسموح (قيمة حدية وفئة غير صالحة)
     * المبلغ -1
     */
    public function test_tc03_payment_below_boundary_rejected()
    {
        $response = $this->post(route('receptionist.booking.pay', $this->booking->id), [
            'amount_paid' => -1,
            'payment_method' => 'Cash',
        ]);

        // التحقق من أن النظام رفض العملية وأرجع خطأ خاص بحقل المبلغ
        $response->assertSessionHasErrors(['amount_paid']);
    }

    /**
     * TC_04: إدخال نص بدل الرقم (فئة تكافؤ غير صالحة)
     */
    public function test_tc04_non_numeric_payment_rejected()
    {
        $response = $this->post(route('receptionist.booking.pay', $this->booking->id), [
            'amount_paid' => 'نص خاطئ',
            'payment_method' => 'Cash',
        ]);

        // التحقق من الرفض
        $response->assertSessionHasErrors(['amount_paid']);
    }

    /**
     * TC_05: ترك طريقة الدفع فارغة (فئة تكافؤ غير صالحة)
     */
    public function test_tc05_empty_payment_method_rejected()
    {
        $response = $this->post(route('receptionist.booking.pay', $this->booking->id), [
            'amount_paid' => 100,
            'payment_method' => '', // تركناها فارغة عمداً
        ]);

        // التحقق من أن الخطأ هذه المرة في حقل طريقة الدفع
        $response->assertSessionHasErrors(['payment_method']);
    }
}
