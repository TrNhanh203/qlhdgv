@extends('layouts.app')

@section('title', 'Đề cương học phần')

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
    .tag {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
        color: #fff;
    }

    .btn-primary { background: #2563eb; color: #fff; }
    .btn-primary:hover { background: #1d4ed8; }
    .btn-secondary { background: #f3f4f6; color: #111827; }
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
    }
    table th {
        background: #f1f5f9;
        font-weight: 600;
        text-align: center;
    }

    .dark-mode table th { background: #374151; color: #f3f4f6; }
    .dark-mode table td { border-bottom: 1px solid #374151; color: #f3f4f6; }

    .action-icon { background: none; border: none; font-size: 18px; cursor: pointer; }

    /* ===== Overlay Modal ===== */
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
    width: 900px;         /* tăng từ 600px lên 900px */
    max-width: 95%;       /* cho responsive */
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    animation: fadeIn 0.3s ease;
}

    .dark-mode .modal-content { background: #1f2937; color: #f3f4f6; }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

    .modal-content h2 { margin-bottom: 16px; font-size: 20px; }

    .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr; 
    gap: 30px;            /* tăng khoảng cách cột */
}



    .form-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 12px;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .form-group label .required { color: red; margin-left: 2px; }

    .form-group input,
    .form-group select {
            width: 100%;  
            padding: 12px;        /* padding rộng hơn */
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 15px;      /* chữ to hơn một chút */
        }

    .dark-mode .form-group input, .dark-mode .form-group select {
        background: #374151;
        color: #f3f4f6;
        border: 1px solid #6b7280;
    }

    .form-actions {
        margin-top: 16px;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    body.dark-mode .container {
        background: #2c2c3e;
        color: #f1f1f1;
        box-shadow: 0 6px 18px rgba(0,0,0,0.35);
    }
    body.dark-mode h1 { color: #f3f4f6; }
    body.dark-mode table { color: #e5e7eb; }
    body.dark-mode table th { background: #1f2937; color: #e5e7eb; border-bottom-color: #374151; }
    body.dark-mode table td { border-bottom-color: #374151; color: #f3f4f6; }
    body.dark-mode table tbody tr:hover { background: #2b3443; }
    tbody tr:hover { background-color: #f3f4f6; }
    body.dark-mode .action-icon { color: #e5e7eb; }
    body.dark-mode .btn-secondary { background: #374151; color: #e5e7eb; }
    body.dark-mode .btn-secondary:hover { background: #4b5563; }
    body.dark-mode .status.active { background: rgba(34,197,94,.15); color: #22c55e; }
    body.dark-mode .status.inactive { background: rgba(239,68,68,.15); color: #ef4444; }

    body.dark-mode .modal-content { background: #2c2c3e; color: #f1f1f1; border: 1px solid #3b3b52; }
    body.dark-mode .form-group label { color: #e5e7eb; }
    body.dark-mode .form-group input, body.dark-mode .form-group select { background: #1f2937; color: #e5e7eb; border-color: #4b5563; }
    body.dark-mode .form-group input::placeholder { color: #9ca3af; }
    body.dark-mode .form-group input:focus, body.dark-mode .form-group select:focus { border-color: #60a5fa; box-shadow: 0 0 0 3px rgba(96,165,250,.2); }
</style>
<div class="container">
    <h3>Quản lý Đề cương học phần</h3>

    <form id="uploadForm" action="{{ route('admin.decuonghocphan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row mb-3">
            <div class="col-md-4">
                <select name="course_id" class="form-select" required>
                    <option value="">-- Chọn học phần --</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}">{{ $c->course_code }} - {{ $c->course_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="file" name="file" class="form-control" required>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-success">Tải lên</button>
            </div>
        </div>
    </form>

    <hr>
     <button type="submit" class="btn btn-danger mt-2" onclick="return confirm('Xóa các đề cương đã chọn?')">Xóa nhiều</button>
    <form id="deleteMultipleForm" action="{{ route('admin.decuonghocphan.deleteMultiple') }}" method="POST">
        @csrf @method('DELETE')
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkAll"></th>
                    <th>Học phần</th>
                    <th>File</th>
                    <th>Người upload</th>
                    <th>Ngày upload</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($syllabuses as $s)
                <tr>
                    <td><input type="checkbox" name="ids[]" value="{{ $s->id }}"></td>
                    <td>{{ $s->course->course_name ?? '-' }}</td>
                    <td><a href="{{ asset('storage/'.$s->file_path) }}" target="_blank">{{ $s->file_name }}</a></td>
                    <td>{{ $s->uploader->name ?? '-' }}</td>
                    <td>{{ $s->uploaded_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <form action="{{ route('admin.decuonghocphan.delete', $s->id) }}" method="POST" onsubmit="return confirm('Xóa đề cương này?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
       
    </form>
</div>

<script>
document.getElementById('checkAll').addEventListener('click', function(){
    let checkboxes = document.querySelectorAll('input[name="ids[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
