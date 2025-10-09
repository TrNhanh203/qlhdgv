@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">Thêm người dùng</div>
            <div class="card-body">
                <form method="POST" action="/users">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Quyền</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="giangvien">Giảng viên</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Thêm mới</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 