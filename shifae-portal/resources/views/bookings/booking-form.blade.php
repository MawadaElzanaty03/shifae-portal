<form action="{{ route('bookings.store') }}" method="POST">
    @csrf <div class="form-group">
        <label>الاسم الكامل</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="form-group">
        <label>رقم الهاتف</label>
        <input type="text" name="phoneNumber" class="form-control" required>
    </div>

    <div class="form-group">
        <label>العمر</label>
        <input type="number" name="age" class="form-control" required>
    </div>

    <div class="form-group">
        <label>اختر الطبيب والموعد المتاح</label>
        <select name="scheduleId" class="form-control" required>
            @foreach($doctors as $doctor)
                <optgroup label="د. {{ $doctor->user->name }} - {{ $doctor->specialty }}">
                    @foreach($doctor->schedules as $schedule)
                        <option value="{{ $schedule->scheduleId }}">
                            {{ $schedule->day }} | من {{ $schedule->startTime }} إلى {{ $schedule->endTime }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
    </div>

    <input type="hidden" name="roomNumber" value="101">

    <button type="submit" class="btn btn-primary">تأكيد الحجز اللحظي</button>
</form>