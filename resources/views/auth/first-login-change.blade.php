@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Đổi mật khẩu lần đầu</h2>
    <form method="POST" action="{{ route('first-login.update') }}">
        @csrf
        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu mới</label>
            <input id="password" type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
    </form>
</div>
@endsection
