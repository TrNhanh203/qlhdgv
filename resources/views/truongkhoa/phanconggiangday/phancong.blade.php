@extends('layouts.apptruongkhoa')

@section('title', 'Phân công giảng dạy - Trưởng Khoa')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chalkboard-teacher mr-2"></i>
                        Phân công giảng dạy
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAssignmentModal">
                            <i class="fas fa-plus"></i> Thêm phân công
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Thông tin năm học hiện tại -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Năm học:</strong> {{ $currentYear->year_code ?? 'Chưa thiết lập' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Học kỳ:</strong> {{ $currentSemester->semester_name ?? 'Chưa thiết lập' }}
                        </div>
                    </div>

                    <!-- Bảng phân công hiện tại -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>STT</th>
                                    <th>Học phần</th>
                                    <th>Giảng viên</th>
                                    <th>Số tiết</th>
                                    <th>Phòng học</th>
                                    <th>Thời gian</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($currentAssignments as $index => $assignment)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $assignment->course->course_code }}</strong><br>
                                        <small>{{ $assignment->course->course_name }}</small>
                                    </td>
                                    <td>{{ $assignment->lecture->full_name }}</td>
                                    <td>{{ $assignment->hours }} tiết</td>
                                    <td>{{ $assignment->room->name ?? 'Chưa phân' }}</td>
                                    <td>
                                        <small>
                                            {{ \Carbon\Carbon::parse($assignment->start_time)->format('d/m/Y H:i') }}<br>
                                            - {{ \Carbon\Carbon::parse($assignment->end_time)->format('d/m/Y H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($assignment->status == 'pending')
                                            <span class="badge badge-warning">Chờ duyệt</span>
                                        @elseif($assignment->status == 'approved')
                                            <span class="badge badge-success">Đã duyệt</span>
                                        @elseif($assignment->status == 'rejected')
                                            <span class="badge badge-danger">Từ chối</span>
                                        @elseif($assignment->status == 'completed')
                                            <span class="badge badge-info">Hoàn thành</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    onclick="editAssignment({{ $assignment->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="deleteAssignment({{ $assignment->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Chưa có phân công nào</td>
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

<!-- Modal thêm phân công -->
<div class="modal fade" id="addAssignmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm phân công giảng dạy</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('truongkhoa.phanconggiangday.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Học phần <span class="text-danger">*</span></label>
                                <select name="course_id" class="form-control" required>
                                    <option value="">Chọn học phần</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">
                                            {{ $course->course_code }} - {{ $course->course_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Giảng viên <span class="text-danger">*</span></label>
                                <select name="lecture_id" class="form-control" required>
                                    <option value="">Chọn giảng viên</option>
                                    @foreach($lecturers as $lecturer)
                                        <option value="{{ $lecturer->id }}">
                                            {{ $lecturer->full_name }} ({{ $lecturer->lecturer_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Số tiết <span class="text-danger">*</span></label>
                                <input type="number" name="hours" class="form-control" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Phòng học</label>
                                <select name="room_id" class="form-control">
                                    <option value="">Chọn phòng học</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Khối lượng hiện tại</label>
                                <div id="currentWorkload" class="form-control-plaintext text-muted">
                                    Chọn giảng viên để xem
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Thời gian bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_time" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Thời gian kết thúc <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_time" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Ghi chú</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <input type="hidden" name="academic_year_id" value="{{ $currentYear->id ?? '' }}">
                    <input type="hidden" name="semester_id" value="{{ $currentSemester->id ?? '' }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Xem khối lượng công việc của giảng viên
$('select[name="lecture_id"]').change(function() {
    const lecturerId = $(this).val();
    const semesterId = '{{ $currentSemester->id ?? "" }}';
    
    if (lecturerId && semesterId) {
        $.get(`/truongkhoa/phanconggiangday/workload/${lecturerId}/${semesterId}`, function(data) {
            $('#currentWorkload').text(`${data.total_hours} tiết`);
        });
    } else {
        $('#currentWorkload').text('Chọn giảng viên để xem');
    }
});

// Sửa phân công
function editAssignment(id) {
    alert('Chức năng sửa đang được phát triển');
}

// Xóa phân công
function deleteAssignment(id) {
    if (confirm('Bạn có chắc chắn muốn xóa phân công này?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/truongkhoa/phanconggiangday/${id}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = '{{ csrf_token() }}';
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
