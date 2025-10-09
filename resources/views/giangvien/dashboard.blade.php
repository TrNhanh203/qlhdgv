@extends('layouts.appGV')

@section('title', 'Dashboard - Giảng Viên')

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
<div class="container-fluid">
  <!-- ===== Header Title ===== -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">
      <i class="bi bi-person-badge me-2"></i>Dashboard Giảng Viên
    </h2>
    <a href="{{ route('giangvien.dashboard') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Về Dashboard
    </a>
  </div>

  <!-- ===== Thống kê cá nhân ===== -->
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="dashboard-card card-primary d-flex align-items-center justify-content-between">
        <div>
          <h5>Lịch giảng dạy</h5>
          <h2>{{ $stats['total_teaching_duties'] }}</h2>
        </div>
        <i class="bi bi-calendar-check-fill dashboard-icon"></i>
      </div>
    </div>
    <div class="col-md-3">
      <div class="dashboard-card card-success d-flex align-items-center justify-content-between">
        <div>
          <h5>Coi thi</h5>
          <h2>{{ $stats['total_exam_proctorings'] }}</h2>
        </div>
        <i class="bi bi-file-earmark-check-fill dashboard-icon"></i>
      </div>
    </div>
    <div class="col-md-3">
      <div class="dashboard-card card-warning d-flex align-items-center justify-content-between">
        <div>
          <h5>Cuộc họp</h5>
          <h2>{{ $stats['total_meetings'] }}</h2>
        </div>
        <i class="bi bi-people-fill dashboard-icon"></i>
      </div>
    </div>
    <div class="col-md-3">
      <div class="dashboard-card card-info d-flex align-items-center justify-content-between">
        <div>
          <h5>Giờ giảng</h5>
          <h2>{{ $stats['total_workload_hours'] }}</h2>
        </div>
        <i class="bi bi-clock-fill dashboard-icon"></i>
      </div>
    </div>
  </div>

  <!-- ===== Lịch giảng và coi thi ===== -->
  <div class="row">
    <!-- Lịch giảng gần đây -->
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Lịch giảng gần đây</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th>Môn học</th>
                  <th>Ngày</th>
                  <th>Phòng</th>
                  <th>Thời gian</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentTeaching as $teaching)
                  <tr>
                    <td>{{ $teaching->course->course_name ?? 'N/A' }}</td>
                    <td>{{ $teaching->teaching_date ? $teaching->teaching_date->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $teaching->room->room_name ?? 'N/A' }}</td>
                    <td>
                      @if($teaching->start_time && $teaching->end_time)
                        {{ $teaching->start_time->format('H:i') }} - {{ $teaching->end_time->format('H:i') }}
                      @else
                        N/A
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center">Chưa có lịch giảng</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Lịch coi thi sắp tới -->
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-file-earmark-check me-2"></i>Lịch coi thi sắp tới</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th>Kỳ thi</th>
                  <th>Ngày</th>
                  <th>Phòng</th>
                  <th>Vai trò</th>
                </tr>
              </thead>
              <tbody>
                @forelse($upcomingExams as $exam)
                  <tr>
                    <td>{{ $exam->exam->exam_name ?? 'N/A' }}</td>
                    <td>{{ $exam->exam_date ? $exam->exam_date->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $exam->room->room_name ?? 'N/A' }}</td>
                    <td>
                      <span class="badge bg-{{ $exam->role === 'proctor' ? 'primary' : 'secondary' }}">
                        {{ $exam->role === 'proctor' ? 'Giám thị' : 'Trợ giảng' }}
                      </span>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center">Chưa có lịch coi thi</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ===== Biểu đồ và cuộc họp ===== -->
  <div class="row mt-4">
    <!-- Biểu đồ giảng dạy theo tháng -->
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Giảng dạy theo tháng</h5>
        </div>
        <div class="card-body">
          <canvas id="monthlyChart" height="200"></canvas>
        </div>
      </div>
    </div>

    <!-- Cuộc họp sắp tới -->
    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Cuộc họp sắp tới</h5>
        </div>
        <div class="card-body">
          @forelse($upcomingMeetings as $meeting)
            <div class="border-bottom pb-2 mb-2">
              <h6 class="mb-1">{{ $meeting->meeting_title }}</h6>
              <small class="text-muted">
                <i class="bi bi-calendar me-1"></i>
                {{ $meeting->meeting_date ? $meeting->meeting_date->format('d/m/Y') : 'N/A' }}
              </small><br>
              <small class="text-muted">
                <i class="bi bi-clock me-1"></i>
                @if($meeting->start_time && $meeting->end_time)
                  {{ $meeting->start_time->format('H:i') }} - {{ $meeting->end_time->format('H:i') }}
                @else
                  N/A
                @endif
              </small>
            </div>
          @empty
            <p class="text-muted text-center">Chưa có cuộc họp nào</p>
          @endforelse
        </div>
      </div>
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
  // Biểu đồ giảng dạy theo tháng
  const monthlyData = @json($monthlyStats);
  const monthLabels = Object.keys(monthlyData).map(month => `Tháng ${month}`);
  const monthData = Object.values(monthlyData);

  new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: {
      labels: monthLabels,
      datasets: [{
        label: 'Số lịch giảng',
        data: monthData,
        borderColor: '#4e73df',
        backgroundColor: 'rgba(78, 115, 223, 0.1)',
        tension: 0.4,
        fill: true
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1
          }
        }
      },
      plugins: {
        legend: {
          display: false
        }
      }
    }
  });
</script>
@endpush
