@extends('doctor.dashboard')

@section('content')
<div class="card">
    <h2>نتيجة البحث (بصلاحيات الطبيب)</h2>
    <p>هذه البيانات معروضة بناءً على دالة DoctorSearchStrategy.</p>
    <hr>
    
    <!-- عرض بيانات المريض -->
    <h3>بيانات المريض الأساسية:</h3>
    <p><strong>اسم المريض:</strong> {{ $patientData->patientName }}</p>
    <p><strong>تاريخ الميلاد:</strong> {{ $patientData->dateOfBirth }}</p>
    <p><strong>الجنس:</strong> {{ $patientData->gender == 'male' ? 'ذكر' : 'أنثى' }}</p>

    <!-- زر عرض السجلات الطبية -->
    <button onclick="toggleRecords()" style="background-color: #2c3e50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-family: 'Tajawal', sans-serif;">
        عرض السجلات الطبية
    </button>

    <!-- قسم السجلات الطبية (مخفي افتراضياً) -->
    <div id="medicalRecords" style="display: none; margin-top: 15px; background: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">
        <h3 style="color: #c0392b;">السجلات الطبية للمريض:</h3>
        
        @if($patientData->records && $patientData->records->count() > 0)
            <table style="width: 100%; border-collapse: collapse; text-align: right;">
                <thead>
                    <tr style="background-color: #ddd;">
                        <th style="padding: 10px; border: 1px solid #ccc;">التاريخ</th>
                        <th style="padding: 10px; border: 1px solid #ccc;">التشخيص (Diagnosis)</th>
                        <th style="padding: 10px; border: 1px solid #ccc;">الملاحظات (Notes)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patientData->records as $record)
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ccc;">{{ $record->created_at->format('Y-m-d') }}</td>
                            <td style="padding: 10px; border: 1px solid #ccc;">{{ $record->diagnosis }}</td>
                            <td style="padding: 10px; border: 1px solid #ccc;">{{ $record->clinicalNotes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color: #7f8c8d;">لا توجد سجلات طبية سابقة لهذا المريض.</p>
        @endif
    </div>

    <!-- كود بسيط لإظهار وإخفاء السجلات عند الضغط على الزر -->
    <script>
        function toggleRecords() {
            var recordsDiv = document.getElementById('medicalRecords');
            if (recordsDiv.style.display === "none") {
                recordsDiv.style.display = "block";
            } else {
                recordsDiv.style.display = "none";
            }
        }
    </script>
    <div style="margin-top: 20px;">
        <!-- زر العودة يرجعنا للداشبورد ويمكننا إضافة كود JS بسيط ليفتح واجهة البحث فوراً إذا أردنا -->
        <a href="{{ route('doctor.dashboard') }}" style="color: #007bb5; text-decoration: none;">&rarr; عودة للوحة الرئيسية</a>
    </div>
</div>
@endsection
