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
        Schema::create('staff_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userId')->references('userId')->on('users')->onDelete('cascade');//ربط المعرف مع جدول المستخدمين و الحذف التتابعي اجنبي 
            $table->string('phoneNumber')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_contacts');
    }
};
