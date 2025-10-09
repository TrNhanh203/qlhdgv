<div class="modal fade" id="proctorDetailModal" tabindex="-1" aria-labelledby="proctorDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content rounded-3 shadow-lg">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="proctorDetailLabel">Xem chi tiết GV</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table align-middle table-bordered text-center">
            <thead class="table-light">
              <tr>
                <th>Họ và tên</th>
                <th>Mã tài khoản</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Trạng thái điểm danh</th>
                <th>Ghi chú</th>
                <th>Thời gian điểm danh</th>
                <th>Hành động</th>
              </tr>
            </thead>
            <tbody>
              @foreach($proctors as $proctor)
                <tr>
                  <td class="text-start">
                    <i class="bi bi-person"></i> {{ $proctor->lecture->full_name }}
                  </td>
                  <td>{{ $proctor->lecture->lecturer_code ?? '—' }}</td>
                  <td>
                    <a href="mailto:{{ $proctor->lecture->email }}">
                      {{ $proctor->lecture->email }}
                    </a>
                  </td>
                  <td>{{ $proctor->lecture->phone ?? '—' }}</td>
                  <td>
                    @if($proctor->checked_in)
                      <span class="badge bg-success">Đã điểm danh</span>
                    @else
                      <span class="badge bg-primary">Chưa điểm danh</span>
                    @endif
                  </td>
                  <td>{{ $proctor->notes ?? 'Chưa có ghi chú' }}</td>
                  <td>
                    {{ $proctor->updated_at?->format('d-m-Y | H:i:s') ?? '—' }}
                  </td>
                  <td>
                    <form action="{{ route('exam_proctorings.checkin', $proctor->id) }}" method="POST">
                      @csrf
                      @method('PUT')
                      <button type="submit" class="btn btn-sm btn-primary">
                        Thực hiện điểm danh
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- Pagination nếu cần --}}
        <div class="d-flex justify-content-end">
          {{ $proctors->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
