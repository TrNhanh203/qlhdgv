@extends('layouts.appsuperadmin')

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

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h1 style="font-size:26px; font-weight:700; color:#2563eb; margin:0;">
            🎓 Danh sách Trường Đại học
        </h1>
        <div style="position:relative; width:300px;">
            <span style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#9ca3af;">🔍</span>
            <input type="text" id="searchInput" placeholder="Tìm kiếm trường..."
                   style="width:100%; padding:10px 12px 10px 34px; border:1px solid #d1d5db; border-radius:8px; transition:all 0.3s;">
        </div>
    </div>

    @foreach ($universities as $uni)
        <!-- Card Trường -->
        <div class="uni-card" onclick="toggleDetails('{{ $uni->id }}')">
            <div class="uni-title">🏫 {{ $uni->university_name }}</div>
            <span class="toggle-icon">▶</span>
        </div>

        <!-- Thông tin chi tiết -->
        <!-- Thông tin chi tiết -->
<div class="uni-details" id="details-{{ $uni->id }}">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon bg-blue-100 text-blue-600">🏢</div>
            <div class="stat-info">
                <p class="stat-label">Số khoa</p>
                <h3 class="stat-value">{{ $uni->faculties_count }}</h3>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-purple-100 text-purple-600">👨‍🏫</div>
            <div class="stat-info">
                <p class="stat-label">Số giảng viên</p>
                <h3 class="stat-value">{{ $uni->lectures_count }}</h3>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-yellow-100 text-yellow-600">🎓</div>
            <div class="stat-info">
                <p class="stat-label">CTĐT</p>
                <h3 class="stat-value">{{ $uni->education_programs_count }}</h3>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-pink-100 text-pink-600">📖</div>
            <div class="stat-info">
                <p class="stat-label">Học phần</p>
                <h3 class="stat-value">{{ $uni->courses_count }}</h3>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-indigo-100 text-indigo-600">🏫</div>
            <div class="stat-info">
                <p class="stat-label">Phòng học</p>
                <h3 class="stat-value">{{ $uni->rooms_count }}</h3>
            </div>
        </div>

        <!-- Số năm học -->
        <div class="stat-card">
            <div class="stat-icon bg-teal-100 text-teal-600">📅</div>
            <div class="stat-info">
                <p class="stat-label">Năm học</p>
                <h3 class="stat-value">{{ $uni->academic_years_count }}</h3>
            </div>
        </div>

        <!-- Số học kỳ -->
        <div class="stat-card">
            <div class="stat-icon bg-orange-100 text-orange-600">🗓️</div>
            <div class="stat-info">
                <p class="stat-label">Học kỳ</p>
                <h3 class="stat-value">{{ $uni->semesters_count }}</h3>
            </div>
        </div>

        <!-- Số kỳ thi -->
        <div class="stat-card">
            <div class="stat-icon bg-red-100 text-red-600">📝</div>
            <div class="stat-info">
                <p class="stat-label">Kỳ thi</p>
                <h3 class="stat-value">{{ $uni->exams_count }}</h3>
            </div>
        </div>

        
    </div>
</div>

    @endforeach
</div>

<script>
    function toggleDetails(id) {
        const card = event.currentTarget;
        const details = document.getElementById("details-" + id);

        // toggle
        const isVisible = details.style.display === "block";
        document.querySelectorAll(".uni-details").forEach(el => el.style.display = "none");
        document.querySelectorAll(".uni-card").forEach(el => el.classList.remove("active"));

        if (!isVisible) {
            details.style.display = "block";
            card.classList.add("active");
        }
    }

    // Search filter
    document.getElementById("searchInput").addEventListener("keyup", function() {
        let filter = this.value.toLowerCase();
        document.querySelectorAll(".uni-card").forEach(function(card) {
            let text = card.innerText.toLowerCase();
            card.style.display = text.includes(filter) ? "flex" : "none";
            let details = document.getElementById("details-" + card.getAttribute("onclick").match(/\d+/)[0]);
            if (details) details.style.display = "none";
        });
    });
</script>
@endsection