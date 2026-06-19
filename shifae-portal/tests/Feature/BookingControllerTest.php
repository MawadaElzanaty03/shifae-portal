<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Room;
use App\Models\DoctorSchedule;
use App\Models\Booking;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $doctor;
    protected $room;
    protected $schedule;

    protected function setUp(): void
    {
        parent::setUp();

        //  تجهيز بيانات طبيب وهمي
        $user = User::create([
            'fullName' => 'Test Doctor',
            'email' => 'doctor@test.com',
            'userName' => 'test_doctor',
            'password' => bcrypt('12345678'),
            'userRole' => 'Doctor',
            'gender' => 'male', // إضافة الجنس لكي ينجح الاقتراح
        ]);

        $this->doctor = Doctor::create([
            'userId' => $user->userId,
            'specialty' => 'اخصائي عام', // التخصص الذي يبحث عنه المصنع
            'roomNumber' => '101',
        ]);

        //  تجهيز موعد متاح للطبيب
        $this->schedule = DoctorSchedule::create([
            'doctorId' => $this->doctor->doctorId,
            'day' => 'الأحد', // حسب ما هو مستخدم في النظام
            'startTime' => '09:00:00',
            'endTime' => '14:00:00',
            'isAvailable' => true,
        ]);

        //  تجهيز غرفة متاحة في المستشفى
        $this->room = Room::create([
            'roomNumber' => '102',
            'roomType' => 'Consultation',
            'roomStatus' => 'Available',
        ]);
    }

    // اختبارات دالة Store (إنشاء الحجز)
    
    public function test_can_store_booking_successfully()
    {
        $response = $this->post(route('bookings.store'), [
            'name' => 'Test Patient',
            'phoneNumber' => '0912345678',
            'dateOfBirth' => '1990-01-01',
            'gender' => 'male',
            // دمج البيانات كما يطلبها الكنترولر (DoctorId | Time | Date)
            'appointment_data' => $this->doctor->doctorId . '|10:00:00|2026-10-10',
        ]);

        // التحقق من أن الحجز تم بنجاح
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // التحقق من حفظ المريض في الداتابيز
        $this->assertDatabaseHas('patients', [
            'patientName' => 'Test Patient'
        ]);

        // التحقق من حفظ الموعد في الداتابيز
        $this->assertDatabaseHas('bookings', [
            'doctorId' => $this->doctor->doctorId,
            'status' => 'pending'
        ]);
    }

    public function test_store_validation_fails_with_missing_data()
    {
        // إرسال بيانات ناقصة عمداً
        $response = $this->post(route('bookings.store'), [
            'name' => '', // ترك الاسم فارغ لكي تفشل عملية التحقق
        ]);

        // التأكد من رجوع أخطاء خاصة بكل الحقول المطلوبة
        $response->assertSessionHasErrors(['name', 'phoneNumber', 'dateOfBirth', 'gender', 'appointment_data']);
    }

    public function test_store_exception_is_handled()
    {
        
        $response = $this->post(route('bookings.store'), [
            'name' => 'Test Patient',
            'phoneNumber' => '0912345678',
            'dateOfBirth' => '1990-01-01',
            'gender' => 'male',
            'appointment_data' => 'wrong_data_format', // بيانات خاطئة عمداً
        ]);

        // يجب أن يمسك الكنترولر الخطأ ويرجع برسالة error بدلاً من الانهيار
        $response->assertSessionHas('error');
    }

        // ----------------------------------------------------
    // اختبار دالة العرض (Create)
    // ----------------------------------------------------
    public function test_create_returns_booking_form_view()
    {
        $response = $this->get(route('bookings.form'));

        $response->assertStatus(200);
        $response->assertViewIs('bookings.booking-form');
        $response->assertViewHas('doctorsData');
    }

    // ----------------------------------------------------
    // اختبارات التعديل (Edit & Update)
    // ----------------------------------------------------
    public function test_edit_returns_edit_view_for_valid_booking()
    {
        $patient = \App\Models\Patient::create(['patientName' => 'Test', 'phoneNumber' => '123']);
        $booking = \App\Models\Booking::create([
            'patientId' => $patient->id,
            'doctorId' => $this->doctor->doctorId,
            'appointmentDate' => '2026-10-10 10:00:00',
            'roomNumber' => '101',
            'status' => 'pending',
        ]);

        $response = $this->get(route('bookings.edit', ['id' => $booking->id]));

        $response->assertStatus(200);
        $response->assertViewIs('bookings.edit');
        $response->assertViewHas('booking');
    }

    public function test_edit_redirects_if_booking_is_cancelled()
    {
        $patient = \App\Models\Patient::create(['patientName' => 'Test', 'phoneNumber' => '123']);
        $booking = \App\Models\Booking::create([
            'patientId' => $patient->id,
            'doctorId' => $this->doctor->doctorId,
            'appointmentDate' => '2026-10-10 10:00:00',
            'roomNumber' => '101',
            'status' => 'cancelled',
        ]);

        $response = $this->get(route('bookings.edit', ['id' => $booking->id]));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'هذا الحجز ملغي ولا يمكن تعديله.');
    }

    public function test_update_modifies_booking_and_redirects()
    {
        $patient = \App\Models\Patient::create(['patientName' => 'Test', 'phoneNumber' => '123']);
        $booking = \App\Models\Booking::create([
            'patientId' => $patient->id,
            'doctorId' => $this->doctor->doctorId,
            'appointmentDate' => '2026-10-10 10:00:00',
            'roomNumber' => '101',
            'status' => 'pending',
        ]);

        $response = $this->put(route('bookings.update', ['id' => $booking->id]), [
            'roomNumber' => '202',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'تم تعديل بيانات الموعد بنجاح.');
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'roomNumber' => '202',
        ]);
    }

    // ----------------------------------------------------
    // اختبارات الإلغاء (Destroy)
    // ----------------------------------------------------
    public function test_destroy_cancels_pending_booking()
    {
        $patient = \App\Models\Patient::create(['patientName' => 'Test', 'phoneNumber' => '123']);
        $booking = \App\Models\Booking::create([
            'patientId' => $patient->id,
            'doctorId' => $this->doctor->doctorId,
            'appointmentDate' => '2026-10-10 10:00:00',
            'roomNumber' => '101',
            'status' => 'pending',
        ]);

        $response = $this->delete(route('bookings.destroy', ['id' => $booking->id]));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'تم إلغاء الموعد بنجاح.');
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled',
        ]);
    }

    // ----------------------------------------------------
    // اختبار دالة الاقتراح الذكي (Recommend Doctor)
    // ----------------------------------------------------
    public function test_recommend_doctor_returns_json()
    {
        $response = $this->get(route('api.recommend.doctor', [
            'age' => 25,
            'gender' => 'male'
        ]));

        // طباعة النتيجة على الشاشة لتوثيق التقرير
        dump('--- نتيجة الاقتراح الذكي ---');
        dump($response->json());

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message']);
        
        // التحقق من أن الاقتراح نجح فعلاً ورجع اسم الطبيب
        $response->assertJsonFragment([
            'success' => true,
            'doctor_name' => 'Test Doctor'
        ]);
    }

    public function test_recommend_doctor_returns_false_if_no_doctor_found()
    {
        $response = $this->get(route('api.recommend.doctor', [
            'age' => 5, // عمر صغير لتشغيل مصنع الأطفال الذي يبحث عن طبيب غير موجود في بيئة الاختبار
            'gender' => 'female'
        ]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'success' => false,
        ]);
    }

    public function test_recommend_doctor_handles_exception()
    {
        // محاكاة كائن Request لرمي خطأ برمجي وإجبار الـ catch على العمل
        $request = \Mockery::mock(\Illuminate\Http\Request::class);
        $request->shouldReceive('input')->andThrow(new \Exception('Forced Exception'));

        $controller = new \App\Http\Controllers\BookingController();
        $response = $controller->recommendDoctor($request);

        $this->assertFalse($response->getData()->success);
    }

    public function test_edit_handles_exception()
    {
        $response = $this->get(route('bookings.edit', ['id' => 9999]));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_update_modifies_appointment_data()
    {
        $patient = \App\Models\Patient::create(['patientName' => 'Test', 'phoneNumber' => '123']);
        $booking = \App\Models\Booking::create([
            'patientId' => $patient->id,
            'doctorId' => $this->doctor->doctorId,
            'appointmentDate' => '2026-10-10 10:00:00',
            'roomNumber' => '101',
            'status' => 'pending',
        ]);

        $response = $this->put(route('bookings.update', ['id' => $booking->id]), [
            'appointment_data' => $this->doctor->doctorId . '|12:00:00|2026-11-11',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'appointmentDate' => '2026-11-11 12:00:00',
        ]);
    }

    public function test_update_handles_exception()
    {
        $response = $this->put(route('bookings.update', ['id' => 9999]), [
            'roomNumber' => '202',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_destroy_fails_if_booking_status_is_invalid()
    {
        $patient = \App\Models\Patient::create(['patientName' => 'Test', 'phoneNumber' => '123']);
        $booking = \App\Models\Booking::create([
            'patientId' => $patient->id,
            'doctorId' => $this->doctor->doctorId,
            'appointmentDate' => '2026-10-10 10:00:00',
            'roomNumber' => '101',
            'status' => 'cancelled', // ملغي مسبقاً
        ]);

        $response = $this->delete(route('bookings.destroy', ['id' => $booking->id]));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_destroy_handles_exception()
    {
        $response = $this->delete(route('bookings.destroy', ['id' => 9999]));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_create_handles_exception()
    {
        // لإجبار الدالة على الاصطدام بخطأ (Exception)، سنضيف موعداً بصيغة وقت خاطئة تماماً
        // مما سيؤدي إلى انهيار مكتبة Carbon للوقت، ويجبر الكود على الذهاب إلى الـ catch
        \App\Models\DoctorSchedule::create([
            'doctorId' => $this->doctor->doctorId,
            'day' => 'الإثنين', 
            'startTime' => 'INVALID_TIME_FORMAT_TO_CRASH_CARBON',
            'endTime' => '14:00:00',
            'isAvailable' => true,
        ]);

        $response = $this->get(route('bookings.form'));
        
        // يجب أن يمسك الـ catch الخطأ ويعيد مصفوفة الأطباء فارغة كما برمجناها
        $response->assertStatus(200);
        $response->assertViewHas('doctorsData', []);
    }

    public function test_create_skips_past_time_slots()
    {
        // السفر عبر الزمن ليوم الأحد الساعة 11 صباحاً لكي تكون مواعيد 9 و 10 (في الماضي)
        $sunday = \Carbon\Carbon::parse('next Sunday 11:00:00');
        $this->travelTo($sunday);

        $response = $this->get(route('bookings.form'));
        $response->assertStatus(200);

        $this->travelBack();
    }

    public function test_edit_skips_past_time_slots()
    {
        $patient = \App\Models\Patient::create(['patientName' => 'Test', 'phoneNumber' => '123']);
        $booking = \App\Models\Booking::create([
            'patientId' => $patient->id,
            'doctorId' => $this->doctor->doctorId,
            'appointmentDate' => '2026-10-10 10:00:00',
            'roomNumber' => '101',
            'status' => 'pending',
        ]);

        $sunday = \Carbon\Carbon::parse('next Sunday 11:00:00');
        $this->travelTo($sunday);

        $response = $this->get(route('bookings.edit', ['id' => $booking->id]));
        $response->assertStatus(200);

        $this->travelBack();
    }

    public function test_destroy_handles_completed_booking()
    {
        $patient = \App\Models\Patient::create(['patientName' => 'Test', 'phoneNumber' => '123']);
        $booking = \App\Models\Booking::create([
            'patientId' => $patient->id,
            'doctorId' => $this->doctor->doctorId,
            'appointmentDate' => '2026-10-10 10:00:00',
            'roomNumber' => '101',
            'status' => 'completed/paid',
        ]);

        $response = $this->delete(route('bookings.destroy', ['id' => $booking->id]));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
