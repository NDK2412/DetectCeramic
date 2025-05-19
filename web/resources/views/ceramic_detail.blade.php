<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $ceramic->name }} - Ceramic Classification</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        :root {
            --primary-color: #b3cde0;
            --secondary-color: #6497b1;
            --accent-color: #e6f0fa;
            --light-color: #f5faff;
            --dark-color: #03396c;
            --text-light: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--light-color);
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            flex: 1;
        }

        header {
            background-color: var(--primary-color);
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            width: 100%;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            color: var(--dark-color);
            font-size: clamp(1.2rem, 2.5vw, 1.8rem);
            font-weight: 600;
            text-decoration: none;
            flex-shrink: 0;
        }

        .logo img {
            height: clamp(30px, 5vw, 40px);
            margin-right: 10px;
        }

        .nav-container {
            display: flex;
            align-items: center;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            gap: clamp(1rem, 2vw, 1.5rem);
        }

        .nav-menu li a {
            color: var(--dark-color);
            text-decoration: none;
            font-weight: 500;
            font-size: clamp(0.9rem, 1.5vw, 1rem);
            transition: color 0.3s ease;
        }

        .nav-menu li a:hover {
            color: var(--secondary-color);
        }

        .login-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .login-section button {
            padding: clamp(0.5rem, 1vw, 0.6rem) clamp(1rem, 2vw, 1.2rem);
            border: none;
            border-radius: 20px;
            font-weight: 500;
            font-size: clamp(0.8rem, 1.2vw, 1rem);
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        #loginButton {
            background-color: var(--secondary-color);
            color: var(--text-light);
        }

        #logoutButton {
            background-color: var(--dark-color);
            color: var(--text-light);
        }

        .login-section button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        }

        .hamburger {
            display: none;
            font-size: 2rem;
            background: none;
            border: none;
            color: var(--dark-color);
            cursor: pointer;
            padding: 0.5rem;
        }

        /* Detail Section */
        .detail-section {
            padding: 2rem 0;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .detail-section h1 {
            font-size: 2.5rem;
            color: var(--dark-color);
            text-align: center;
            margin-bottom: 2rem;
        }

        .detail-content {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .detail-image {
            flex: 1;
            min-width: 300px;
        }

        .detail-image img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            object-fit: cover;
        }

        .detail-info {
            flex: 1;
            min-width: 300px;
        }

        .detail-info p {
            font-size: 1.1rem;
            color: #666;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .detail-info a {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: var(--secondary-color);
            color: var(--text-light);
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .detail-info a:hover {
            background-color: var(--dark-color);

        }


        footer {
            text-align: center;
            padding: 1.5rem;
            background-color: var(--primary-color);
            color: var(--dark-color);
            margin-top: auto;
            width: 100%;
            font-size: clamp(0.8rem, 1.5vw, 1rem);
        }

        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }

            .nav-container {
                display: none;
                flex-direction: column;
                width: 100%;
                position: absolute;
                top: 100%;
                left: 0;
                background-color: var(--primary-color);
                padding: 1rem;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            }

            .nav-container.active {
                display: flex;
            }

            .nav-menu {
                flex-direction: column;
                width: 100%;
                gap: 1rem;
                text-align: center;
            }

            .nav-menu li {
                width: 100%;
            }

            .login-section {
                width: 100%;
                justify-content: center;
                flex-direction: column;
                gap: 0.8rem;
            }

            .login-section button {
                width: 100%;
            }

            .header-content {
                justify-content: space-between;
            }

            .detail-section {
                padding: 1.5rem;
            }

            .detail-section h1 {
                font-size: 1.5rem;
            }

            .detail-content {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 1rem;
            }

            .logo {
                font-size: 1.2rem;
            }

            .logo img {
                height: 25px;
            }

            .detail-section h1 {
                font-size: 1.2rem;
            }

            .detail-info p {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <div class="header-content">
                <a href="#" class="logo"><img src="http://localhost:8000/storage/ceramics/logo2.webp" alt="Logo">
                    Ceramic Classification
                </a>
                <button class="hamburger" aria-label="Toggle menu">☰</button>
                <div class="nav-container">
                    <ul class="nav-menu">
                        <li><a href="/">Trang chủ</a></li>
                        <li><a href="/gallery">Thư viện đồ gốm</a></li>
                        <li><a href="#" id="classificationLink">Nhận diện</a></li>
                        <li><a href="#market">Mua bán</a></li>
                        <li><a href="#" id="contactLink">Liên hệ</a></li>
                    </ul>
                </div>
            </div>
        </header>
        <!-- <div class="login-section">
            <button id="loginButton" onclick="redirectToLogin()">Try It Out <i class="fa-solid fa-arrow-up-from-bracket"></i></button>
            <button id="logoutButton" onclick="logout()" style="display:none;">Đăng xuất</button>
        </div> -->

        <!-- Detail Section -->
        <section class="detail-section">
            <h1>{{ $ceramic->name }}</h1>
            <div class="detail-content">
                <div class="detail-image">
                    <img src="{{ $ceramic->image ? asset('storage/' . $ceramic->image) : 'https://via.placeholder.com/400x300' }}"
                        alt="{{ $ceramic->name }}">
                </div>
                <div class="detail-info">
                    <p><strong>Danh mục:</strong> {{ $ceramic->category ?? 'Không có' }}</p>
                    <p><strong>Nguồn gốc:</strong> {{ $ceramic->origin ?? 'Không có' }}</p>
                    <p><strong>Mô tả:</strong> {!! $ceramic->description ?? 'Không có mô tả.' !!}</p>
                    <a href="{{ route('gallery') }}">Quay lại thư viện</a>
                </div>
            </div>
        </section>
    </div>

    <footer>
        <p>© 2023 Ceramic Classification System. All rights reserved.</p>
    </footer>

    <script>
        // Toggle menu hamburger
        const hamburger = document.querySelector('.hamburger');
        const navContainer = document.querySelector('.nav-container');
        const classificationLink = document.querySelector('#classificationLink');
        const loginPrompt = document.querySelector('#loginPrompt');
        const contactLink = document.querySelector('#contactLink');
        const contactSidebar = document.querySelector('#contactSidebar');

        hamburger.addEventListener('click', () => {
            navContainer.classList.toggle('active');
        });

        // Biến kiểm tra trạng thái đăng nhập
        let isAuthenticated = false;

        // Hàm kiểm tra trạng thái đăng nhập
        async function checkLoginStatus() {
            try {
                let response = await fetch("http://localhost:8000/api/check-auth", {
                    credentials: "include"
                });
                let data = await response.json();

                if (data.authenticated) {
                    isAuthenticated = true;
                    document.getElementById("loginButton").style.display = "none";
                    document.getElementById("logoutButton").style.display = "block";
                } else {
                    isAuthenticated = false;
                    document.getElementById("loginButton").style.display = "block";
                    document.getElementById("logoutButton").style.display = "none";
                }
            } catch (error) {
                console.error("Lỗi kiểm tra đăng nhập:", error);
            }
        }

        // Chuyển hướng đến trang đăng nhập
        function redirectToLogin() {
            window.location.href = "http://localhost:8000/login";
        }

        // Đăng xuất người dùng
        async function logout() {
            try {
                document.getElementById('logoutButton').innerHTML = '<span class="loading"></span> Processing...';
                await fetch("http://localhost:8000/api/logout", {
                    method: "POST",
                    credentials: "include"
                });
                window.location.reload();
            } catch (error) {
                console.error("Lỗi đăng xuất:", error);
                document.getElementById('logoutButton').textContent = 'Đăng xuất';
            }
        }

        // Kiểm tra trạng thái đăng nhập khi trang tải
        window.onload = checkLoginStatus;
    </script>
</body>

</html>