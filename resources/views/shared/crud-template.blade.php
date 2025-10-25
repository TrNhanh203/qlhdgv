@extends('layouts.app')
@section('content')
    @include('components.crud-style')

    <div class="container">
        <h1>{{ $title ?? 'Danh sách' }}</h1>

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
                            <td>{{ $item->{$col['field']} }}</td>
                        @endforeach
                        <td>
                            <button class="btn btn-secondary" onclick="openEdit({{ json_encode($item) }})">⚙️</button>
                        </td>
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

    @include('components.crud-script')

    <script>
        function openEdit(data) {
            document.getElementById('crudId').value = data.id;
            document.querySelectorAll('#crudForm [data-field]').forEach(i => i.value = data[i.dataset.field] ?? '');
            document.getElementById('crudModal').style.display = 'flex';
        }
    </script>
@endsection
