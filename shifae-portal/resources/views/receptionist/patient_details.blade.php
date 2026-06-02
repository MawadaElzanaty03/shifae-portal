@extends('receptionist.dashboard')

@section('content')
<div class="card">
    <h2>نتيجة البحث</h2>
    <p>هذه البيانات معروضة بناءً على صلاحيات موظف الاستقبال (ReceptionistSearchStrategy).</p>
    <hr>
    
    <!-- هنا نقوم بعرض المتغير الذي أرسلته دالة البحث patientData$ -->
    <h3>بيانات المريض:</h3>
    <p><strong>اسم المريض:</strong> {{ $patientData->patientName }}</p>
    <p><strong>رقم الهاتف:</strong> {{ $patientData->phoneNumber }}</p>
    
    

    <div style="margin-top: 20px;">
        <a href="{{ route('receptionist.dashboard') }}" style="color: #007bb5; text-decoration: none;">&rarr; عودة للبحث من جديد</a>
    </div>
</div>
@endsection
