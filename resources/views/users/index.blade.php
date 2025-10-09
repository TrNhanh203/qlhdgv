@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Quản lý người dùng</h3>
    <a href="/users/create" class="btn btn-success">Thêm người dùng</a>
</div>
<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Tên</th>
            <th>Email</th>
            <th>Quyền</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        {{-- @foreach ($users as $user) --}}
        <tr>
            <td>1</td>
            <td>Nguyễn Văn A</td>
            <td>nguyenvana@email.com</td>
            <td>Admin</td>
            <td>
                <a href="/users/1/edit" class="btn btn-sm btn-primary">Sửa</a>
                <form action="/users/1" method="POST" style="display:inline-block">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xóa người dùng này?')">Xóa</button>
                </form>
            </td>
        </tr>
        {{-- @endforeach --}}
    </tbody>
</table>
@endsection 