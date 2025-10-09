@extends('layouts.app')

@section('content')
<div class="container mt-6 pt-3">
<style>
    .gradient-text {
        background: linear-gradient(to bottom right, #dfc42fff, #0077ffff, #f148b1ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: bold;
    }

    .box-shadow {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .table th, .table td {
        text-align: center;
        vertical-align: middle;
    }

    .chart-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .chart-box {
        flex: 1 1 48%;
        max-width: 48%;
        background-color: #ffffff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.08);
    }

    canvas {
        width: 100% !important;
        height: 240px !important;
    }

    @media (max-width: 768px) {
        .chart-box {
            max-width: 100%;
        }
    }
</style>

<h3 class="mb-3 text-center py-3 rounded gradient-text">
    THỐNG KÊ & BÁO CÁO
</h3>

<div class="box-shadow mb-4">
    <form class="row g-3 mb-3">
        <div class="col-md-3">
            <input type="text" class="form-control" placeholder="Giảng viên">
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control" placeholder="Khoa">
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control" placeholder="Học kỳ">
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary w-100">Lọc</button>
        </div>
    </form>

    <h5 class="text-center fw-bold mb-4">Thống kê trực quan</h5>
    <div class="chart-container">
        <div class="chart-box">
            <canvas id="barChart"></canvas>
        </div>
        <div class="chart-box">
            <canvas id="pieChart"></canvas>
        </div>
    </div>

    <div class="table-responsive mt-4">
        <table class="table table-bordered table-hover table-sm">
            <thead class="table-primary">
                <tr>
                    <th>Giảng viên</th>
                    <th>Số tiết giảng</th>
                    <th>Số đề tài NCKH</th>
                    <th>Số SV hướng dẫn</th>
                    <th>Giờ công tác khác</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Trần Thị B</td>
                    <td>120</td>
                    <td>2</td>
                    <td>5</td>
                    <td>20</td>
                </tr>
                <tr>
                    <td>Nguyễn Văn A</td>
                    <td>100</td>
                    <td>1</td>
                    <td>3</td>
                    <td>15</td>
                </tr>
                <tr>
                    <td>Lê Thị C</td>
                    <td>90</td>
                    <td>3</td>
                    <td>4</td>
                    <td>18</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="/thongke/export/pdf" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Xuất PDF</a>
        <a href="/thongke/export/excel" class="btn btn-success"><i class="fas fa-file-excel"></i> Xuất Excel</a>
    </div>
</div>

{{-- Thêm Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const barCtx = document.getElementById('barChart').getContext('2d');
    const pieCtx = document.getElementById('pieChart').getContext('2d');

    // Dữ liệu giả
    const labels = ['Trần Thị B', 'Nguyễn Văn A', 'Lê Thị C'];
    const tietGiang = [120, 100, 90];
    const deTai = [2, 1, 3];

    // Biểu đồ cột
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Số tiết giảng',
                data: tietGiang,
                backgroundColor: ['#3498db', '#2ecc71', '#f1c40f']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: false }
            }
        }
    });

    // Biểu đồ tròn
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                label: 'Tỷ lệ đề tài NCKH',
                data: deTai,
                backgroundColor: ['#e74c3c', '#9b59b6', '#3498db']
            }]
        },
        options: {
            responsive: true,
            aspectRatio: 1.2,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endsection
