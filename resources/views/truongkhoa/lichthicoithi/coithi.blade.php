@extends('layouts.apptruongkhoa')

@section('title', 'Trưởng Khoa-' . auth()->user()->getUniversityCode())


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
@endsection