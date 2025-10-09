@php
    use Illuminate\Support\Facades\Route;
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ĐĂNG NHẬP HỆ THỐNG</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: url('/images/university.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            background: linear-gradient(to bottom right, #edf2faff, #f8e8f1ff, #ece0b9ff);
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-box h2 {
            text-align: center;
            color: #4f46e5;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #333;
        }

        input[type="email"],
        input[type="password"] {
            width: 94%;
            padding: 10px 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            transition: 0.3s;
        }
        input[type="password"],
input[type="text"] {
    width: 94%;
    padding: 10px 14px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
    transition: 0.3s;
}
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #6366f1;
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .checkbox-group input {
            margin-right: 8px;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .actions a {
            font-size: 14px;
            color: #4f46e5;
            text-decoration: none;
        }

        .actions a:hover {
            text-decoration: underline;
        }

        button {
            background-color: #4f46e5;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
        }

        button:hover {
            background-color: #4338ca;
        }

        .error-message {
            color: red;
            font-size: 13px;
            margin-top: 5px;
        }

        .google-login {
            text-align: center;
            margin-top: 20px;
        }

        .google-login img {
            width: 220px;
            transition: 0.3s;
        }
        .login-header {
    display: flex;
    align-items: center;
    justify-content: center; /* căn giữa theo chiều ngang */
    gap: 10px;
    margin-bottom: 20px;
}
        .login-header h2 {
            margin: 0;
            font-size: 24px;
        }

        .login-logo {
            width: 40px; /* chỉnh kích thước theo ý bạn */
            height: auto;
            margin-left: 10px;
        }
        .google-login img:hover {
            transform: scale(1.05);
        }
        .remember-forgot {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 15px;
    margin-bottom: 10px;
}

.forgot-link {
    font-size: 14px;
    color: #4f46e5;
    text-decoration: none;
}
.actions {
    display: flex;
    justify-content: center; /* căn giữa nút */
    margin-top: 20px;
}
.forgot-link:hover {
    text-decoration: underline;
}
.google-login {
    text-align: center;
    margin-top: 20px;
}

.google-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover; /* Giúp ảnh luôn vừa khung vuông, không méo */
    transition: transform 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}


.google-icon:hover {
    transform: scale(1.1);
}
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-header">
            <img src="{{ asset('education.png') }}" alt="Logo" class="login-logo">
        <h2>ĐĂNG NHẬP</h2>
        
        </div>
        
        @if (session('status'))
        <div class="error-message">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group" style="position: relative;">
    <label for="password">Mật khẩu</label>
    <input id="password" type="password" name="password" required>
    <img id="togglePassword" 
         src="{{ asset('eye.png') }}" 
         alt="Hiện/Ẩn mật khẩu" 
         style="
            position: absolute;
            bottom: 10px;      /* căn sát đáy input, có thể chỉnh lại giá trị này */
            right: 12px;
            transform: translateY(0);
            width: 24px;
            height: 24px;
            cursor: pointer;
            user-select: none;
         ">
    @error('password')
        <div class="error-message">{{ $message }}</div>
    @enderror
</div>


        <div class="remember-forgot">
    <div class="checkbox-group">
        <input type="checkbox" id="remember" name="remember">
        <label for="remember">Ghi nhớ đăng nhập</label>
    </div>
    @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}" class="forgot-link">Quên mật khẩu?</a>
    @endif
</div>

<div class="actions">
    <button type="submit">Đăng nhập</button>
</div>
    </form>

        <!-- Đăng nhập bằng Google -->
       <div class="google-login">
            <p>Hoặc</p>
            <a href="{{ url('auth/google') }}">
                <img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png" alt="Đăng nhập bằng Google">
            </a>
        </div>
    </div>
   <script>
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#password');

    togglePassword.addEventListener('click', function () {
        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

        // Thay đổi ảnh mắt mở / đóng
        this.src = isPassword 
            ? "{{ asset('uneye.png') }}" 
            : "{{ asset('eye.png') }}";
    });
</script>

</body>
</html>
