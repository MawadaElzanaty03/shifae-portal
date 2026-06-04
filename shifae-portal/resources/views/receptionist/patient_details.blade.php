@extends('receptionist.dashboard')

@section('content')
<div class="card">
    <h2>نتيجة البحث</h2>
    <p>هذه البيانات معروضة بناءً على صلاحيات موظف الاستقبال (ReceptionistSearchStrategy).</p>
    <hr>
    
    <!-- بيانات المريض -->
    <h3>بيانات المريض:</h3>
    <p><strong>اسم المريض:</strong> {{ $patientData->patientName }}</p>
    <p><strong>رقم الهاتف:</strong> {{ $patientData->phoneNumber }}</p>
    
        <hr>
    <h3>مواعيد المريض:</h3>
    @if($patientData->bookings && $patientData->bookings->count() > 0)
        <table style="width: 100%; text-align: right; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #ecf0f1;">
                    <th style="padding: 10px; border: 1px solid #bdc3c7;">رقم الحجز</th>
                    <th style="padding: 10px; border: 1px solid #bdc3c7;">الطبيب</th>
                    <th style="padding: 10px; border: 1px solid #bdc3c7;">تاريخ ووقت الموعد</th>
                    <th style="padding: 10px; border: 1px solid #bdc3c7;">الغرفة</th>
                    <th style="padding: 10px; border: 1px solid #bdc3c7;">الحالة</th>
                    <th style="padding: 10px; border: 1px solid #bdc3c7;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patientData->bookings as $booking)
                <tr>
                    <td style="padding: 10px; border: 1px solid #bdc3c7;">#{{ $booking->id }}</td>
                    <td style="padding: 10px; border: 1px solid #bdc3c7;">{{ $booking->doctor->user->fullName ?? 'غير محدد' }}</td>
                    <td style="padding: 10px; border: 1px solid #bdc3c7;">{{ $booking->appointmentDate }}</td>
                    <td style="padding: 10px; border: 1px solid #bdc3c7;">{{ $booking->roomNumber }}</td>
                    <td style="padding: 10px; border: 1px solid #bdc3c7;">
                        @if($booking->status == 'cancelled')
                            <span style="color: red; font-weight: bold;">ملغي</span>
                        @else
                            <span style="color: green; font-weight: bold;">{{ $booking->status }}</span>
                        @endif
                    </td>
                    <td style="padding: 10px; border: 1px solid #bdc3c7;">
                        @if($booking->status !== 'cancelled')
                            <!-- التعديل -->
                            <a href="{{ route('bookings.edit', $booking->id) }}" style="background-color: #f39c12; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; margin-left: 5px;">تعديل</a>
                            
                            <!-- الإلغاء -->
                            <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الموعد؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background-color: #e74c3c; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer;">إلغاء</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color: #7f8c8d;">لا توجد مواعيد مسجلة لهذا المريض.</p>
    @endif


    <div style="margin-top: 20px;">
        <a href="{{ route('receptionist.dashboard') }}" style="color: #007bb5; text-decoration: none;">&rarr; عودة للبحث من جديد</a>
    </div>
</div>
@endsection
