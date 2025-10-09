@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">Sửa thông tin người dùng</div>
            <div class="card-body">
                <form method="POST" action="/users/1">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên</label>
                        <input type="text" class="form-control" id="name" name="name" value="Nguyễn Văn A" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="nguyenvana@email.com" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Quyền</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="admin" selected>Admin</option>
                            <option value="giangvien">Giảng viên</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Cập nhật</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 