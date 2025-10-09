<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Hoạt động Giảng viên Trong Trường Đại Học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: url('/images/university.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', sans-serif;
            color: #fff;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            padding: 80px 20px;
            text-align: center;
        }

        .hero-content h1 {
            font-size: 3rem;
            font-weight: 700;
            color: #facc15;
        }

        .hero-content p {
            font-size: 1.25rem;
            margin-top: 15px;
            color: #e5e7eb;
        }

        .btn-login {
            margin-top: 30px;
            padding: 12px 30px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 10px;
        }

        .features {
            position: relative;
            z-index: 2;
            margin-top: 50px;
        }

        .feature-box {
            background: rgba(255, 255, 255, 0.95);
            color: #1e293b;
            padding: 30px 20px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .feature-box:hover {
            transform: translateY(-8px);
            background: #f1f5f9;
        }

        .feature-icon {
            font-size: 2.5rem;
            color: #2563eb;
            margin-bottom: 10px;
        }

        .footer {
            text-align: center;
            padding: 20px 0;
            margin-top: 60px;
            font-size: 14px;
            color: #f3f4f6;
        }
    </style>
</head>
<body>

    <div class="overlay"></div>

    <div class="container hero-content">
        <h1>QUẢN LÝ HOẠT ĐỘNG CỦA GIẢNG VIÊN</h1>
        <p>Nền tảng hiện đại dành cho tất cả các trường đại học để theo dõi, đánh giá và báo cáo toàn diện hoạt động của giảng viên.</p>
        <a href="{{ route('login') }}" class="btn btn-warning btn-login">Đăng nhập hệ thống</a>
    </div>

    <div class="container features">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <div class="feature-icon">📚</div>
                    <h5>Quản lý chuyên môn</h5>
                    <p>Theo dõi phân công giảng dạy, lịch thi, thời khóa biểu và tiến độ hoàn thành môn học.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <div class="feature-icon">🧪</div>
                    <h5>Nghiên cứu khoa học</h5>
                    <p>Quản lý đề tài NCKH, nghiệm thu, minh chứng nghiên cứu và tổng hợp kết quả.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <div class="feature-icon">👨‍🏫</div>
                    <h5>Hướng dẫn sinh viên</h5>
                    <p>Thống kê hướng dẫn khóa luận, đồ án, thực tập, đánh giá kết quả và kết nối sinh viên.</p>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-3">
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <div class="feature-icon">📁</div>
                    <h5>Lưu trữ hồ sơ</h5>
                    <p>Lưu trữ chứng chỉ, học hàm, học vị, lịch sử công tác và hồ sơ chuyên môn giảng viên.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <div class="feature-icon">📊</div>
                    <h5>Thống kê & Báo cáo</h5>
                    <p>Xuất báo cáo tự động theo từng tiêu chí: giảng dạy, NCKH, hướng dẫn, điểm đánh giá,...</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <div class="feature-icon">🔔</div>
                    <h5>Thông báo & Nhắc việc</h5>
                    <p>Gửi nhắc nhở tự động, phân quyền người nhận, thông báo nhanh tới từng bộ phận/khoa.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} - Hệ thống quản lý dành cho các trường đại học Việt Nam. Phát triển bởi nhóm đồ án.
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
