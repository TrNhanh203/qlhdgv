@extends('layouts.apptruongkhoa')

@section('title', 'Dashboard-Trưởng Khoa-' . auth()->user()->getUniversityCode())


@section('content')
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f9fafb;
        margin: 0;
    }

    .container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
    }

    /* Card trường */
    .uni-card {
        background: #fff;
        border-radius: 12px;
        padding: 18px 20px;
        margin-bottom: 14px;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s;
    }
    .uni-card:hover {
        background: #f1f5f9;
        transform: translateY(-2px);
    }

    .uni-title {
        font-size: 18px;
        font-weight: 600;
        color: #2563eb;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .toggle-icon {
        font-size: 20px;
        color: #6b7280;
        transition: transform 0.3s;
    }
    .uni-card.active .toggle-icon {
        transform: rotate(90deg);
        color: #2563eb;
    }

    /* Thông tin chi tiết */
    .uni-details {
        display: none;
        background: #fff;
        padding: 16px;
        margin-top: -10px;
        margin-bottom: 14px;
        border-radius: 0 0 12px 12px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 12px;
    }
    table th, table td {
        padding: 10px;
        border-bottom: 1px solid #e5e7eb;
        text-align: center;
    }
    table th {
        background: #f3f4f6;
        font-weight: 600;
    }

    /* Dark mode */
    body.dark-mode .uni-card {
        background: #1f2937;
        color: #f3f4f6;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
    }
    body.dark-mode .uni-card:hover { background: #374151; }
    body.dark-mode .uni-details { background: #2c2c3e; color: #f3f4f6; }
    body.dark-mode table th { background: #374151; color: #f3f4f6; }
    body.dark-mode table td { border-color: #4b5563; }
    /* Grid thống kê */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-top: 12px;
}

.stat-card {
    background: #f9fafb;
    border-radius: 12px;
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: all 0.3s;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.12);
}

.stat-icon {
    font-size: 28px;
    padding: 12px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-info {
    display: flex;
    flex-direction: column;
}
.card-body canvas {
  width: 100% !important;
}

.stat-label {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 4px;
}
.stat-value {
    font-size: 20px;
    font-weight: 700;
    color: #111827;
}

/* Dark mode */
body.dark-mode .stat-card {
    background: #1f2937;
    color: #f3f4f6;
    box-shadow: 0 2px 6px rgba(0,0,0,0.4);
}
body.dark-mode .stat-label { color: #9ca3af; }
body.dark-mode .stat-value { color: #f9fafb; }

</style>
<div class="container-fluid">
  <!-- ===== Header Title ===== -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">
      <i class="bi bi-building me-2"></i>Trưởng Khoa
      {{ auth()->user()->getFacultyName() ?? 'Chưa có khoa' }}
    </h2>
    
  </div>

  <!-- ===== Thống kê tổng quan ===== -->
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="dashboard-card card-primary d-flex align-items-center justify-content-between">
        <div>
          <h5>Bộ môn</h5>
          <h2>{{ $stats['total_departments'] }}</h2>
        </div>
        <i class="bi bi-people-fill dashboard-icon"></i>
      </div>
    </div>
    <div class="col-md-3">
      <div class="dashboard-card card-success d-flex align-items-center justify-content-between">
        <div>
          <h5>Giảng viên</h5>
          <h2>{{ $stats['total_lecturers'] }}</h2>
        </div>
        <i class="bi bi-person-badge-fill dashboard-icon"></i>
      </div>
    </div>
    <div class="col-md-3">
      <div class="dashboard-card card-warning d-flex align-items-center justify-content-between">
        <div>
          <h5>Môn học</h5>
          <h2>{{ $stats['total_courses'] }}</h2>
        </div>
        <i class="bi bi-book-fill dashboard-icon"></i>
      </div>
    </div>
    <div class="col-md-3">
      <div class="dashboard-card card-info d-flex align-items-center justify-content-between">
        <div>
          <h5>Lịch giảng</h5>
          <h2>{{ $stats['total_teaching_duties'] }}</h2>
        </div>
        <i class="bi bi-calendar-check-fill dashboard-icon"></i>
      </div>
    </div>
  </div>

  <!-- ===== Biểu đồ và bảng thống kê ===== -->
  <div class="row">
    <!-- Thống kê theo bộ môn -->
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Thống kê theo bộ môn</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th>Bộ môn</th>
                  <th>Giảng viên</th>
                  <th>Môn học</th>
                </tr>
              </thead>
              <tbody>
                @forelse($departmentStats as $dept)
                  <tr>
                    <td>{{ $dept['name'] }}</td>
                    <td>{{ $dept['lecturer_count'] }}</td>
                    <td>{{ $dept['course_count'] }}</td>
                  </tr>
                @empty
                  <tr><td colspan="3" class="text-center">Chưa có dữ liệu</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Thống kê theo bộ môn</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th>Bộ môn</th>
                  <th>Giảng viên</th>
                  <th>Môn học</th>
                </tr>
              </thead>
              <tbody>
                @forelse($departmentStats as $dept)
                  <tr>
                    <td>{{ $dept['name'] }}</td>
                    <td>{{ $dept['lecturer_count'] }}</td>
                    <td>{{ $dept['course_count'] }}</td>
                  </tr>
                @empty
                  <tr><td colspan="3" class="text-center">Chưa có dữ liệu</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
</div>

<div class="row mt-4">
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">
          <i class="bi bi-pie-chart-fill me-2"></i>
          Thống kê giảng viên theo bộ môn
        </h5>
      </div>
      <div class="card-body">
        <canvas id="lecturerChart" height="300"></canvas>
      </div>
    </div>
  </div>

  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Giảng dạy theo tháng</h5>
      </div>
      <div class="card-body">
        <canvas id="teachingChart" height="300"></canvas>
      </div>
    </div>
  </div>
</div>




  <!-- ===== Danh sách giảng viên gần đây ===== -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Danh sách giảng viên trong khoa</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th>STT</th>
                  <th>Họ tên</th>
                  <th>Bộ môn</th>
                  <th>Học vị</th>
                  <th>Số môn đang giảng</th>
                </tr>
              </thead>
              <tbody>
              @forelse($lecturers as $index => $lec)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $lec['name'] }}</td>
                  <td>{{ $lec['department'] }}</td>
                  <td>{{ $lec['degree'] }}</td>
                  <td>{{ $lec['course_count'] }}</td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center">Chưa có dữ liệu giảng viên</td></tr>
              @endforelse
            </tbody>

            </table>
          </div>
        </div>
        
      </div>
    </div>
  </div>
  <div class="mt-3">{{ $lecturers->links('pagination::bootstrap-5') }}</div>
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
  // Biểu đồ giảng viên theo bộ môn
  const lecturerData = @json($lecturerStats);
  const deptLabels = lecturerData.map(d => d.department);
  const deptCounts = lecturerData.map(d => d.lecturer_count);

  new Chart(document.getElementById('lecturerChart'), {
    type: 'bar', // hoặc 'pie' nếu bạn muốn tròn
    data: {
      labels: deptLabels,
      datasets: [{
        label: 'Số giảng viên',
        data: deptCounts,
        backgroundColor: [
          '#3b82f6',
          '#22c55e',
          '#f59e0b',
          '#ef4444',
          '#8b5cf6',
          '#ec4899',
          '#14b8a6'
        ],
        borderRadius: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
</script>

<script>
  // Biểu đồ giảng dạy theo tháng
  const teachingData = @json($teachingStats);
  const labels = Object.keys(teachingData).map(month => `Tháng ${month}`);
  const data = Object.values(teachingData);

  new Chart(document.getElementById('teachingChart'), {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Số lịch giảng',
        data: data,
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
