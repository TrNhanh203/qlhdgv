@extends('layouts.app')
@section('content')
<h3>Thông báo hệ thống</h3>
<a href="/notifications/create" class="btn btn-success mb-3">Gửi thông báo mới</a>
<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Tiêu đề</th>
            <th>Loại</th>
            <th>Ngày gửi</th>
            <th>Nội dung</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            <td>Phân công mới</td>
            <td>Phân công</td>
            <td>2024-05-10</td>
            <td>Giảng viên A được phân công dạy lớp D19CQCN01.</td>
        </tr>
        <tr>
            <td>2</td>
            <td>Thay đổi lịch thi</td>
            <td>Lịch thi</td>
            <td>2024-05-12</td>
            <td>Lịch thi môn Toán rời rạc đã thay đổi sang ngày 15/5.</td>
        </tr>
    </tbody>
</table>
@endsection 