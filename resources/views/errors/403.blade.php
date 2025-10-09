@extends('layouts.errors')

@section('content')
<div style="display:flex; justify-content:center; align-items:center; min-height:100vh; background:#f9fafb;">
    <div style="text-align:center; padding:40px; background:#fff; border-radius:16px; box-shadow:0 10px 25px rgba(0,0,0,0.1); max-width:500px; width:100%;">
        
        <!-- Icon cảnh báo -->
        <div style="font-size:70px; color:#ef4444; margin-bottom:15px;">
            ⚠️
        </div>

        <!-- Mã lỗi -->
        <h1 style="font-size:64px; font-weight:bold; color:#ef4444; margin:0;">403</h1>
        <h2 style="margin:10px 0; font-size:22px; color:#374151;">KHÔNG CÓ QUYỀN TRUY CẬP</h2>
        <p style="color:#6b7280; margin-bottom:30px;">
            Xin lỗi, bạn không có quyền để truy cập vào trang này.
        </p>

        <!-- Nút quay lại -->
        <div style="display:flex; justify-content:center; gap:10px;">
            <a href="{{ url()->previous() }}" 
               style="padding:12px 24px; background:#3b82f6; color:#fff; text-decoration:none; border-radius:8px; font-weight:500; transition:0.3s;">
               ⬅ 🏠Quay lại
            </a>
            
        </div>
    </div>
</div>
@endsection
