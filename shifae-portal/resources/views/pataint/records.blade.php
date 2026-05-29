@extends('layouts.app') @section('content')
<div class="container mt-4">
    <h2>إدارة السجلات الطبية للمريض</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(!isset($record))
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            إنشاء سجل طبي جديد
        </div>
        <div class="card-body">
            <form action="{{ route('record.create') }}" method="POST">
                @csrf
                <input type="hidden" name="patientId" value="{{ $patient->id }}">

                <div class="form-group mb-3">
                    <label for="diagnosis" class="form-label">التشخيص المبدئي:</label>
                    <input type="text" name="diagnosis" id="diagnosis" class="form-control" placeholder="أدخل تشخيص الحالة هنا..." value="{{ old('diagnosis') }}" required>
                </div>

                <button type="submit" class="btn btn-primary">حفظ التشخيص وفتح السجل</button>
            </form>
        </div>
    </div>
    @endif

    @if(isset($record))
    <div class="card">
        <div class="card-header bg-success text-white">
            الملاحظات السريرية وتفاصيل الجلسة
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>التشخيص الحالي:</strong> {{ $record->diagnosis }}
            </div>

            <form action="{{ route('record.updateNotes', $record->recordId) }}" method="POST">
                @csrf
                @method('PUT') <div class="form-group mb-3">
                    <label for="clinicalNotes" class="form-label">تفاصيل الجلسة والملاحظات:</label>
                    <textarea name="clinicalNotes" id="clinicalNotes" class="form-control" rows="6" placeholder="اكتب ملاحظات الجلسة هنا..." required>{{ old('clinicalNotes', $record->clinicalNotes) }}</textarea>
                </div>

                <button type="submit" class="btn btn-success">حفظ السجل الطبي (حفظ كمسودة/نهائي)</button>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection