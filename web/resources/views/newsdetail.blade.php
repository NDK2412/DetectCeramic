<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $article->title }} - Ceramic Classification</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        :root {
            --primary-color: #b3cde0;
            --secondary-color: #6497b1;
            --dark-color: #03396c;
            --light-color: #f5faff;
            --text-light: #ffffff;
            --accent-color: #e6f0fa;
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
            height: clamp(50px, 5vw, 50px);
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
            margin-bottom: 10px;
            animation: bounce 1s infinite;
        }

        @keyframes bounce {
            0%,
            100% {
                transform: translateY(0);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }

            50% {
                transform: translateY(-12px);
                box-shadow: 0 15px 20px rgba(0, 0, 0, 0.2);
            }
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

        /* Cải thiện giao diện phần tin tức */
        .content-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .article-header {
            margin-bottom: 1.5rem;
        }

        h1 {
            font-size: 2rem;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .article-meta {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .article-meta span {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .article-meta i {
            color: var(--secondary-color);
        }

        .article-image {
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .article-image img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .article-image img:hover {
            transform: scale(1.02);
        }

        p.excerpt {
            font-size: 1rem;
            font-style: italic;
            color: #555;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: var(--accent-color);
            border-left: 4px solid var(--secondary-color);
            border-radius: 4px;
        }

        p.content {
            font-size: 1rem;
            line-height: 1.8;
            color: #333;
            margin-bottom: 2rem;
            text-align: justify;
        }

        a.back {
            display: inline-block;
            padding: 0.6rem 1.5rem;
            background-color: var(--secondary-color);
            color: var(--text-light);
            text-decoration: none;
            border-radius: 20px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        a.back:hover {
            background-color: var(--dark-color);
            transform: translateY(-2px);
        }

        .contact-sidebar {
            position: fixed;
            top: 0;
            right: -450px;
            width: 450px;
            height: 100%;
            background-color: var(--primary-color);
            color: var(--dark-color);
            padding: 2rem;
            z-index: 1500;
            transition: right 0.3s ease-in-out;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.2);
        }

        .contact-sidebar.active {
            overflow: scroll;
            right: 0;
        }

        .contact-sidebar h3 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .contact-sidebar ul {
            list-style: none;
        }

        .contact-sidebar li {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .contact-sidebar i {
            margin-right: 10px;
            font-size: 1.2rem;
            width: 20px;
            text-align: center;
        }

        .contact-sidebar a {
            color: var(--dark-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .contact-sidebar a:hover {
            color: var(--secondary-color);
        }

        .contact-form {
            margin-top: 1.5rem;
        }

        .contact-form h4 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: var(--secondary-color);
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 0.6rem;
            margin-bottom: 0.8rem;
            border: none;
            border-radius: 5px;
            background-color: var(--text-light);
            color: #333;
            font-size: 0.9rem;
        }

        .contact-form textarea {
            height: 100px;
            resize: none;
        }

        .contact-form button {
            background-color: rgb(5, 53, 66);
            color: var(--text-light);
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
        }

        .contact-form button:hover {
            background-color: var(--secondary-color);
            color: var(--dark-color);
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

            .contact-sidebar {
                width: 250px;
            }

            h1 {
                font-size: 1.5rem;
            }

            .article-meta {
                font-size: 0.8rem;
            }

            p.excerpt,
            p.content {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <div class="header-content">
                <a href="#" class="logo">
                    <img src="{{ asset('storage/ceramics/logo2.webp') }}" alt="Logo">
                    Ceramic Classification
                </a>
                <button class="hamburger" aria-label="Toggle menu">☰</button>
                <div class="nav-container">
                    <ul class="nav-menu">
                        <li><a href="/">Trang chủ</a></li>
                        <li><a href="/gallery">Thư viện đồ gốm</a></li>
                        <li><a href="/dashboard" id="classificationLink">Nhận diện</a></li>
                        <li><a href="#market">Mua bán</a></li>
                        <li><a href="#" id="contactLink">Liên hệ</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="content-container">
            <div class="article-header">
                <h1>{{ $article->title }}</h1>
                <div class="article-meta">
                    <span><i class="far fa-calendar-alt"></i> {{ $article->created_at->format('d/m/Y H:i') }}</span>
                    @if ($article->source_url)
                        <span><i class="fas fa-link"></i> <a href="{{ $article->source_url }}" target="_blank">Nguồn bài viết</a></span>
                    @endif
                </div>
            </div>

            @if ($article->image)
                <div class="article-image">
                    <img src="{{ url($article->image) }}" alt="{{ $article->title }}">
                </div>
            @endif

            @if ($article->excerpt)
                <p class="excerpt">{{ $article->excerpt }}</p>
            @endif

            <p class="content">{{ $article->content }}</p>

            <a href="{{ route('home') }}" class="back">Quay lại trang chủ</a>
        </div>
    </div>

    <!-- Contact Sidebar -->
    <div class="contact-sidebar" id="contactSidebar">
        <h3>Liên hệ với chúng tôi</h3>
        <ul>
            <li><i class="fas fa-phone"></i> <span>SĐT: 0982638519</span></li>
            <li><i class="fas fa-envelope"></i> <a href="mailto:khangkhang1111777@gmail.com">Email:
                    khangkhang1111777@gmail.com</a></li>
            <li><i class="fab fa-facebook-f"></i> <a href="https://facebook.com/ceramic" target="_blank">Facebook</a>
            </li>
            <li><i class="fab fa-instagram"></i> <a href="https://instagram.com/ceramic" target="_blank">Instagram</a>
            </li>
            <li><i class="fa-brands fa-x-twitter"></i> <a href="https://twitter.com/ceramic" target="_blank">Twitter</a>
            </li>
            <li><i class="fas fa-map-marker-alt"></i> <span>Địa chỉ: 123 Đường Gốm, TP. Hà Nội</span></li>
        </ul>
        <div class="contact-form">
            <h4>Gửi liên hệ</h4>
            <form id="contactForm" method="POST" action="{{ route('contact.submit') }}">
                @csrf
                <h8>Nhập họ tên:</h8>
                <input type="text" name="name" placeholder="Họ tên" required>
                <h8>Nhập SĐT:</h8>
                <input type="tel" name="phone" placeholder="Số điện thoại" required>
                <h8>Nhập email:</h8>
                <input type="email" name="email" placeholder="Email" required>
                <h8>Nhập nội dung:</h8>
                <textarea name="message" placeholder="Nội dung" required></textarea>
                <button type="submit">Gửi</button>
            </form>
        </div>
    </div>

    <footer>
        <p>© 2023 Ceramic Classification System. All rights reserved.</p>
    </footer>

    <script>
        const hamburger = document.querySelector('.hamburger');
        const navContainer = document.querySelector('.nav-container');
        const contactLink = document.querySelector('#contactLink');
        const contactSidebar = document.querySelector('#contactSidebar');
        let isAuthenticated = false;

        hamburger.addEventListener('click', () => {
            navContainer.classList.toggle('active');
        });

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

        function redirectToLogin() {
            window.location.href = "http://localhost:8000/login";
        }

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

        contactLink.addEventListener('click', (e) => {
            e.preventDefault();
            contactSidebar.classList.add('active');
        });

        document.addEventListener('click', (e) => {
            if (contactSidebar.classList.contains('active') &&
                !contactSidebar.contains(e.target) &&
                e.target !== contactLink &&
                !navContainer.contains(e.target)) {
                contactSidebar.classList.remove('active');
            }
        });

        const contactForm = document.getElementById('contactForm');
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(contactForm);

            try {
                const response = await fetch("{{ route('contact.submit') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();
                if (response.ok) {
                    alert('Thông tin liên hệ đã được gửi thành công!');
                    contactForm.reset();
                    contactSidebar.classList.remove('active');
                } else {
                    alert('Có lỗi xảy ra: ' + result.message);
                }
            } catch (error) {
                console.error('Lỗi gửi liên hệ:', error);
                alert('Không thể gửi liên hệ. Vui lòng thử lại sau.');
            }
        });

        window.onload = checkLoginStatus;
    </script>
</body>

</html>