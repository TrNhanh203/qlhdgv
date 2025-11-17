@extends('layouts.appGV')

@section('content')
    <div class="container-fluid">

        <h4 class="mb-3">
            <i class="bi bi-journal-text"></i> Đề cương tôi được phân công
        </h4>

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Học phần</th>
                            <th>CTĐT</th>
                            <th>Vai trò</th>
                            <th>Hạn soạn</th>
                            <th>Trạng thái</th>
                            <th style="width:140px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assignments as $a)
                            <tr>
                                <td>
                                    <strong>{{ $a->course_code }}</strong> – {{ $a->course_name }}
                                </td>

                                <td>
                                    {{ $a->program_code }} ({{ $a->program_version_code }})
                                </td>

                                <td>
                                    @if ($a->role === 'chu_bien')
                                        <span class="badge bg-primary">Chủ biên</span>
                                    @elseif ($a->role === 'dong_bien')
                                        <span class="badge bg-secondary">Đồng biên</span>
                                    @else
                                        <span class="badge bg-info text-dark">Tham gia</span>
                                    @endif
                                </td>

                                <td>{{ $a->due_date ?? '—' }}</td>

                                <td>
                                    @if ($a->version_id)
                                        <span class="badge bg-success">
                                            V{{ $a->version_no }} – {{ $a->version_status }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">Chưa tạo đề cương</span>
                                    @endif
                                </td>

                                <td>


                                    {{-- Tạo đề cương khi chưa có version --}}
                                    @if (!$a->version_id)
                                        <form method="POST"
                                            action="{{ route('giangvien.outlines.createVersion', ['assignment' => $a->assignment_id]) }}">
                                            @csrf
                                            <button class="btn btn-primary btn-sm">
                                                Tạo đề cương
                                            </button>
                                        </form>
                                    @else
                                        {{-- Mở đề cương khi đã có version --}}
                                        <a href="{{ route('giangvien.outlines.edit', ['courseVersion' => $a->version_id]) }}"
                                            class="btn btn-outline-primary btn-sm">
                                            Mở đề cương
                                        </a>
                                    @endif

                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Bạn chưa được phân công soạn đề cương nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
