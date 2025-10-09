@extends('layouts.app')

@section('title', 'Dashboard - Admin')

@section('content')
<div class="container-fluid">
  <!-- ===== Header Title ===== -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">
      <i class="bi bi-building me-2"></i>Thông tin Trường
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
          <img src="{{ $university?->logo ? asset($university->logo) : asset('logos/default.png') }}" 
            alt="Logo {{ $university?->university_name ?? 'Chưa cập nhật' }}" 
            class="img-fluid rounded mb-2" 
            style="max-height:120px;">

          <p class="fw-bold">Mã trường: {{ $universityCodeShort ?? 'N/A' }}</p>
        </div>
        <div class="col-md-9">
          <p><strong>Tên trường:</strong> {{ $university?->university_name ?? 'Chưa cập nhật' }}</p>
          <p><strong>Loại hình:</strong> {{ $university?->university_type ?? 'Chưa cập nhật' }}</p>
          <p><strong>Địa chỉ:</strong> {{ $university?->address ?? 'Chưa cập nhật' }}</p>
          <p><strong>Số điện thoại:</strong> {{ $university?->phone ?? 'Chưa cập nhật' }}</p>
          <p><strong>Email:</strong> {{ $university?->email ?? 'Chưa cập nhật' }}</p>
          <p><strong>Website:</strong> 
              @if($university?->website)
                  <a href="{{ $university->website }}" target="_blank">{{ $university->website }}</a>
              @else Chưa cập nhật @endif
          </p>
          <p><strong>Fanpage:</strong> 
              @if($university?->fanpage)
                  <a href="{{ $university->fanpage }}" target="_blank">{{ $university->fanpage }}</a>
              @else Chưa cập nhật @endif
          </p>
          <p><strong>Ngày thành lập:</strong> {{ $university?->founded_date ? \Carbon\Carbon::parse($university->founded_date)->format('d/m/Y') : 'Chưa cập nhật' }}</p>
          <p><strong>Trạng thái:</strong> 
            <span class="badge 
                @if($university?->status_id == 1) bg-success
                @elseif($university?->status_id == 3) bg-warning
                @elseif($university?->status_id == 2) bg-danger
                @else bg-secondary
                @endif">
                {{ $university?->status_id?->name ?? 'Đang hoạt động' }}
            </span>
        </p>
        </div>
      </div>
      <div class="mt-3">
        <strong>Giới thiệu:</strong>
        <p>{{ $university?->description ?? 'Chưa có thông tin giới thiệu' }}</p>
      </div>
      <button class="btn btn-primary mt-3" onclick="openUniversityModal()">Cập nhật thông tin trường</button>
    </div>
  </div>

  <!-- Modal Cập nhật trường -->
  <div id="universityModal" class="modal">
    <div class="modal-content" style="width:600px; max-width:95%; padding:25px;">
        <h4>Cập nhật thông tin trường</h4>
        <form id="universityForm" method="POST" enctype="multipart/form-data" onsubmit="event.preventDefault(); saveUniversity();">
        @csrf
        @method('PUT')
        <input type="hidden" id="universityId" value="{{ $university->id }}">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                <input type="text" id="universityName" name="university_name" class="form-control" value="{{ $university->university_name }}" placeholder="Tên trường">
                <input type="text" id="universityType" name="university_type" class="form-control" value="{{ $university->university_type }}" placeholder="Loại hình">
                <input type="text" id="address" name="address" class="form-control" value="{{ $university->address }}" placeholder="Địa chỉ">
                <input type="text" id="phone" name="phone" class="form-control" value="{{ $university->phone }}" placeholder="Điện thoại">
                <input type="email" id="email" name="email" class="form-control" value="{{ $university->email }}" placeholder="Email">
                <input type="text" id="website" name="website" class="form-control" value="{{ $university->website }}" placeholder="Website">
                <input type="text" id="fanpage" name="fanpage" class="form-control" value="{{ $university->fanpage }}" placeholder="Fanpage">
                <input type="date" id="foundedDate" name="founded_date" class="form-control" value="{{ $university->founded_date ? \Carbon\Carbon::parse($university->founded_date)->format('Y-m-d') : '' }}">
                <div style="grid-column: span 2;">
                    <label>Logo hiện tại</label><br>
                    <img src="{{ $university->logo ? asset($university->logo) : asset('logos/default.png') }}" 
                        alt="Logo {{ $university->university_name }}" 
                        class="img-fluid rounded mb-2" 
                        style="max-height:120px;">
                    <input type="file" id="logo" name="logo" class="form-control" accept="image/*">
                </div>
                <div style="grid-column: span 2;">
                    <textarea id="description" name="description" class="form-control" placeholder="Mô tả">{{ $university->description }}</textarea>
                </div>
            </div>
            <div class="text-end mt-3">
                <button type="button" class="btn btn-secondary" onclick="closeUniversityModal()">Đóng</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
  </div>

  <!-- ===== Thống kê & Biểu đồ ===== -->
  <div class="row g-4 mt-4">
    <div class="col-md-3"><div class="dashboard-card p-3" style="background:#007bff;color:white"><h5>Khoa/Viện</h5><h2>{{ $stats['total_faculties'] }}</h2></div></div>
    <div class="col-md-3"><div class="dashboard-card p-3" style="background:#ff8800;color:white"><h5>Bộ môn</h5><h2>{{ $stats['total_departments'] }}</h2></div></div>
    <div class="col-md-3"><div class="dashboard-card p-3" style="background:#28a745;color:white"><h5>Giảng viên</h5><h2>{{ $stats['total_lecturers'] }}</h2></div></div>
    <div class="col-md-3"><div class="dashboard-card p-3" style="background:#ffc107;color:white"><h5>Phòng học</h5><h2>{{ $stats['total_rooms'] }}</h2></div></div>
    <div class="col-md-3"><div class="dashboard-card p-3" style="background:#17a2b8;color:white"><h5>Học phần</h5><h2>{{ $stats['total_courses'] }}</h2></div></div>
    <div class="col-md-3"><div class="dashboard-card p-3" style="background:#6f42c1;color:white"><h5>Kỳ thi</h5><h2>{{ $stats['total_exams'] }}</h2></div></div>
  </div>
  
  <div class="row mt-5">
    <div class="col-lg-6"><div class="card p-3"><h5>Giảng viên phân công theo khoa</h5><canvas id="chartLecturersAssigned"></canvas></div></div>
      <div class="col-lg-6"><div class="card p-3"><h5>Giảng viên theo Bộ môn</h5><canvas id="chartLecturersByDept"></canvas></div></div>
      
  </div>

  <div class="row mt-5">
      <div class="col-lg-6"><div class="card p-3"><h5>Học phần mở theo Học kỳ</h5><canvas id="chartCoursesByYearSemester"></canvas></div></div>
      
      <div class="col-lg-6"><div class="card p-3"><h5>Bộ môn theo Khoa</h5><canvas id="departmentsByFacultyChart"></canvas></div></div>
  </div>
    <div class="row mt-5">
    <div class="col-lg-12">
        <div class="card p-3">
            <h5>Học kỳ theo Năm học</h5>
            <canvas id="chartSemestersListByYear"></canvas>
        </div>
    </div>
</div>
  <div class="row mt-5">
      <div class="col-lg-12"><div class="card p-3"><h5>Học phần mở theo Học kỳ theo Năm học</h5><canvas id="chartSemesterByYear"></canvas></div></div>
  </div>
</div>

<style>

.dashboard-card{border-radius:10px;box-shadow:0 4px 6px rgba(0,0,0,0.1)}
.modal {
  display: none;
  position: fixed;
  top:0;left:0;width:100%;height:100%;
  background:rgba(0,0,0,0.5);
  justify-content:center;
  align-items:center;
  z-index:9999
}

.modal-content{background:#fff;padding:20px;border-radius:8px;width:500px;max-width:90%}
</style>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

// ===== Giảng viên theo Bộ môn (Bar) =====
const lecturersByDept = @json($lecturersByDept);
new Chart(document.getElementById('chartLecturersByDept'), {
    type: 'bar',
    data: {
        labels: lecturersByDept.map(x=>x.department),
        datasets:[{
            label:'Số giảng viên',
            data: lecturersByDept.map(x=>x.total),
            backgroundColor:'#0d6efd'
        }]
    },
    options:{responsive:true,scales:{y:{beginAtZero:true}}}
});

// ===== Học phần theo Học kỳ (Line) =====
const rawData = @json($coursesByYearSemester);
const yearsCourses = [...new Set(rawData.map(i=>i.year_code))];
const semestersCourse = [...new Set(rawData.map(i=>i.semester_name))];
const datasetsCourses = semestersCourse.map((s,i)=>({
    label:s,
    data:yearsCourses.map(y=>{
        const f=rawData.find(r=>r.year_code===y&&r.semester_name===s);
        return f?f.total:0;
    }),
    borderColor:`hsl(${i*60},70%,50%)`,
    backgroundColor:`hsl(${i*60},70%,70%)`,
    tension:0.3
}));
new Chart(document.getElementById('chartCoursesByYearSemester'), {
    type:'line', 
    data:{labels:yearsCourses,datasets:datasetsCourses},
    options:{responsive:true,scales:{y:{beginAtZero:true}}}
});

// ===== Bộ môn theo Khoa (Line) =====
const departmentsByFaculty = @json($departmentsByFaculty);
new Chart(document.getElementById('departmentsByFacultyChart'), {
    type:'line',
    data:{
        labels:departmentsByFaculty.map(item=>item.faculty),
        datasets:[{
            label:'Số bộ môn',
            data:departmentsByFaculty.map(item=>item.total),
            borderColor:'#28a745',
            backgroundColor:'rgba(40,167,69,0.3)',
            tension:0.3
        }]
    },
    options:{responsive:true,scales:{y:{beginAtZero:true}}}
});

// ===== Giảng viên phân công theo Khoa (Bar) =====
new Chart(document.getElementById('chartLecturersAssigned'), {
    type:'bar',
    data:{
        labels:{!! json_encode($giangVienTheoKhoa->pluck('ten_khoa')) !!},
        datasets:[{
            label:'Số giảng viên',
            data:{!! json_encode($giangVienTheoKhoa->pluck('giangviens_count')) !!},
            backgroundColor:'#ffc107'
        }]
    },
    options:{responsive:true,scales:{y:{beginAtZero:true}}}
});

// =====học phần Học kỳ theo Năm học (Stacked Bar) =====
const semestersByYear = @json($semestersByYear);
const yearsSemesters = [...new Set(semestersByYear.map(i => i.year_code))];
const semestersList = [...new Set(semestersByYear.map(i => i.semester_name))];
const datasetsSemester = semestersList.map((s, i) => ({
    label: s,
    data: yearsSemesters.map(y => {
        const f = semestersByYear.find(r => r.year_code === y && r.semester_name === s);
        return f ? f.total : 0;
    }),
    backgroundColor: `hsl(${i * 80}, 70%, 55%)`,
}));
new Chart(document.getElementById('chartSemesterByYear'), {
    type: 'bar',
    data: {labels: yearsSemesters, datasets: datasetsSemester},
    options: {
        responsive: true,
        plugins: {title: {display: true,text: 'Học phần mở theo học kỳ trong năm học'}},
        interaction: {mode: 'nearest', axis: 'x', intersect: false},
        scales: {x: {stacked: true}, y: {stacked: true, beginAtZero: true}}
    }
});
// ===== Danh sách Học kỳ theo Năm học (Stacked Bar) =====
const semestersListByYear = @json($semestersListByYear);
const yearsOnly = [...new Set(semestersListByYear.map(i => i.year_code))].sort();
const semestersOnly = [...new Set(semestersListByYear.map(i => i.semester_name).filter(Boolean))];

const datasetsList = semestersOnly.map((s, i) => ({
    label: s,
    data: yearsOnly.map(y => {
        const f = semestersListByYear.find(r => r.year_code === y && r.semester_name === s);
        return f ? 1 : 0; 
    }),
    backgroundColor: `hsl(${i * 90}, 65%, 55%)`,
}));

new Chart(document.getElementById('chartSemestersListByYear'), {
    type: 'bar',
    data: { labels: yearsOnly, datasets: datasetsList },
    options: {
        responsive: true,
        plugins: { title: { display: true, text: 'Học kỳ theo Năm học' } },
        scales: { 
            x: { stacked: true }, 
            y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } } 
        }
    }
});


</script>
<script>
function saveUniversity() {
    let form = document.getElementById('universityForm');
    let formData = new FormData(form);

    fetch("{{ route('admin.truong.update', $university->id) }}", {
        method: "POST", // ✅ giữ POST, Laravel sẽ hiểu PUT nhờ _method=PUT
        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message ?? "Cập nhật thành công!");
            location.reload();
        } else {
            alert(data.message ?? "Có lỗi xảy ra!");
        }
    })
    .catch(err => {
        console.error(err);
        alert("Lỗi hệ thống!");
    });
}

</script>
<script>
function openUniversityModal() {
    document.getElementById('universityModal').style.display = 'flex';
}
function closeUniversityModal() {
    document.getElementById('universityModal').style.display = 'none';
}
</script>
@endpush


