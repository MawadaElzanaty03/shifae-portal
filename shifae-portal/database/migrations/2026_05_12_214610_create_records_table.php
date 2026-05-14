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
        Schema::create('records', function (Blueprint $table) {
            $table->id('recordId');// recordId
        // - foreignId('patient_id'): يصنع خانة للرقم التعريفي للمريض.
        // - constrained('patients'): يتأكد إن الرقم هذا موجود فعلاً في جدول المرضى.
        // - onDelete('cascade'): لو مسحنا ملف المريض من المنظومة، تنمسح معاه كل سجلاته تلقائياً باش ما تقعدش بيانات عشوائية.
        $table->foreignId('patientId')->constrained('patients')->onDelete('cascade');
       // هذا السطر يربط السجل الطبي بالدكتور المعالج .
       // لو تم حذف الدكتور من المنظومة، تنحذف السجلات المرتبطة بيه (ممكن تغييرها لاحقاً لو تبي تحتفظي بالسجلات).
        $table->foreignId('doctorId')->constrained('doctors')->onDelete('cascade');
        
        // خصائص السجل
        $table->string('diagnosis');//خانة لتشخيص المبدئي
        $table->text('clinicalNotes');//لتسجيل النصوص الطويلة(فقرات كاملة عن حالة المريض)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};
