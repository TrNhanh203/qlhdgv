@extends('layouts.appbomon')

@section('title', 'Dashboard - Trưởng Bộ Môn')

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
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        table th,
        table td {
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
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        body.dark-mode .uni-card:hover {
            background: #374151;
        }

        body.dark-mode .uni-details {
            background: #2c2c3e;
            color: #f3f4f6;
        }

        body.dark-mode table th {
            background: #374151;
            color: #f3f4f6;
        }

        body.dark-mode table td {
            border-color: #4b5563;
        }

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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
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
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
        }

        body.dark-mode .stat-label {
            color: #9ca3af;
        }

        body.dark-mode .stat-value {
            color: #f9fafb;
        }
    </style>

    <div class="container-fluid">
        <!-- ===== Header Title ===== -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">
                <i class="bi bi-people me-2"></i>Dashboard Trưởng Bộ Môn
            </h2>
            <a href="{{ route('truongbomon.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Về Dashboard
            </a>
        </div>

        <!-- ===== Thống kê tổng quan ===== -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="dashboard-card card-primary d-flex align-items-center justify-content-between">
                    <div>
                        <h5>Giảng viên</h5>
                        <h2>{{ $stats['total_lecturers'] }}</h2>
                    </div>
                    <i class="bi bi-person-badge-fill dashboard-icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card card-success d-flex align-items-center justify-content-between">
                    <div>
                        <h5>Môn học</h5>
                        <h2>{{ $stats['total_courses'] }}</h2>
                    </div>
                    <i class="bi bi-book-fill dashboard-icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card card-warning d-flex align-items-center justify-content-between">
                    <div>
                        <h5>Lịch giảng</h5>
                        <h2>{{ $stats['total_teaching_duties'] }}</h2>
                    </div>
                    <i class="bi bi-calendar-check-fill dashboard-icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card card-info d-flex align-items-center justify-content-between">
                    <div>
                        <h5>Cuộc họp</h5>
                        <h2>{{ $stats['total_meetings'] }}</h2>
                    </div>
                    <i class="bi bi-people-fill dashboard-icon"></i>
                </div>
            </div>
        </div>

        <!-- ===== Biểu đồ và bảng thống kê ===== -->
        <div class="row">
            <!-- Thống kê giảng viên -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Thống kê giảng viên</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Họ tên</th>
                                        <th>Học vị</th>
                                        <th>Lịch giảng</th>
                                        <th>Coi thi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lecturerStats as $lecturer)
                                        <tr>
                                            {{-- <td>{{ $lecturer['name'] }}</td>
                    <td>{{ $lecturer['degree'] ?? 'Chưa cập nhật' }}</td>
                    <td>{{ $lecturer['teaching_count'] }}</td>
                    <td>{{ $lecturer['exam_count'] }}</td> --}}

                                            <td>{{ $lecturer->name }}</td>
                                            <td>{{ $lecturer->degree ?? 'Chưa cập nhật' }}</td>
                                            <td>{{ $lecturer->teaching_count }}</td>
                                            <td>{{ $lecturer->exam_count }}</td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Chưa có dữ liệu</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Biểu đồ giảng dạy theo tuần -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Giảng dạy theo tuần</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="weeklyChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== Cuộc họp gần đây ===== -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Cuộc họp gần đây</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tiêu đề</th>
                                        <th>Ngày họp</th>
                                        <th>Thời gian</th>
                                        <th>Địa điểm</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentMeetings as $meeting)
                                        <tr>
                                            <td>{{ $meeting->meeting_title }}</td>
                                            <td>{{ $meeting->meeting_date ? $meeting->meeting_date->format('d/m/Y') : 'N/A' }}
                                            </td>
                                            <td>
                                                @if ($meeting->start_time && $meeting->end_time)
                                                    {{ $meeting->start_time->format('H:i') }} -
                                                    {{ $meeting->end_time->format('H:i') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $meeting->location ?? 'N/A' }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $meeting->status === 'completed' ? 'success' : ($meeting->status === 'scheduled' ? 'warning' : 'secondary') }}">
                                                    {{ $meeting->status === 'completed' ? 'Đã hoàn thành' : ($meeting->status === 'scheduled' ? 'Đã lên lịch' : 'Chưa xác định') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Chưa có cuộc họp nào</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
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
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        }
    </style>
@endsection

@push('scripts')
    <script>
        // Biểu đồ giảng dạy theo tuần
        const weeklyData = @json($weeklyStats);
        const weekLabels = Object.keys(weeklyData).map(week => `Tuần ${week}`);
        const weekData = Object.values(weeklyData);

        new Chart(document.getElementById('weeklyChart'), {
            type: 'bar',
            data: {
                labels: weekLabels,
                datasets: [{
                    label: 'Số lịch giảng',
                    data: weekData,
                    backgroundColor: '#4e73df',
                    borderColor: '#4e73df',
                    borderWidth: 1
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
