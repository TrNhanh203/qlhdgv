@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">Đổi mật khẩu</div>
            <div class="card-body">
                <form method="POST" action="/change-password">
                    @csrf
                    <div class="mb-3">
                        <label for="old_password" class="form-label">Mật khẩu cũ</label>
                        <input type="password" class="form-control" id="old_password" name="old_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Mật khẩu mới</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Đổi mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 