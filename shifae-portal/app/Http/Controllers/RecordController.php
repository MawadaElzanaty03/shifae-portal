<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Record;
use Exception;
use Illuminate\Support\Facades\Auth;

class RecordController extends Controller
{
    // دالة إنشاء سجل طبي جديد للمريض باستخدام التشخيص
    public function createRecord(Request $request)
    {
        // استخدام try/catch لتجنب انهيار النظام ومعالجة الأخطاء
        try {
            // التحقق من صحة البيانات المدخلة واجبار إدخال التشخيص
            $validatedData = $request->validate([
                'patientId' => 'required|integer|exists:patients,id',
                'diagnosis' => 'required|string|max:500',
            ]);

            // جلب معرف الطبيب المسجل حالياً في النظام
            $currentDoctorId = Auth::id();

            // إنشاء السجل الطبي الجديد
            $newRecord = Record::create([
                'patientId' => $validatedData['patientId'],
                'doctorId' => $currentDoctorId,
                'diagnosis' => $validatedData['diagnosis'],
                'clinicalNotes' => null, // الملاحظات تكون فارغة مبدئياً
            ]);

            // إرجاع استجابة نجاح مع رقم السجل للتوجه لإضافة الملاحظات
            return redirect()->route('doctor.records.show', ['recordId' => $newRecord->id])
                             ->with('success', 'تم إنشاء السجل الطبي بنجاح.');

        } catch (Exception $recordError) {
            // في حال عدم وجود تشخيص أو حدوث خطأ، نرجع رسالة خطأ
            return back()->withErrors(['diagnosisError' => 'الرجاء إدخال التشخيص بشكل صحيح.'])
                         ->withInput();
        }
    }

    // دالة تحديث السجل الطبي وإضافة الملاحظات السريرية
    public function updateNotes(Request $request, $recordId)
    {
        try {
            // التحقق من صحة الملاحظات المدخلة
            $validatedNotes = $request->validate([
                'clinicalNotes' => 'required|string',
            ]);

            // البحث عن السجل الطبي المطلوب
            $targetRecord = Record::findOrFail($recordId);

            // تحديث الملاحظات السريرية
            $targetRecord->update([
                'clinicalNotes' => $validatedNotes['clinicalNotes'],
            ]);

            // إرجاع رسالة تأكيد الحفظ بنجاح
            return back()->with('success', 'تم حفظ الملاحظات السريرية بنجاح.');

        } catch (Exception $updateError) {
            // التقاط أي خطأ غير متوقع أثناء الحفظ
            return back()->withErrors(['updateError' => 'فشل في الاتصال أو حفظ الملاحظات، يرجى المحاولة مرة أخرى.']);
        }
    }
}