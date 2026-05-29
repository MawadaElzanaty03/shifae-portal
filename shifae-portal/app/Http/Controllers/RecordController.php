<?php

namespace App\Http\Controllers;
use App\Models\Patient;//نحتاجوه لعرض المريض
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
                'clinicalNotes' =>'لم يتم إدخال ملاحظات سريرية بعد.', // الملاحظات تكون فارغة مبدئياً
            ]);

            // إرجاع استجابة نجاح مع رقم السجل للتوجه لإضافة الملاحظات
            return redirect()->route('record.show',$validatedData ['patientId'])
                             ->with('success', 'تم إنشاء السجل الطبي بنجاح.');

        } catch (\Exception $recordError) {
            // في حال حدوث خطأ، نرجع رسالة توضح السبب الفعلي للمشكلة
            return back()->withErrors(['diagnosisError' => 'حدث خطأ: ' . $recordError->getMessage()])
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
           return redirect()->route('doctor.dashboard')
                             ->with('success', 'تم حفظ الملاحظات السريرية بنجاح.');

        } catch (Exception $updateError) {
            // التقاط أي خطأ غير متوقع أثناء الحفظ
            return back()->withErrors(['updateError' => 'فشل في الاتصال أو حفظ الملاحظات، يرجى المحاولة مرة أخرى.']);
        }
    }

    // دالة لجلب وعرض صفحة السجل الطبي للمريض المختار
public function showRecordPage($patientId)
{
    try {
        // جلب بيانات المريض بناءً على الرقم المعرّف أو إظهار خطأ 404 إن لم يكن موجوداً
        $patient = Patient::findOrFail($patientId);

        // البحث عن سجل طبي موجود مسبقاً لهذا المريض ومربوط بالطبيب الحالي
        $record = Record::where('patientId', $patientId)
                        ->where('doctorId', Auth::id())
                        ->first();

        // تمرير بيانات المريض والسجل (إن وجد) إلى واجهة الـ Blade
        return view('pataint.records', compact('patient', 'record'));

    } catch (Exception $viewError) {
        // التقاط أي خطأ وإعادة توجيه الطبيب للوحة التحكم مع رسالة تنبيه
        return redirect()->route('doctor.dashboard')
                         ->withErrors(['viewError' => 'حدث خطأ أثناء محاولة فتح ملف المريض.']);
    }
}
}