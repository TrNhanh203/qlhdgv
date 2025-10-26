@extends($layout ?? 'layouts.apptruongkhoa')

@section('content')
    <div class="container py-4">
        {{-- ====== TIÊU ĐỀ ====== --}}
        <div class="mb-3 border-bottom pb-2">
            <h4 class="mb-1">Chuẩn đầu ra chương trình (PLO & PI)</h4>
            <p class="mb-0 text-muted">
                📘 Chương trình đào tạo: <strong>{{ $program->program_name ?? 'Không xác định' }}</strong><br>
                🔖 Phiên bản: <strong>{{ $version->version_code ?? 'Không rõ' }}</strong>
                (Hiệu lực: {{ $version->effective_from ?? 'N/A' }} → {{ $version->effective_to ?? 'N/A' }})
            </p>
        </div>

        {{-- ====== DANH SÁCH PLO ====== --}}
        <div id="ploContainer"></div>
        <button class="btn btn-primary mt-3" id="addPloBtn">+ Thêm PLO</button>
    </div>

    {{-- ===== MODAL FORM PLO ===== --}}
    <div class="modal fade" id="ploModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm/Sửa PLO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="ploId">
                    <div class="mb-3">
                        <label class="form-label">Mã PLO</label>
                        <input type="text" class="form-control" id="ploCode">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" id="ploDesc" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button class="btn btn-primary" id="savePloBtn">Lưu</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== MODAL FORM PI ===== --}}
    <div class="modal fade" id="piModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm/Sửa PI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="piId">
                    <input type="hidden" id="piPloId">
                    <div class="mb-3">
                        <label class="form-label">Mã PI</label>
                        <input type="text" class="form-control" id="piCode">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" id="piDesc" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button class="btn btn-primary" id="savePiBtn">Lưu</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TOAST ===== --}}
    <div class="position-fixed top-0 end-0 p-3" style="z-index:1055">
        <div id="crudToast" class="toast align-items-center text-bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body" id="toastMsg">Thao tác thành công!</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <script>
        // ===== AJAX =====
        async function postJson(url, data) {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify(data)
            });
            return res.json();
        }

        document.addEventListener('DOMContentLoaded', () => {
            const versionId = {{ $version_id }};
            let plos = @json($plos);

            const container = document.getElementById('ploContainer');
            const ploModal = new bootstrap.Modal('#ploModal');
            const piModal = new bootstrap.Modal('#piModal');
            const toastEl = new bootstrap.Toast(document.getElementById('crudToast'));
            const showToast = (msg, ok = true) => {
                const el = document.getElementById('crudToast');
                el.classList.remove('text-bg-success', 'text-bg-danger');
                el.classList.add(ok ? 'text-bg-success' : 'text-bg-danger');
                document.getElementById('toastMsg').innerText = msg;
                toastEl.show();
            };



            // ===== RENDER =====
            function render() {
                container.innerHTML = '';
                plos.forEach((plo, i) => {
                    const card = document.createElement('div');
                    card.className = 'card mb-3 shadow-sm';
                    card.innerHTML = `
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div><strong>${plo.code}</strong> – ${plo.description}</div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="editPLO(${i})"><i class="bi bi-pencil-square me-2"></i>Sửa</a></li>
                            <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deletePLO(${plo.id})"><i class="bi bi-trash me-2"></i>Xóa</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <h6>Danh sách PI:</h6>
                    <ul class="list-group mb-2">
                        ${(plo.pis||[]).map((pi, j) => `
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div><strong>${pi.code}</strong>: ${pi.description}</div>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="editPI(${i}, ${j})"><i class="bi bi-pencil-square me-2"></i>Sửa</a></li>
                                                        <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deletePI(${pi.id})"><i class="bi bi-trash me-2"></i>Xóa</a></li>
                                                    </ul>
                                                </div>
                                            </li>`).join('')}
                    </ul>
                    <button class="btn btn-sm btn-outline-primary" onclick="openPiModal(${plo.id})">+ Thêm PI</button>
                </div>`;
                    container.appendChild(card);
                });
            }

            // ===== CRUD PLO =====
            window.openPloModal = () => {
                document.getElementById('ploId').value = '';
                document.getElementById('ploCode').value = '';
                document.getElementById('ploDesc').value = '';
                ploModal.show();
            };
            window.editPLO = (i) => {
                const p = plos[i];
                document.getElementById('ploId').value = p.id;
                document.getElementById('ploCode').value = p.code;
                document.getElementById('ploDesc').value = p.description;
                ploModal.show();
            };
            document.getElementById('savePloBtn').addEventListener('click', async () => {
                const id = document.getElementById('ploId').value;
                const code = document.getElementById('ploCode').value.trim();
                const description = document.getElementById('ploDesc').value.trim();
                if (!code || !description) return showToast('Vui lòng nhập đủ thông tin', false);
                await postJson(`/truongkhoa/chuongtrinhdaotao/phienban/${versionId}/plo/store-plo`, {
                    id,
                    code,
                    description
                });
                showToast('✅ Lưu PLO thành công');
                location.reload();
            });
            window.deletePLO = async (id) => {
                if (!confirm('Xóa PLO này?')) return;
                await postJson(`/truongkhoa/chuongtrinhdaotao/phienban/${versionId}/plo/delete-plo`, {
                    id
                });
                showToast('🗑️ Xóa PLO thành công');
                location.reload();
            };

            // ===== CRUD PI =====
            window.openPiModal = (ploId) => {
                document.getElementById('piId').value = '';
                document.getElementById('piPloId').value = ploId;
                document.getElementById('piCode').value = '';
                document.getElementById('piDesc').value = '';
                piModal.show();
            };
            window.editPI = (i, j) => {
                const pi = plos[i].pis[j];
                document.getElementById('piId').value = pi.id;
                document.getElementById('piPloId').value = plos[i].id;
                document.getElementById('piCode').value = pi.code;
                document.getElementById('piDesc').value = pi.description;
                piModal.show();
            };
            document.getElementById('savePiBtn').addEventListener('click', async () => {
                const id = document.getElementById('piId').value;
                const plo_id = document.getElementById('piPloId').value;
                const code = document.getElementById('piCode').value.trim();
                const description = document.getElementById('piDesc').value.trim();
                if (!code || !description) return showToast('Vui lòng nhập đủ thông tin', false);
                await postJson(`/truongkhoa/chuongtrinhdaotao/phienban/${versionId}/plo/store-pi`, {
                    id,
                    plo_id,
                    code,
                    description
                });
                showToast('✅ Lưu PI thành công');
                location.reload();
            });
            window.deletePI = async (id) => {
                if (!confirm('Xóa PI này?')) return;
                await postJson(`/truongkhoa/chuongtrinhdaotao/phienban/${versionId}/plo/delete-pi`, {
                    id
                });
                showToast('🗑️ Xóa PI thành công');
                location.reload();
            };

            document.getElementById('addPloBtn').addEventListener('click', openPloModal);
            render();
        });
    </script>
@endsection
