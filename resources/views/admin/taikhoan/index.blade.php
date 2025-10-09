@extends('layouts.app')

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

<div class="container">
    <!-- Nút thêm mới -->
    <div style="margin: 20px 0; text-align: right;">
        <button onclick="document.getElementById('addUserModal').style.display='flex'" 
                style="padding: 10px 16px; background:#2563eb; color:#fff; border:none; border-radius:8px; cursor:pointer;">
            ➕ Thêm mới tài khoản
        </button>
    </div>

    <!-- Bảng 1: Trưởng khoa -->
    <h2>Tài khoản Trưởng khoa</h2>
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Avatar</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Ngày cập nhật</th>
                <th style="width: 50px;">Hiệu chỉnh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users->where('role','truongkhoa') as $user)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><img src="{{ $user->avatar ? asset($user->avatar) : asset('avatars/default.png') }}" class="avatar"></td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->status_id == 1)
                        <span class="status active">Hoạt động</span>
                    @else
                        <span class="status inactive">Không hoạt động</span>
                    @endif
                </td>
                <td>{{ $user->created_at ? $user->created_at->format('Y-m-d') : '' }}</td>
                <td>{{ $user->updated_at ? $user->updated_at->format('Y-m-d') : '' }}</td>

                <td><button class="action-icon">⚙️</button></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Bảng 2: Bộ môn -->
    <h2>Tài khoản Bộ môn</h2>
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Avatar</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Ngày cập nhật</th>
                <th style="width: 50px;">Hiệu chỉnh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users->where('role','truongbomon') as $user)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><img src="{{ $user->avatar ? asset($user->avatar) : asset('avatars/default.png') }}" class="avatar"></td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->status_id == 1)
                        <span class="status active">Hoạt động</span>
                    @else
                        <span class="status inactive">Không hoạt động</span>
                    @endif
                </td>
                <td>{{ $user->created_at ? $user->created_at->format('Y-m-d') : '' }}</td>
                <td>{{ $user->updated_at ? $user->updated_at->format('Y-m-d') : '' }}</td>

                <td><button class="action-icon">⚙️</button></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Bảng 3: Giảng viên -->
    <h2>Tài khoản Giảng viên</h2>
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Avatar</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Ngày cập nhật</th>
                <th style="width: 50px;">Hiệu chỉnh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users->where('role','giangvien') as $user)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><img src="{{ $user->avatar ? asset($user->avatar) : asset('avatars/default.png') }}" class="avatar"></td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->status_id == 1)
                        <span class="status active">Hoạt động</span>
                    @else
                        <span class="status inactive">Không hoạt động</span>
                    @endif
                </td>
                <td>{{ $user->created_at ? $user->created_at->format('Y-m-d') : '' }}</td>
                <td>{{ $user->updated_at ? $user->updated_at->format('Y-m-d') : '' }}</td>

                <td><button class="action-icon">⚙️</button></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<!-- Modal thêm mới -->
<div id="addUserModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
     background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center; z-index:999;">
    <div style="background:#fff; padding:24px; border-radius:12px; width:700px; max-width:95%; position:relative;">
        <h3 style="margin-bottom:20px; font-size:20px; font-weight:700;">Thêm mới tài khoản</h3>
        
        <form method="POST" action="{{ route('admin.taikhoan.store') }}">
            @csrf

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <!-- Role -->
                <div>
                    <label for="role" style="display:block; font-weight:600; margin-bottom:6px;">Vai trò</label>
                    <select name="role" id="role" required 
                            style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:8px;">
                        <option value="">-- Chọn role --</option>
                        <option value="truongkhoa">Trưởng khoa</option>
                        <option value="truongbomon">Trưởng bộ môn</option>
                        <option value="giangvien">Giảng viên</option>
                    </select>
                </div>

                <!-- Lecture ID -->
                <div>
                    <label for="lecture_id" style="display:block; font-weight:600; margin-bottom:6px;">Giảng viên (ID)</label>
                    <input type="number" name="lecture_id" id="lecture_id" placeholder="ID giảng viên" 
                           style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:8px;">
                </div>

                <!-- Name -->
                <div>
                    <label for="name" style="display:block; font-weight:600; margin-bottom:6px;">Họ và tên</label>
                    <input type="text" name="name" id="name" required 
                           style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:8px;">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" style="display:block; font-weight:600; margin-bottom:6px;">Email</label>
                    <input type="email" name="email" id="email" required 
                           style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:8px;">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" style="display:block; font-weight:600; margin-bottom:6px;">Mật khẩu</label>
                    <input type="password" name="password" id="password" required 
                           style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:8px;">
                </div>

                <!-- User Type -->
                <div>
                    <label for="user_type" style="display:block; font-weight:600; margin-bottom:6px;">Loại tài khoản</label>
                    <input type="text" name="user_type" id="user_type" required placeholder="staff / admin / ..."
                           style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:8px;">
                </div>

                <!-- Status -->
                <div>
                    <label for="status_id" style="display:block; font-weight:600; margin-bottom:6px;">Trạng thái</label>
                    <select name="status_id" id="status_id" required 
                            style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:8px;">
                        <option value="1">Hoạt động</option>
                        <option value="0">Không hoạt động</option>
                    </select>
                </div>
            </div>

            <!-- Submit -->
            <div style="text-align:right; margin-top:20px;">
                <button type="button" onclick="document.getElementById('addUserModal').style.display='none'" 
                        style="padding:8px 14px; margin-right:10px; border:none; background:#9ca3af; color:#fff; border-radius:6px;">
                    Hủy
                </button>
                <button type="submit" 
                        style="padding:8px 14px; border:none; background:#16a34a; color:#fff; border-radius:6px;">
                    Lưu
                </button>
            </div>
        </form>
    </div>
</div>


@endsection
