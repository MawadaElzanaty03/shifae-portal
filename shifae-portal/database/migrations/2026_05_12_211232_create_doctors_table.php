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
        Schema::create('doctors', function (Blueprint $table) {
        $table->id('doctorId'); // هذا حيكون الـ doctor_id اللي بنربطوا بيه بعدين
        
        // الربط بجدول المستخدمين (هذي هي الوراثة اللي في الرسمة)
        $table->foreignId('userId')->constrained('users')->onDelete('cascade');
        
        // الخصائص الخاصة بالدكتور من الكلاس دايجرام
        $table->string('specialty'); // التخصص
        $table->decimal('profitPercentage', 5, 2)->default(60.00); // نسبة الربح
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
