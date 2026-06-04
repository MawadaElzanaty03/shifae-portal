@extends('receptionist.dashboard')

@section('content')
<div class="card" style="max-width: 700px; margin: 0 auto;">
    <h2>تعديل الموعد رقم #{{ $booking->id }}</h2>
    <hr>
    
    <!-- بيانات المريض الحالية -->
    <div style="background-color: #f1f6f9; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-right: 4px solid #007bb5;">
        <h4 style="margin-top: 0;">بيانات المريض والموعد الحالي:</h4>
        <p><strong>اسم المريض:</strong> {{ $booking->patient->patientName }}</p>
        <p><strong>رقم الهاتف:</strong> {{ $booking->patient->phoneNumber }}</p>
        <p><strong>الطبيب الحالي:</strong> د. {{ $booking->doctor->user->fullName ?? 'غير محدد' }}</p>
        <p><strong>الموعد الحالي:</strong> <span style="color: #c0392b; font-weight: bold;">{{ $booking->appointmentDate }}</span></p>
    </div>

    <!-- نموذج التعديل -->
    <form action="{{ route('bookings.update', $booking->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- اختيار الموعد -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 8px;">اختر الموعد الجديد (اختياري):</label>
            <select name="appointment_data" style="width: 100%; padding: 12px; border-radius: 4px; border: 1px solid #ccc; font-family: 'Tajawal', sans-serif;">
                <option value="">-- الإبقاء على نفس الموعد والطبيب الحالي --</option>
                
                @if(!empty($availableSlots))
                    @foreach($availableSlots as $doctorName => $slots)
                        <optgroup label="د. {{ $doctorName }}">
                            @foreach($slots as $slot)
                                <!-- بيانات الموعد المدمجة للباك إند -->
                                <option value="{{ $slot['doctorId'] }}|{{ $slot['fullTime'] }}|{{ $slot['bookingDate'] }}">
                                    {{ $slot['dateLabel'] }} - الساعة {{ $slot['timeLabel'] }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                @else
                    <option disabled>عذراً، لا توجد مواعيد متاحة حالياً.</option>
                @endif

            </select>
            <small style="color: #7f8c8d; display: block; margin-top: 5px;">إذا لم تختر أي موعد جديد من القائمة، سيبقى الموعد الحالي كما هو.</small>
        </div>

        <!-- رقم الغرفة -->
        <div style="margin-bottom: 25px;">
            <label style="display: block; font-weight: bold; margin-bottom: 8px;">رقم الغرفة:</label>
            <input type="text" name="roomNumber" value="{{ $booking->roomNumber }}" style="width: 100%; padding: 12px; border-radius: 4px; border: 1px solid #ccc; font-family: 'Tajawal', sans-serif;">
        </div>

        <!-- الأزرار -->
        <div style="text-align: left; margin-top: 20px;">
            <a href="javascript:history.back()" style="background-color: #95a5a6; color: white; padding: 12px 20px; text-decoration: none; border-radius: 4px; margin-left: 10px;">إلغاء والعودة</a>
            <button type="submit" style="background-color: #27ae60; color: white; border: none; padding: 12px 25px; border-radius: 4px; cursor: pointer; font-weight: bold; font-family: 'Tajawal', sans-serif;">حفظ التعديلات</button>
        </div>
    </form>
</div>
@endsection
