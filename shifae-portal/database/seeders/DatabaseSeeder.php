<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Room;
use App\Models\Booking;
use App\Models\DoctorSchedule;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. إنشاء مدير النظام
        User::create([
            'userName' => 'admin',
            'fullName' => 'مدير النظام',
            'email' => 'admin@shifae.com',
            'password' => Hash::make('12345678'),
            'userRole' => 'Admin',
            'gender' => 'male'
        ]);

        // إنشاء موظف الاستقبال
        User::create([
            'userName' => 'reception',
            'fullName' => 'موظف الاستقبال',
            'email' => 'reception@shifae.com',
            'password' => Hash::make('12345678'),
            'userRole' => 'Receptionist',
            'gender' => 'female'
        ]);

        // 2. إنشاء بعض غرف العيادة
        $rooms = [];
        for ($i = 101; $i <= 105; $i++) {
            $rooms[] = Room::create([
                'roomNumber' => (string) $i,
                'roomStatus' => 'Available',
            ]);
        }

        // 3. إنشاء بعض المرضى
        $patients = [];
        for ($i = 1; $i <= 5; $i++) {
            $patients[] = Patient::create([
                'patientName' => 'مريض ' . $i,
                'phoneNumber' => '092000000' . $i,
                'dateOfBirth' => Carbon::now()->subYears(rand(10, 60))->format('Y-m-d'),
                'gender' => $i % 2 == 0 ? 'male' : 'female',
            ]);
        }

        // 4. إنشاء بعض الأطباء مع مستخدميهم
        $doctors = [];
        // التخصصات
        $specialties = ['اخصائي عام', 'تعديل سلوك', 'طبيب نفسي'];
        foreach ($specialties as $index => $specialty) {
            $user = User::create([
                'userName' => 'doctor_' . $index,
                'fullName' => 'دكتور ' . $specialty,
                'email' => 'doctor' . $index . '@shifae.com',
                'password' => Hash::make('12345678'),
                'userRole' => 'Doctor',
                'gender' => $index % 2 == 0 ? 'male' : 'female'
            ]);

            $doctor = Doctor::create([
                'userId' => $user->userId,
                'specialty' => $specialty,
                'profitPercentage' => 50,
            ]);
            $doctors[] = $doctor;

            // 5. إنشاء جدول مواعيد لكل طبيب
            DoctorSchedule::create([
                'doctorId' => $doctor->doctorId,
                'day' => 'الأحد',
                'startTime' => '09:00:00',
                'endTime' => '14:00:00',
                'isAvailable' => true,
            ]);
        }

        // 6. إنشاء بعض الحجوزات للمرضى
        foreach ($patients as $index => $patient) {
            Booking::create([
                'patientId' => $patient->id,
                'doctorId' => $doctors[array_rand($doctors)]->doctorId,
                'appointmentDate' => Carbon::now()->addDays(rand(1, 10))->format('Y-m-d 10:00:00'),
                'roomNumber' => $rooms[array_rand($rooms)]->id,
                'status' => 'pending',
                'amount_paid' => rand(50, 200),
                'payment_method' => 'Cash',
            ]);
        }
    }
}
