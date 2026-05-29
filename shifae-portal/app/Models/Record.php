<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;
    // تحديد الحقول القابلة للإدخال
    protected $fillable = [
        'patientId',
        'doctorId',
        'diagnosis',
        'clinicalNotes',
    ];
    protected $primaryKey = 'recordId';
    public function patient()
    {
        try {
            return $this->belongsTo(Patient::class, 'patientId');
        } catch (\Exception $recordError) {
            // معالجة الخطأ في حال فشل الربط
            \Log::error('Error in Record-Patient relationship: ' . $recordError->getMessage());
        }
    }

    // ربط السجل بالطبيب (علاقة 1 إلى متعدد)
    public function doctor()
    {
        try {
            return $this->belongsTo(Doctor::class, 'doctorId');
        } catch (\Exception $recordError) {
            \Log::error('Error in Record-Doctor relationship: ' . $recordError->getMessage());
        }
    }
}
