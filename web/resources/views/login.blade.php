<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        // Lấy metadata cho trang admin
        $metadataForAdmin = App\Models\Metadata::where('page', 'login')->first();
        // Lấy APK mới nhất (giữ nguyên nếu cần)
        $latestApk = App\Models\Apk::latest()->first();
    @endphp

    <!-- Sử dụng metadata cho title, description, keywords, favicon -->
    <title>{{ $metadataForAdmin->title ?? 'Trang chủ' }}</title>
    <meta name="description" content="{{ $metadataForAdmin->description ?? '' }}">
    <meta name="keywords" content="{{ $metadataForAdmin->keywords ?? '' }}">

    @if ($metadataForAdmin->favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/images/' . $metadataForAdmin->favicon) }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @if (isset($recaptchaEnabled) && $recaptchaEnabled)
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 0 0 rgba(24, 119, 242, 0.5);
            }

            50% {
                box-shadow: 0 0 15px rgba(24, 119, 242, 0.5);
            }
        }

        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(to bottom right, #4facfe, rgb(255, 255, 255));
            margin: 0;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        h2 {
            color: #1a1a1a;
            margin-bottom: 10px;
            font-size: 28px;
            font-weight: bold;
        }

        .welcome-text {
            color: #606770;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        form div {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #606770;
            font-weight: 500;
        }

        .input-group {
            position: relative;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 1px solid #dddfe2;
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box;
            transition: all 0.3s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #1877f2;
            box-shadow: 0 0 8px rgba(24, 119, 242, 0.5);
            outline: none;
        }

        .input-group i {
            position: absolute;
            left: 12px;
            top: 65%;
            transform: translateY(-50%);
            color: #606770;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #1877f2;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 15px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        button:hover {
            background: #166fe5;
            animation: pulse 1s infinite;
        }

        button i {
            font-size: 18px;
        }

        .google-login-btn {
            background: #ffffff;
            color: #4285f4;
            border: 1px solid #4285f4;
            margin-top: 10px;
        }

        .google-login-btn:hover {
            background: #f1f1f1;
            animation: pulse 1s infinite;
        }

        .links {
            margin-top: 20px;
            text-align: center;
        }

        a {
            color: #1877f2;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        a:hover {
            text-decoration: underline;
            color: #145dbf;
        }

        .divider {
            border-top: 1px solid #dddfe2;
            margin: 20px 0;
            animation: fadeIn 1s ease-in-out;
        }

        .alert-danger {
            color: #dc3545;
            background: #f8d7da;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            animation: fadeIn 0.5s ease-in-out;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 15px;
        }

        .remember-me input[type="checkbox"] {
            width: auto;
            padding: 0;
            margin: 0;
        }

        .remember-me label {
            margin-bottom: 0;
            font-weight: normal;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>LOG IN</h2>
        <div class="welcome-text">
            <h3>Welcome!</h3>
            <p>Log in to use all features of the site</p>
        </div>

        <div class="divider"></div>

        <!-- Hiển thị thông báo thành công -->
        @if (session('success'))
            <div class="alert alert-success"
                style="color: #155724; background: #d4edda; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                <i class="fas fa-envelope"></i>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <i class="fas fa-lock"></i>
            </div>

            <!-- Remember Me Checkbox -->
            <div class="links">
                <a href="{{ route('password.change.form') }}">Change password?</a>
            </div>

            @if ($recaptchaEnabled)
                <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                @error('g-recaptcha-response')
                    <!-- <div class="alert alert-danger">{{ $message }}</div> -->
                @enderror
            @endif

            <button type="submit">
                <i class="fas fa-sign-in-alt"></i> LOG IN
            </button>
        </form>

        <!-- Google Login Button -->
        <a href="{{ route('auth.google') }}">
            <button class="google-login-btn">
                <i class="fab fa-google"></i>Sign in with Google
            </button>
        </a>

        <p style="margin-top: 20px; color: #606770;">Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
    </div>
</body>

</html>