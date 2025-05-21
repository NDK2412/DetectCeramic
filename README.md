# Ceramic AI

Ceramic AI là một hệ thống trí tuệ nhân tạo tiên tiến giúp nhận diện và phân loại gốm sứ dựa trên hình ảnh. Ứng dụng hỗ trợ người dùng xác định niên đại, xuất xứ, và đặc điểm của đồ gốm thông qua công nghệ học máy. Ceramic AI cung cấp cả giao diện web và ứng dụng Android, phù hợp cho các nhà sưu tầm, nhà nghiên cứu, bảo tàng, và người yêu gốm sứ.

## Tính năng chính

- **Nhận diện gốm sứ**: Tải lên hoặc chụp ảnh gốm sứ để nhận kết quả phân tích nhanh chóng.
- **Lịch sử nhận diện**: Lưu trữ và xem lại lịch sử các lần nhận diện với thông tin chi tiết.
- **Đánh giá trải nghiệm**: Người dùng có thể đánh giá và xem đánh giá từ cộng đồng.
- **Cài đặt mô hình AI**: Quản trị viên có thể chọn mô hình LLM và nhập API Key (chỉ trên web).
- **Hỗ trợ đa nền tảng**: Giao diện web (Laravel) và ứng dụng Android.
- **Đa dạng ứng dụng**: Phục vụ bảo tàng, giáo dục, thương mại, và bảo tồn di sản.

## Yêu cầu hệ thống

### Web
- **Môi trường**:
  - PHP >= 8.0
  - Laravel >= 9.0
  - MySQL hoặc PostgreSQL
  - Composer
  - Node.js và npm (cho assets)
- **Trình duyệt**: Chrome, Firefox, Safari (phiên bản mới nhất)
- **API Key**: Yêu cầu API Key cho mô hình LLM (nếu sử dụng AI bên thứ ba).



## Hướng dẫn cài đặt

### 1. Ứng dụng Web
1. **Clone repository**:
   ```bash
   git clone <repository-url>
   cd ceramic-ai
   ```

2. **Cài đặt dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Sao chép file môi trường**:
   ```bash
   cp .env.example .env
   ```

4. **Cấu hình file `.env`**:
   - Cập nhật thông tin cơ sở dữ liệu:
     ```
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=ceramic_ai
     DB_USERNAME=root
     DB_PASSWORD=
     ```
   - Cấu hình URL ứng dụng:
     ```
     APP_URL=http://localhost:8000
     ```

5. **Tạo khóa ứng dụng**:
   ```bash
   php artisan key:generate
   ```

6. **Biên dịch assets**:
   ```bash
   npm run dev
   ```

7. **Khởi động server**:
   ```bash
   start php artisan serve --host=0.0.0.0 --port=8000 && php artisan queue:work
   ```
   Truy cập ứng dụng tại `http://localhost:8000`.

## Hướng dẫn sử dụng

### 1. Truy cập ứng dụng Web
1. **Đăng nhập**:
   - Truy cập `http://localhost:8000/login`.
   - Sử dụng tài khoản đã đăng ký hoặc tạo tài khoản mới.

2. **Nhận diện gốm sứ**:
   - Vào mục **CeramicAI** trên dashboard.
   - Chọn chế độ **Upload photo** hoặc **Take a photo**:
     - **Upload photo**: Tải ảnh từ thiết bị hoặc kéo thả ảnh vào khu vực upload.
     - **Take a photo**: Sử dụng camera để chụp ảnh gốm sứ.
   - Nhấn **Generate** để nhận kết quả nhận diện và thông tin chi tiết.

3. **Xem lịch sử**:
   - Vào mục **History** để xem danh sách các lần nhận diện.
   - Nhập từ khóa vào ô tìm kiếm để lọc kết quả.
   - Nhấn **See details** để xem thông tin chi tiết của mỗi lần nhận diện.

4. **Đánh giá**:
   - Vào mục **Rating** để gửi đánh giá (1-5 sao) và phản hồi.
   - Xem đánh giá của người dùng khác trong bảng **Reviews From Other Users**.
   - Sử dụng bộ lọc để xem đánh giá theo số sao.

5. **Cập nhật thông tin cá nhân**:
   - Nhấn vào tên người dùng trong sidebar để mở popup cập nhật.
   - Cập nhật tên, số điện thoại, giới tính, địa chỉ và nhấn **Lưu**.

6. **Cài đặt mô hình AI** (dành cho quản trị viên):
   - Truy cập `/admin/llm-settings`.
   - Chọn mô hình LLM từ danh sách và nhập API Key.
   - Nhấn **Lưu cài đặt**.
   - docker pull ndk2412/ceramicdetect   #image docker API 
   - 

### 2. Sử dụng ứng dụng Android
1. **Mở ứng dụng**:
   - Cài đặt và mở ứng dụng Ceramic AI trên thiết bị Android.

2. **Xem lịch sử nhận diện**:
   - Ứng dụng hiển thị danh sách các lần nhận diện gốm sứ trong một `RecyclerView`.
   - Mỗi item bao gồm:
     - Hình ảnh gốm sứ (100x100dp).
     - Kết quả nhận diện.
     - Thời gian nhận diện.
     - Nút **Ấn để hiện/ẩn thông tin** để xem chi tiết.

3. **Tương tác**:
   - Nhấn nút **Ấn để hiện/ẩn thông tin** để hiển thị hoặc ẩn thông tin bổ sung.
   - Khung viền (xám, bo góc 8dp) giúp phân biệt các item trong danh sách.

## Cấu trúc dự án

### Web
```
ceramic-ai/
├── app/
│   ├── Models/
│   │   ├── Metadata.php
│   │   ├── Apk.php
│   │   └── ...
│   └── ...
├── resources/
│   ├── views/
│   │   ├── admin/
│   │   │   └── llm_settings.blade.php
│   │   ├── dashboard.blade.php
│   │   └── index.blade.php
│   └── css/
│       ├── dashboard.css
│       └── public.css
├── public/
│   ├── storage/
│   │   ├── ceramics/
│   │   │   └── logo2.webp
│   │   └── ...
│   └── css/
└── ...
```



## Góp ý và hỗ trợ

Nếu bạn gặp vấn đề hoặc có ý kiến đóng góp, vui lòng liên hệ:

- **Email**: khangkhang1111777@gmail.com
- **SĐT**: 
- **Facebook**:
- **Địa chỉ**: 

Bạn cũng có thể gửi phản hồi qua biểu mẫu liên hệ trong mục **Contact** trên trang web.

## Giấy phép

© 2023 Ceramic Classification System. All rights reserved.

---

*Hướng dẫn này được tạo vào ngày 04/05/2025 và có thể được cập nhật theo phiên bản mới của ứng dụng.*
