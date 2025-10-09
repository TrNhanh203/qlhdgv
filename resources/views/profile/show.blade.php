@php
    switch(auth()->user()->role) {
        case 'superadmin':
            $layout = 'layouts.appsuperadmin';
            break;
        case 'admin':
            $layout = 'layouts.app';
            break;
        case 'truongkhoa':
            $layout = 'layouts.apptruongkhoa';
            break;
        case 'truongbomon':
            $layout = 'layouts.apptruongbomon';
            break;
        case 'giangvien':
            $layout = 'layouts.appGV';
            break;
        default:
            $layout = 'layouts.app';
    }
@endphp

@extends($layout)

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
body.dark-mode .dashboard-card {
    background: #1f2937;
    color: #f3f4f6;
    box-shadow: 0 4px 6px rgba(0,0,0,0.3);
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
    table tbody tr {
    transition: background 0.25s ease, transform 0.2s ease;
    }
    table tbody tr:hover {
        background: #e0f2fe;  /* xanh nhạt */
        transform: scale(1.01);
    }
    .action-icon {
    transition: transform 0.3s, color 0.3s;
    }
    .action-icon:hover {
        transform: rotate(20deg) scale(1.2);
        color: #2563eb;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge.bg-success::before { content: "✅"; }
    .badge.bg-danger::before { content: "⛔"; }
    .badge.bg-info::before   { content: "⏸"; }
    .badge.bg-secondary::before { content: "❓"; }

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
    .dark-input {
    background: #fff;
    color: #111827;
    border: 1px solid #d1d5db;
    transition: all 0.2s;
}

body.dark-mode .dark-input {
    background: #1f2937; /* màu nền tối */
    color: #f3f4f6;      /* chữ sáng */
    border: 1px solid #4b5563;
}

body.dark-mode .dark-input::placeholder {
    color: #9ca3af;
}

body.dark-mode .dark-input:focus {
    border-color: #60a5fa;
    box-shadow: 0 0 0 3px rgba(96,165,250,.2);
}
    
</style>
<div class="container">
    <h3 class="mb-4">👤 Hồ sơ cá nhân</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="mb-4">
        <label>Ảnh đại diện</label><br>
        <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : asset('images/default-avatar.png') }}"
             alt="Avatar"
             class="rounded-circle mb-2"
             style="width:100px;height:100px;object-fit:cover;"
             onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';">
        <input type="file" name="avatar" class="form-control dark-input">
    </div>

    <div class="mb-4">
        <label class="form-label">Tên</label>
        <input type="text" name="name" value="{{ old('name',$user->name) }}" class="form-control dark-input" required>
        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email',$user->email) }}" class="form-control dark-input" required>
        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Mật khẩu (nếu đổi)</label>
        <input type="password" name="password" class="form-control dark-input">
        @error('password') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Xác nhận mật khẩu</label>
        <input type="password" name="password_confirmation" class="form-control dark-input">
    </div>

    <button type="submit" class="btn btn-primary">💾 Lưu thay đổi</button>
</form>

</div>
@endsection
