<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khôi phục mật khẩu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* CSS bạn đã viết */
    </style>
</head>
<body>
    <div class="reset-box">
        <h2>KHÔI PHỤC MẬT KHẨU</h2>

        @if (session('status'))
            <div class="message">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">Email của bạn</label>
                <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu mới</label>
                <input id="password" type="password" name="password" required>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Xác nhận mật khẩu</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>

            <div class="actions">
                <button type="submit">Đặt lại mật khẩu</button>
            </div>
        </form>
    </div>
</body>
</html>
