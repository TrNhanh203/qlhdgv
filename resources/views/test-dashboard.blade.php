<!DOCTYPE html>
<html>
<head>
    <title>Test Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Test Dashboard - Authentication Working!</h3>
                    </div>
                    <div class="card-body">
                        <h4>Thông tin User:</h4>
                        <ul>
                            <li><strong>Email:</strong> {{ $user->email }}</li>
                            <li><strong>User Type:</strong> {{ $user->user_type }}</li>
                            <li><strong>Active:</strong> {{ $user->status_id ? 'Yes' : 'No' }}</li>
                        </ul>
                        
                        <hr>
                        
                        <h4>Test các Dashboard:</h4>
                        <div class="row">
                            <div class="col-md-2">
                                <a href="{{ route('superadmin.dashboard') }}" class="btn btn-primary">SuperAdmin</a>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-success">Admin</a>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('truongkhoa.dashboard') }}" class="btn btn-warning">Trưởng Khoa</a>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('truongbomon.dashboard') }}" class="btn btn-info">Trưởng Bộ Môn</a>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('giangvien.dashboard') }}" class="btn btn-secondary">Giảng Viên</a>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 