@extends('layouts.apptruongkhoa')

@section('title', 'Danh sách Bộ môn')

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
.status.active {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 500;
    color: #22c55e;
    background: rgba(34,197,94,0.1);
    border: 1px solid #22c55e;
}

.status.inactive {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 500;
    color: #ef4444;
    background: rgba(239,68,68,0.1);
    border: 1px solid #ef4444;
}
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
    <h3 class="mb-3">Danh sách Bộ môn</h3>

    {{-- Search Bộ môn --}}
    <form method="GET" action="{{ route('truongkhoa.bomon.index') }}" class="mb-3 d-flex">
        <input type="text" id="searchDepartments" class="form-control mb-3" placeholder="Tìm kiếm Bộ môn...">
    </form>

    

    {{-- Bảng Bộ môn --}}
    <table >
        <thead>
            <tr>
                <th>Tên Bộ môn</th>
                <th>Khoa</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            @forelse($departments as $department)
            <tr>
                <td>{{ $department->department_name }}</td>
                <td>{{ $department->faculty->faculty_name ?? '---' }}</td>
                <td>
                    @if($department->status_id == 1)
                        <span class="status active">Hoạt động</span>
                    @else
                        <span class="status inactive">Ngưng hoạt động</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center text-muted">Chưa có dữ liệu</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $departments->links() }}
    </div>

    {{-- Bảng Trưởng Bộ môn --}}
    <h3 class="mt-5 mb-3">Danh sách Trưởng Bộ môn</h3>

    {{-- Search Trưởng Bộ môn --}}
    <form method="GET" action="{{ route('truongkhoa.bomon.index') }}" class="mb-3 d-flex">
        
{{-- Tìm kiếm Trưởng Bộ môn --}}
<input type="text" id="searchTBM" class="form-control mb-3" placeholder="Tìm kiếm Trưởng Bộ môn...">
    </form>

    

    <table >
        <thead>
            <tr>
                <th>Bộ môn</th>
                <th>Trưởng Bộ môn</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Ngày bắt đầu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($truongBoMon as $tbm)
            <tr>
                <td>{{ $tbm->department->department_name ?? '---' }}</td>
                <td>{{ $tbm->lecture->full_name ?? '---' }}</td>
                <td>{{ $tbm->lecture->email ?? '---' }}</td>
                <td>{{ $tbm->lecture->phone ?? '---' }}</td>
                <td>
                    {{ $tbm->start_date ? \Carbon\Carbon::parse($tbm->start_date)->format('d/m/Y') : '---' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted">Chưa có dữ liệu</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<script>
    // ===== Tìm kiếm Bộ môn đa năng =====
document.getElementById('searchDepartments').addEventListener('input', function(){
    const keyword = this.value.toLowerCase().trim();
    document.querySelectorAll('table:nth-of-type(1) tbody tr').forEach(row => {
        const cells = row.querySelectorAll('td');
        let match = false;
        cells.forEach(cell => {
            if(cell.textContent.toLowerCase().includes(keyword)) match = true;
        });
        row.style.display = match ? '' : 'none';
    });
});

// ===== Tìm kiếm Trưởng Bộ môn đa năng =====
document.getElementById('searchTBM').addEventListener('input', function(){
    const keyword = this.value.toLowerCase().trim();
    document.querySelectorAll('table:nth-of-type(2) tbody tr').forEach(row => {
        const cells = row.querySelectorAll('td');
        let match = false;
        cells.forEach(cell => {
            if(cell.textContent.toLowerCase().includes(keyword)) match = true;
        });
        row.style.display = match ? '' : 'none';
    });
});

    </script>
    <script>
function normalizeString(str) {
    return str
        .toLowerCase()                     // chuyển chữ thường
        .normalize('NFD')                  // tách dấu
        .replace(/[\u0300-\u036f]/g, '')  // bỏ dấu
        .replace(/\s+/g, ' ')              // chuẩn hóa khoảng trắng
        .trim();
}

const searchBox = document.getElementById('searchBox');
searchBox.addEventListener('input', function() {
    const keyword = normalizeString(this.value);
    document.querySelectorAll('table tbody tr').forEach(row => {
        let match = false;
        row.querySelectorAll('td').forEach(cell => {
            if(normalizeString(cell.textContent).includes(keyword)) {
                match = true;
            }
        });
        row.style.display = match ? '' : 'none';
    });
});
</script>
@endsection