@extends('layouts.appsuperadmin')

@section('title', 'Dashboard - Super Admin')

@section('content')
<div class="row g-4">
  <!-- Tổng số trường -->
  <div class="col-md-3">
    <div class="dashboard-card card-primary d-flex align-items-center justify-content-between">
      <div>
        <h5>Trường</h5>
        <h2>{{ $stats['total_universities'] }}</h2>
      </div>
      <i class="bi bi-building-fill dashboard-icon"></i>
    </div>
  </div>
  <!-- Tổng số Admin trường -->
  <div class="col-md-3">
    <div class="dashboard-card card-success d-flex align-items-center justify-content-between">
      <div>
        <h5>Admin trường</h5>
        <h2>{{ $stats['total_admins'] }}</h2>
      </div>
      <i class="bi bi-shield-lock-fill dashboard-icon"></i>
    </div>
  </div>
  <!-- Tổng số người dùng -->
  <div class="col-md-3">
    <div class="dashboard-card card-warning d-flex align-items-center justify-content-between">
      <div>
        <h5>Người dùng</h5>
        <h2>{{ $stats['total_users'] }}</h2>
      </div>
      <i class="bi bi-people-fill dashboard-icon"></i>
    </div>
  </div>
  <!-- Hoạt động hệ thống -->
  <div class="col-md-3">
    <div class="dashboard-card card-info d-flex align-items-center justify-content-between">
      <div>
        <h5>Hoạt động hệ thống</h5>
        <h2>{{ $stats['system_uptime'] }}%</h2>
      </div>
      <i class="bi bi-speedometer2 dashboard-icon"></i>
    </div>
  </div>
</div>

<!-- Biểu đồ -->
<div class="row mt-5">
  <div class="col-md-6">
    <div class="card p-3">
      <h5>Phân bổ người dùng theo vai trò</h5>
      <canvas id="chartRoles"></canvas>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card p-3">
      <h5>Số lượng trường theo khu vực</h5>
      <canvas id="chartSchools"></canvas>
    </div>
  </div>
</div>

<!-- Nhật ký hoạt động -->
<div class="row mt-5">
  <div class="col-md-12">
    <div class="card p-3">
      <h5>Nhật ký hệ thống (5 hoạt động gần nhất)</h5>
      <table>
        <thead>
          <tr>
            <th>Thời gian</th>
            <th>Người thực hiện</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentActivities as $activity)
            <tr>
              <td>{{ $activity->logged_at ? \Carbon\Carbon::parse($activity->logged_at)->format('d/m/Y H:i') : '-' }}</td>
              <td>{{ $activity->user->lecture->full_name ?? 'Hệ thống' }}</td>
              <td>{{ $activity->action }}</td>
            </tr>
          @empty
            <tr><td colspan="3" class="text-center">Chưa có hoạt động nào</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
  .dashboard-icon {
    font-size: 2.5rem;
    opacity: 0.8;
    transition: transform 0.3s ease, opacity 0.3s ease;
  }

  .dashboard-card:hover .dashboard-icon {
    transform: rotate(15deg) scale(1.2);
    opacity: 1;
  }

  .dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
  }
</style>
@endsection

@push('scripts')
<script>
  // Chart Roles - Phân bổ người dùng theo vai trò
  new Chart(document.getElementById('chartRoles'), {
    type: 'doughnut',
    data: {
      labels: ['SuperAdmin', 'Admin Trường', 'Trưởng khoa', 'Trưởng bộ môn', 'Giảng viên'],
      datasets: [{
        data: [
          {{ $roleDistribution['superadmin'] ?? 0 }},
          {{ $roleDistribution['admin'] ?? 0 }},
          {{ $roleDistribution['truongkhoa'] ?? 0 }},
          {{ $roleDistribution['truongbomon'] ?? 0 }},
          {{ $roleDistribution['giangvien'] ?? 0 }}
        ],
        backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b', '#858796']
      }]
    }
  });

  // Chart Schools - Số lượng trường theo khu vực
  new Chart(document.getElementById('chartSchools'), {
    type: 'bar',
    data: {
      labels: {!! json_encode(array_keys($schoolsByRegion)) !!},
      datasets: [{
        label: 'Số lượng trường',
        data: {!! json_encode(array_values($schoolsByRegion)) !!},
        backgroundColor: '#36b9cc'
      }]
    },
    options: { scales: { y: { beginAtZero: true } } }
  });
</script>
@endpush
