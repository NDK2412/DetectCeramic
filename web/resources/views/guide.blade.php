<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hướng Dẫn Sử Dụng - Ceramic Recognition</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        :root {
            --primary-color: rgb(38, 70, 82);
            --secondary-color: rgb(118, 218, 236);
            --accent-color: #eceff1;
            --light-color: #f5f7fa;
            --dark-color: #263238;
            --success-color: #00c853;
            --gradient: radial-gradient(circle, var(--secondary-color), var(--primary-color));
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--gradient);
            color: var(--dark-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Header */
        .header {
            background: white;
            padding: 20px;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .header h1 {
            font-size: 2.2rem;
            font-weight: 600;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .home-link {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color 0.3s;
        }

        .home-link:hover {
            color: var(--secondary-color);
        }

        /* Container */
        .container {
            margin: auto;
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 1200px;
        }

        /* Section */
        .section {
            background: var(--light-color);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .section:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .section h2 {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section h2 i {
            color: var(--secondary-color);
            font-size: 1.8rem;
        }

        .section p {
            font-size: 1rem;
            line-height: 1.6;
            color: #333;
            margin-bottom: 15px;
        }

        /* Guide Steps */
        .guide-steps {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .step {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }

        .step:hover {
            transform: translateX(5px);
        }

        .step h3 {
            font-size: 1.2rem;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        /* Video Section */
        .video-guide {
            text-align: center;
        }

        .video-guide iframe {
            width: 100%;
            max-width: 800px;
            height: 450px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* FAQ Section */
        .faq-item {
            margin-bottom: 15px;
        }

        .faq-item summary {
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--primary-color);
            cursor: pointer;
            padding: 10px;
            background: white;
            border-radius: 8px;
            transition: background 0.3s;
        }

        .faq-item summary:hover {
            background: var(--accent-color);
        }

        .faq-item p {
            padding: 10px;
            background: #f9f9f9;
            border-radius: 0 0 8px 8px;
        }



        /* Social Links */
        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .social-links a {
            color: var(--primary-color);
            font-size: 1.8rem;
            transition: color 0.3s, transform 0.3s;
        }

        .social-links a:hover {
            color: var(--secondary-color);
            transform: scale(1.2);
        }

        /* Footer */
        .footer {
            background: var(--primary-color);
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: auto;
        }

        .footer p {
            font-size: 0.9rem;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .header h1 {
                font-size: 1.8rem;
            }

            .home-link {
                font-size: 0.9rem;
            }

            .section h2 {
                font-size: 1.5rem;
            }

            .video-guide iframe {
                height: 300px;
            }

            .rating-stars label {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 480px) {
            .video-guide iframe {
                height: 200px;
            }

            .social-links a {
                font-size: 1.5rem;
            }

            .rating-stars label {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <a href="/" class="home-link"><i class="fas fa-home"></i> Quay về Trang Chủ</a>
        <h1>Hướng Dẫn Sử Dụng</h1>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Guide Steps -->
        <div class="section guide-steps">
            <h2><i class="fas fa-book"></i> Hướng Dẫn Sử Dụng Ceramic Recognition</h2>
            <div class="step">
                <h3>Bước 1: Đăng Ký Tài Khoản</h3>
                <p>Truy cập trang đăng ký, điền thông tin cá nhân và xác nhận email để tạo tài khoản.</p>
            </div>
            <div class="step">
                <h3>Bước 2: Nạp Tiền</h3>
                <p>Chọn gói nạp tiền phù hợp, quét mã QR và tải lên ảnh chứng minh chuyển khoản.</p>
            </div>
            <div class="step">
                <h3>Bước 3: Sử Dụng Dịch Vụ</h3>
                <p>Tải lên hình ảnh gốm sứ để nhận diện và nhận kết quả phân tích chi tiết.</p>
            </div>
        </div>

        <!-- Video Guide -->
        <div class="section video-guide">
            <h2><i class="fas fa-video"></i> Video Hướng Dẫn</h2>
            <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>
        </div>

        <!-- FAQ Section -->
        <div class="section faq">
            <h2><i class="fas fa-question-circle"></i> Câu Hỏi Thường Gặp</h2>
            <details class="faq-item">
                <summary>Làm thế nào để nạp tiền?</summary>
                <p>Chọn gói nạp tiền, quét mã QR, chuyển khoản và tải lên ảnh chứng minh. Yêu cầu của bạn sẽ được xử lý
                    trong vòng 24 giờ.</p>
            </details>
            <details class="faq-item">
                <summary>Tôi có thể nhận diện bao nhiêu hình ảnh với 1 token?</summary>
                <p>Mỗi token cho phép bạn nhận diện 1 hình ảnh gốm sứ.</p>
            </details>
            <details class="faq-item">
                <summary>Làm sao để liên hệ hỗ trợ?</summary>
                <p>Bạn có thể gửi email đến support@ceramicrecognition.com hoặc liên hệ qua các kênh mạng xã hội bên
                    dưới.</p>
            </details>
        </div>



        <!-- Social Links -->
        <div class="section social-links">
            <a href="https://facebook.com" target="_blank"><i class="fab fa-facebook"></i></a>
            <a href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
            <a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
            <a href="https://youtube.com" target="_blank"><i class="fab fa-youtube"></i></a>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2025 Ceramic Recognition. All rights reserved.</p>
    </div>

    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Rating Form Submission
        document.getElementById('ratingForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const rating = document.querySelector('input[name="rating"]:checked');
            const comment = document.querySelector('.rating-comment').value;

            if (!rating) {
                alert('Vui lòng chọn số sao đánh giá!');
                return;
            }

            // Gửi đánh giá (đoạn này có thể thay bằng AJAX call đến server)
            alert('Cảm ơn bạn đã đánh giá ' + rating.value + ' sao!\nPhản hồi của bạn đã được ghi nhận.');

            // Reset form
            this.reset();
            document.querySelectorAll('.rating-stars label').forEach(star => {
                star.style.color = '#ccc';
            });
        });

        // Star hover effect
        document.querySelectorAll('.rating-stars label').forEach(star => {
            star.addEventListener('mouseover', function () {
                const stars = Array.from(document.querySelectorAll('.rating-stars label'));
                const currentIndex = stars.indexOf(this);

                stars.forEach((s, index) => {
                    if (index <= currentIndex) {
                        s.style.color = '#ffc107';
                    }
                });
            });

            star.addEventListener('mouseout', function () {
                const checkedStar = document.querySelector('input[name="rating"]:checked');
                if (!checkedStar) {
                    document.querySelectorAll('.rating-stars label').forEach(s => {
                        s.style.color = '#ccc';
                    });
                }
            });
        });


    </script>
</body>

</html>