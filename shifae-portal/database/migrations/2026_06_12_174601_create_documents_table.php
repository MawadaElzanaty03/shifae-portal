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
        Schema::create('documents', function (Blueprint $table) {
            $table->id('documentId'); // المفتاح الأساسي
            //ربط المستند بجدول اليوزر
            $table->foreignId('userId')->constrained('users', 'userId')->onDelete('cascade'); 
            $table->string('documentName'); // اسم الملف (مثلاً: "السيرة الذاتية")
            $table->string('filePath'); // مسار حفظ الملف في السيرفر
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
