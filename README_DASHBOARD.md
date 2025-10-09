# Hệ thống Quản lý Hoạt động Giảng viên - Dashboard

## Tổng quan

Hệ thống quản lý hoạt động giảng viên với phân quyền 5 cấp độ:
- **SuperAdmin**: Quản lý toàn bộ hệ thống
- **Admin trường**: Quản lý trường đại học
- **Trưởng khoa**: Quản lý khoa
- **Trưởng bộ môn**: Quản lý bộ môn
- **Giảng viên**: Xem thông tin cá nhân

## Cài đặt và Chạy

### 1. Cài đặt dependencies
```bash
composer install
npm install
```

### 2. Cấu hình database
- Tạo file `.env` từ `.env.example`
- Cấu hình thông tin database trong file `.env`

### 3. Chạy migration và seeder
```bash
php artisan migrate
php artisan db:seed
```

### 4. Chạy ứng dụng
```bash
php artisan serve
npm run dev
```

## Tài khoản đăng nhập

Sau khi chạy seeder, bạn có thể đăng nhập với các tài khoản sau:

### SuperAdmin
- Email: `superadmin@tdmu.edu.vn`
- Password: `password`

### Admin trường
- Email: `admin@tdmu.edu.vn`
- Password: `password`

### Trưởng khoa
- Email: `truongkhoa@tdmu.edu.vn`
- Password: `password`

### Trưởng bộ môn
- Email: `truongbomon@tdmu.edu.vn`
- Password: `password`

### Giảng viên
- Email: `giangvien@tdmu.edu.vn`
- Password: `password`

## Tính năng Dashboard

### 1. SuperAdmin Dashboard
- **Thống kê tổng quan**: Số lượng trường, admin, người dùng, hoạt động hệ thống
- **Phân bổ người dùng**: Biểu đồ phân bổ theo vai trò
- **Trường theo khu vực**: Biểu đồ số lượng trường theo miền
- **Nhật ký hoạt động**: 5 hoạt động gần nhất trong hệ thống

### 2. Admin trường Dashboard
- **Thông tin trường**: Chi tiết thông tin trường đại học
- **Thống kê tổng quan**: Số lượng khoa, giảng viên, phòng học, kỳ thi
- **Tỷ lệ sinh viên**: Biểu đồ phân bổ sinh viên theo khoa
- **Môn học theo học kỳ**: Biểu đồ số lượng môn học theo học kỳ

### 3. Trưởng khoa Dashboard
- **Thống kê khoa**: Số lượng bộ môn, giảng viên, môn học, lịch giảng
- **Thống kê bộ môn**: Bảng thống kê chi tiết theo bộ môn
- **Giảng dạy theo tháng**: Biểu đồ lịch giảng theo tháng
- **Danh sách giảng viên**: Thông tin giảng viên trong khoa

### 4. Trưởng bộ môn Dashboard
- **Thống kê bộ môn**: Số lượng giảng viên, môn học, lịch giảng, cuộc họp
- **Thống kê giảng viên**: Bảng thống kê chi tiết giảng viên
- **Giảng dạy theo tuần**: Biểu đồ lịch giảng theo tuần
- **Cuộc họp gần đây**: Danh sách cuộc họp trong bộ môn

### 5. Giảng viên Dashboard
- **Thống kê cá nhân**: Số lượng lịch giảng, coi thi, cuộc họp, giờ giảng
- **Lịch giảng gần đây**: Danh sách lịch giảng gần đây
- **Lịch coi thi sắp tới**: Danh sách lịch coi thi sắp tới
- **Giảng dạy theo tháng**: Biểu đồ lịch giảng theo tháng
- **Cuộc họp sắp tới**: Danh sách cuộc họp sắp tới

## Cấu trúc Database

### Bảng chính
- `universities`: Thông tin trường đại học
- `faculties`: Thông tin khoa
- `departments`: Thông tin bộ môn
- `lectures`: Thông tin giảng viên
- `users`: Tài khoản người dùng
- `roles`: Vai trò trong hệ thống
- `courses`: Môn học
- `teaching_duties`: Lịch giảng
- `exams`: Kỳ thi
- `exam_proctorings`: Lịch coi thi
- `meetings`: Cuộc họp
- `workloads`: Khối lượng giảng dạy
- `audit_logs`: Nhật ký hoạt động

### Quan hệ
- Mỗi trường có nhiều khoa
- Mỗi khoa có nhiều bộ môn
- Mỗi bộ môn có nhiều giảng viên
- Mỗi giảng viên có một tài khoản user
- Mỗi user có thể có nhiều role

## Middleware và Bảo mật

### CheckRole Middleware
- Kiểm tra quyền truy cập theo role
- Redirect về dashboard tương ứng nếu không có quyền
- Đăng ký trong `bootstrap/app.php`

### Authentication
- Sử dụng Laravel Auth
- Password được hash bằng bcrypt
- Session-based authentication

## API Endpoints

### Dashboard Routes
- `GET /superadmin/dashboard` - SuperAdmin dashboard
- `GET /admin/dashboard` - Admin trường dashboard
- `GET /truongkhoa/dashboard` - Trưởng khoa dashboard
- `GET /truongbomon/dashboard` - Trưởng bộ môn dashboard
- `GET /giangvien/dashboard` - Giảng viên dashboard

### Authentication Routes
- `GET /login` - Form đăng nhập
- `POST /login` - Xử lý đăng nhập
- `POST /logout` - Đăng xuất

## Công nghệ sử dụng

- **Backend**: Laravel 11
- **Frontend**: Bootstrap 5, Chart.js
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel Auth
- **Charts**: Chart.js

## Phát triển thêm

### Thêm tính năng mới
1. Tạo migration cho bảng mới
2. Tạo model với relationships
3. Tạo controller với logic xử lý
4. Tạo view với giao diện
5. Thêm routes và middleware

### Thêm role mới
1. Thêm role vào bảng `roles`
2. Cập nhật middleware `CheckRole`
3. Tạo controller và view cho role mới
4. Thêm routes với middleware tương ứng

## Troubleshooting

### Lỗi thường gặp
1. **Lỗi database**: Kiểm tra cấu hình `.env` và chạy lại migration
2. **Lỗi authentication**: Kiểm tra session và cache
3. **Lỗi chart**: Kiểm tra dữ liệu truyền vào Chart.js

### Debug
- Sử dụng `dd()` để debug dữ liệu
- Kiểm tra log trong `storage/logs/laravel.log`
- Sử dụng Laravel Telescope để debug (nếu có)

## Liên hệ

Nếu có vấn đề hoặc cần hỗ trợ, vui lòng liên hệ team phát triển. 