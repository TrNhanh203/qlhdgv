@extends('layouts.app')

@section('title', 'Danh sách lịch gác thi')

@section('content')
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f9fafb;
        margin: 0;
    }

    .dark-mode {
        background: #1f2937;
        color: #f3f4f6;
    }

    .container {
        max-width: 1200px;
        margin: 20px auto;
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    h1 {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 16px;
        color: #333;
    }

    .dark-mode h1 { color: #f3f4f6; }

    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: 0.2s;
    }

    .btn-primary:hover { background: #1d4ed8; }
    .btn-secondary:hover { background: #e5e7eb; }
    .btn-disabled { background: #e5e7eb; color: #9ca3af; cursor: not-allowed; }

    .tag-primary { background-color: #2563eb; }  
    .tag-green { background-color: #22c55e; }   
    .tag-blue { background-color: #3b82f6; }    
    .tag-gray { background-color: #6b7280; } 

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
    }
    table td {
        vertical-align: middle;
    }
    table th, table td {
        padding: 12px 10px;
        border-bottom: 1px solid #e5e7eb;
        text-align: center;
        font-size: 14px;
        vertical-align: middle;
    }
    table th { background: #f1f5f9; font-weight: 600; }

    .dark-mode table th { background: #374151; color: #f3f4f6; }
    .dark-mode table td { border-bottom: 1px solid #374151; color: #f3f4f6; }

    .action-icon { background: none; border: none; font-size: 18px; cursor: pointer; }

    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-content {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        width: 600px;
        max-width: 90%;
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        animation: fadeIn 0.3s ease;
    }

    .dark-mode .modal-content { background: #1f2937; color: #f3f4f6; }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

    .modal-content h2 { margin-bottom: 16px; font-size: 20px; }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-actions {
        text-align: right;
        margin-top: 12px;
    }

    .btn {
        padding: 6px 14px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    .btn-primary {
        background: #2563eb;
        color: #fff;
    }

    .btn-secondary {
        background: #e5e7eb;
        color: #111827;
        margin-right: 6px;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .form-group label .required { color: red; margin-left: 2px; }

    .form-group input, .form-group select {
        width: 100%;
        padding: 8px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
    }

    .dark-mode .form-group input, .dark-mode .form-group select {
        background: #374151;
        color: #f3f4f6;
        border: 1px solid #6b7280;
    }

    .modal-actions {
        margin-top: 16px;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    body.dark-mode .container { background: #2c2c3e; color: #f1f1f1; box-shadow: 0 6px 18px rgba(0,0,0,0.35); }
    body.dark-mode h1 { color: #f3f4f6; }
    body.dark-mode table tbody tr:hover { background: #2b3443; }
</style>
<div class="container mt-4">
    <h4 class="mb-3">Danh sách lịch gác thi</h4>

    <div class="mb-3">
        <a href="" class="btn btn-primary">Thêm mới</a>
        <button class="btn btn-danger">Xóa mục</button>
        <button class="btn btn-secondary">Tự động xếp lịch gác thi cho 0 ca thi</button>
        <button class="btn btn-info">Gửi email nhắc thông báo cho 0 ca thi</button>
    </div>

    <div class="alert alert-info">
        <strong>Lưu ý:</strong> Đối với các ca thi đã có dữ liệu, bạn không thể chỉnh sửa lịch gác thi.
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th><input type="checkbox"></th>
                    <th>Môn thi</th>
                    <th>Phòng thi</th>
                    <th>Kỳ thi</th>
                    <th>Loại kỳ thi</th>
                    <th>Hình thức thi</th>
                    <th>Thời gian từ</th>
                    <th>Thời gian đến</th>
                    <th>Thời lượng (phút)</th>
                    <th>SLSV tham gia</th>
                    <th>SLGV cần</th>
                    <th>SLTT cần</th>
                    <th>SLGV đã có</th>
                    <th>SLTT đã có</th>
                    <th>Trạng thái điểm danh</th>
                    <th>Hiệu chỉnh</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exam_proctorings as $proctoring)
                <tr>
                    <td><input type="checkbox" name="selected[]" value="{{ $proctoring->id }}"></td>
                    <td>{{ $proctoring->exam->course->course_name }}</td>
                    <td>{{ $proctoring->exam->room->name ?? '-' }}</td>
                    <td>{{ $proctoring->exam->semester->semester_name }}</td>
                    <td>{{ $proctoring->exam->exam_type }}</td>
                    <td>{{ $proctoring->exam->exam_form }}</td>
                    <td>{{ $proctoring->exam->exam_start ? $proctoring->exam->exam_start->format('d-m-Y H:i') : '-' }}</td>
                    <td>{{ $proctoring->exam->exam_end ? $proctoring->exam->exam_end->format('d-m-Y H:i') : '-' }}</td>
                    <td>{{ $proctoring->exam->exam_start && $proctoring->exam->exam_end ? $proctoring->exam->exam_end->diffInMinutes($proctoring->exam->exam_start) : '-' }}</td>
                    <td>{{ $proctoring->exam->expected_students }}</td>
                    <td>{{ $proctoring->assignment_type == 'giangvien' ? 1 : 2 }}</td>
                    <td>{{ $proctoring->assignment_type == 'trothong' ? 1 : 2 }}</td>
                    <td>{{ $proctoring->lecture->count() }}</td>
                    <td>{{ $proctoring->status_id ? 1 : 0 }}</td>
                    <td>
                        @if(!$proctoring->checked_in)
                            <span class="badge bg-danger">Chưa điểm danh</span>
                        @else
                            <span class="badge bg-success">Đã điểm danh</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('exam_proctorings.edit', $proctoring->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-gear"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $exam_proctorings->links() }} 
    </div>
</div>
@endsection
