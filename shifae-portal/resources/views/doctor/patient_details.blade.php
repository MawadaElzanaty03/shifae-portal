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

    <!-- يمكنك لاحقاً عرض السجلات الطبية (Medical Records) هنا لأنها من صلاحيات الطبيب -->
    
    <div style="margin-top: 20px;">
        <!-- زر العودة يرجعنا للداشبورد ويمكننا إضافة كود JS بسيط ليفتح واجهة البحث فوراً إذا أردنا -->
        <a href="{{ route('doctor.dashboard') }}" style="color: #007bb5; text-decoration: none;">&rarr; عودة للوحة الرئيسية</a>
    </div>
</div>
@endsection
