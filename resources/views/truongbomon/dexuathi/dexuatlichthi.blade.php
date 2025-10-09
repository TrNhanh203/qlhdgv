@extends('layouts.appbomon')

@section('title', 'Đề xuất lịch thi - Trưởng Bộ môn')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Đề xuất lịch thi
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addExamModal">
                            <i class="fas fa-plus"></i> Thêm kỳ thi
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

                    <!-- Bảng kỳ thi đã đề xuất -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>STT</th>
                                    <th>Học phần</th>
                                    <th>Tên kỳ thi</th>
                                    <th>Loại thi</th>
                                    <th>Hình thức</th>
                                    <th>Thời gian</th>
                                    <th>Phòng thi</th>
                                    <th>Số SV</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($exams as $index => $exam)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $exam->course->course_code }}</strong><br>
                                        <small>{{ $exam->course->course_name }}</small>
                                    </td>
                                    <td>{{ $exam->exam_name }}</td>
                                    <td>{{ $exam->exam_type }}</td>
                                    <td>{{ $exam->exam_form }}</td>
                                    <td>
                                        <small>
                                            {{ \Carbon\Carbon::parse($exam->exam_start)->format('d/m/Y H:i') }}<br>
                                            - {{ \Carbon\Carbon::parse($exam->exam_end)->format('d/m/Y H:i') }}
                                        </small>
                                    </td>
                                    <td>{{ $exam->room->name ?? 'Chưa phân' }}</td>
                                    <td>{{ $exam->expected_students }}</td>
                                    <td>
                                        @if($exam->status == 'pending')
                                            <span class="badge badge-warning">Chờ duyệt</span>
                                        @elseif($exam->status == 'approved')
                                            <span class="badge badge-success">Đã duyệt</span>
                                        @elseif($exam->status == 'rejected')
                                            <span class="badge badge-danger">Từ chối</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    onclick="editExam({{ $exam->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @if($exam->status != 'approved')
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="deleteExam({{ $exam->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Chưa có kỳ thi nào</td>
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

<!-- Modal thêm kỳ thi -->
<div class="modal fade" id="addExamModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm kỳ thi</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('truongbomon.dexuathi.store') }}" method="POST">
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
                                <label>Tên kỳ thi <span class="text-danger">*</span></label>
                                <input type="text" name="exam_name" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Loại thi <span class="text-danger">*</span></label>
                                <select name="exam_type" class="form-control" required>
                                    <option value="">Chọn loại thi</option>
                                    <option value="Giữa kỳ">Giữa kỳ</option>
                                    <option value="Cuối kỳ">Cuối kỳ</option>
                                    <option value="Thi lại">Thi lại</option>
                                    <option value="Thi cải thiện">Thi cải thiện</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Hình thức thi <span class="text-danger">*</span></label>
                                <select name="exam_form" class="form-control" required>
                                    <option value="">Chọn hình thức</option>
                                    <option value="Thi viết">Thi viết</option>
                                    <option value="Thi vấn đáp">Thi vấn đáp</option>
                                    <option value="Thi thực hành">Thi thực hành</option>
                                    <option value="Thi trực tuyến">Thi trực tuyến</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Ca thi</label>
                                <input type="text" name="exam_batch" class="form-control" placeholder="VD: Ca 1, Ca 2">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Thời gian bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="exam_start" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Thời gian kết thúc <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="exam_end" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phòng thi</label>
                                <select name="room_id" class="form-control">
                                    <option value="">Chọn phòng thi</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Số sinh viên dự kiến <span class="text-danger">*</span></label>
                                <input type="number" name="expected_students" class="form-control" min="1" required>
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
// Sửa kỳ thi
function editExam(id) {
    alert('Chức năng sửa đang được phát triển');
}

// Xóa kỳ thi
function deleteExam(id) {
    if (confirm('Bạn có chắc chắn muốn xóa kỳ thi này?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/truongbomon/dexuathi/${id}`;
        
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
