<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة شفائي - تسجيل الدخول</title>
    </head>
<body style="font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 50px;">

    <div style="max-width: 400px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; color: #333;">تسجيل الدخول لكادر شفائي</h2>

        @if($errors->any())
            <div style="background-color: #ffdddd; color: #d8000c; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                <ul style="margin: 0; padding-right: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf

            <div style="margin-bottom: 15px;">
                <label for="userName" style="display: block; margin-bottom: 5px; font-weight: bold;">اسم المستخدم:</label>
                <input type="text" id="userName" name="userName" value="{{ old('userName') }}" required 
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="password" style="display: block; margin-bottom: 5px; font-weight: bold;">كلمة المرور:</label>
                <input type="password" id="password" name="password" required 
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>

            <button type="submit" style="width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">
                دخول
            </button>
        </form>
    </div>

</body>
</html>