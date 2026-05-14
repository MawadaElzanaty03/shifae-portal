<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
        $table->id();
            // ربط الحجز بمريض معين (علاقة Foreign Key)
        $table->foreignId('patientId')->constrained('patients')->onDelete('cascade');
         $table->foreignId('doctorId')->constrained('doctors')->onDelete('cascade');// لربط بالطبيب من يكون
         // بيانات الحجز الأساسية
        $table->dateTime('appointmentDate'); // تاريخ ووقت الموعد
        $table->string('roomNumber');        // رقم الحجرة
        $table->string('status')->default('pending'); // حالة الموعد (معلق، مؤكد، ملغي)
        //ديفولت ليها انه معلق و هذي الي بنبرمج علي اساسها الدوال يتحول لموعد مأكد عند الدفع
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
