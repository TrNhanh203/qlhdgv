@extends($layout ?? 'layouts.app')
@section('content')
    @include('components.crud-style')

    <div class="container">
        <h1>{{ $title ?? 'Danh sách' }}</h1>
        @if (!empty($customAddButton))
            <button class="btn btn-primary d-flex align-items-center gap-1" id="btnCustomAdd">
                @if (!empty($customAddButton['icon']))
                    <i class="{{ $customAddButton['icon'] }}"></i>
                @endif
                <span>{{ $customAddButton['label'] }}</span>
            </button>
        @endif
        <button class="btn btn-primary" onclick="document.getElementById('crudModal').style.display='flex'">+ Thêm
            mới</button>
        <button id="deleteBtn" class="btn btn-disabled" disabled>Xóa</button>

        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll" onclick="CRUD.toggleAll(this,'.row-check')"></th>
                    @foreach ($columns as $col)
                        <th>{{ $col['label'] }}</th>
                    @endforeach
                    <th>Hiệu chỉnh</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td><input type="checkbox" class="row-check" value="{{ $item->id }}"></td>

                        @foreach ($columns as $col)
                            @if (($col['type'] ?? '') === 'actions')
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i> Thao tác
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow">
                                            @foreach ($col['menu_items'] ?? [] as $menu)
                                                @php
                                                    $url = route($menu['route'], $item->{$menu['param']});
                                                @endphp
                                                <li>
                                                    <a class="dropdown-item d-flex flex-column align-items-start"
                                                        href="{{ $url }}">
                                                        <div>
                                                            @if (!empty($menu['icon']))
                                                                <i class="{{ $menu['icon'] }} me-2"></i>
                                                            @endif
                                                            <strong>{{ $menu['text'] }}</strong>
                                                        </div>
                                                        @if (!empty($menu['desc']))
                                                            <small class="text-muted ms-4">{{ $menu['desc'] }}</small>
                                                        @endif
                                                    </a>
                                                </li>
                                                @if (!$loop->last)
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </td>
                            @else
                                <td>
                                    {{-- @if (!empty($col['link_to_child']))
                                        <a href="{{ route('truongkhoa.phienban.index', ['program_id' => $item->id]) }}"
                                            class="text-primary">
                                            {{ $item->{$col['field']} }}
                                        </a>
                                    @else
                                        {{ $item->{$col['field']} }}
                                    @endif --}}
                                    {{ $item->{$col['field']} }}

                                </td>
                            @endif
                        @endforeach

                        {{-- ⚙️ nút chỉnh sửa cũ có thể bỏ hoặc giữ tuỳ ý --}}
                        <td><button class="btn btn-secondary" onclick="openEdit({{ json_encode($item) }})">⚙️</button></td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>

    <!-- Modal -->
    <div id="crudModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">{{ $modalTitle ?? 'Thêm mới' }}</h2>
            <form id="crudForm">
                <input type="hidden" id="crudId">
                <input type="hidden" id="parentId" name="parent_id" value="{{ $parent_id ?? '' }}">
                <div class="form-grid">
                    @foreach ($fields as $f)
                        <div class="form-group">
                            <label>{{ $f['label'] }} @if ($f['required'] ?? false)
                                    <span class="required">*</span>
                                @endif
                            </label>
                            @if (($f['type'] ?? 'text') === 'select')
                                <select data-field="{{ $f['name'] }}">
                                    @foreach ($f['options'] ?? [] as $val => $text)
                                        <option value="{{ $val }}">{{ $text }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="{{ $f['type'] ?? 'text' }}" data-field="{{ $f['name'] }}">
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Lưu</button>
                    <button type="button" class="btn btn-secondary"
                        onclick="document.getElementById('crudModal').style.display='none'">Hủy</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirm Redirect Modal -->





    @include('components.crud-script')

    <script>
        function openEdit(data) {
            document.getElementById('crudId').value = data.id;
            document.querySelectorAll('#crudForm [data-field]').forEach(i => i.value = data[i.dataset.field] ?? '');
            document.getElementById('crudModal').style.display = 'flex';
        }
    </script>
    @if (!empty($customAddButton))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const oldBtn = document.querySelector('button[onclick*="crudModal"]');
                if (oldBtn) oldBtn.style.display = 'none';

                const btn = document.getElementById('btnCustomAdd');
                if (!btn) return;

                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Xác nhận',
                        text: @json($customAddButton['confirm'] ?? 'Bạn có chắc muốn thực hiện thao tác này?'),
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Đồng ý',
                        cancelButtonText: 'Hủy',
                        reverseButtons: true,
                        confirmButtonColor: '#0d6efd'
                    }).then(result => {
                        if (result.isConfirmed) {
                            window.location.href = @json($customAddButton['route']);
                        }
                    });
                });
            });
        </script>
    @endif


@endsection
