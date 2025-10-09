@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <!-- ===== Header Title ===== -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">
      <i class="bi bi-building me-2"></i> Quản lý Thông tin Trường
    </h2>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Về Dashboard
    </a>
  </div>

  <!-- ===== Thông tin Trường ===== -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white fw-bold">
      <i class="bi bi-info-circle me-2"></i> Thông tin Trường
    </div>
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-3 text-center">
          <img src="{{ asset('storage/logos/tdmu.png') }}" alt="Logo Trường" class="img-fluid rounded mb-2" style="max-height: 120px;">
          <p class="fw-bold">Mã trường: TDMU</p>
        </div>
        <div class="col-md-9">
          <p><strong>Tên trường:</strong> Trường Đại học Thủ Dầu Một</p>
          <p><strong>Loại hình:</strong> Công lập</p>
          <p><strong>Ngày thành lập:</strong> 24/06/2009</p>
          <p><strong>Khẩu hiệu:</strong> “Năng động – Hội nhập – Phát triển”</p>
          <p><strong>Địa chỉ:</strong> Số 06 Trần Văn Ơn, Phú Hòa, TP. Thủ Dầu Một, Bình Dương</p>
          <p><strong>Số điện thoại:</strong> (0274) 3834 694</p>
          <p><strong>Email liên hệ:</strong> info@tdmu.edu.vn</p>
          <p><strong>Website:</strong> <a href="https://tdmu.edu.vn" target="_blank">https://tdmu.edu.vn</a></p>
          <p><strong>Fanpage:</strong> 
            <a href="https://www.facebook.com/DaiHocThuDauMot" target="_blank">
              facebook.com/DaiHocThuDauMot
            </a>
          </p>
          <p><strong>Trạng thái:</strong> 
            <span class="badge bg-success">Đang hoạt động</span>
          </p>
        </div>
      </div>
      <div class="mt-3">
        <strong>Giới thiệu:</strong>
        <p>
          Trường Đại học Thủ Dầu Một là cơ sở giáo dục đại học công lập, trực thuộc UBND tỉnh Bình Dương,
          định hướng trở thành trường đại học trọng điểm trong khu vực Đông Nam Bộ, đào tạo đa ngành, gắn kết
          chặt chẽ với nhu cầu phát triển kinh tế - xã hội.
        </p>
      </div>
      <a href="#" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-pencil-square"></i> Cập nhật thông tin
      </a>
    </div>
  </div>
</div>
@endsection
