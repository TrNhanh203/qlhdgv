@extends($layout ?? 'layouts.apptruongkhoa')

@section('content')
    <style>
        body {
            background: #f3f4f3
        }

        .page {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 20px auto;
            box-shadow: 0 0 5px rgba(0, 0, 0, .2);
            font-family: 'Times New Roman', serif;
            color: #000;
            font-size: 14pt
        }

        .panel {
            width: 210mm;
            margin: 0 auto 16px auto;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 14px 16px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
            font-family: system-ui, Segoe UI, Roboto
        }

        .panel h5 {
            margin: 0 0 10px 0;
            font-weight: 700;
            font-size: 16px
        }

        .panel .row+.row {
            margin-top: 8px
        }

        .panel label {
            font-size: 13px;
            margin-bottom: 4px
        }

        .panel input[type="text"],
        .panel textarea {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px 10px;
            font-size: 14px
        }

        .panel textarea {
            min-height: 60px
        }

        .flex {
            display: flex;
            gap: 10px
        }

        .grow {
            flex: 1
        }

        .rightActions {
            display: flex;
            gap: 8px;
            align-items: center
        }

        .section {
            margin-top: 18px;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 10px
        }

        .section-header {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap
        }

        .section-header input[type="text"] {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 13px
        }

        .section-header .order {
            width: 64px;
            text-align: center
        }

        .section-title {
            font-weight: bold
        }

        [contenteditable="true"]:focus {
            outline: 2px solid #4c8bf5;
            border-radius: 4px
        }

        .btn-ghost {
            border: 1px solid #d1d5db;
            background: #fff;
            padding: 4px 8px;
            border-radius: 6px
        }

        .btn-ghost:hover {
            background: #f3f4f6
        }

        .btn-danger-lite {
            border: 1px solid #fecaca;
            background: #fff0f0
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 35px;
            font-family: "Times New Roman", serif;
            line-height: 1.35
        }

        .header .left,
        .header .right {
            width: 48%;
            text-align: center
        }

        .header .line {
            margin: 0;
            padding: 0
        }

        .header .bold {
            font-weight: bold
        }

        .header .italic {
            font-style: italic
        }

        .header .right #national_header {
            white-space: nowrap
        }

        /* giữ 1 dòng */
        .main-title {
            text-align: center;
            font-weight: bold;
            margin-top: 35px;
            font-size: 16pt;
            text-transform: uppercase
        }

        .major-title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            text-transform: uppercase;
            margin-top: 6px;
            position: relative;
            padding-bottom: 6px
        }

        .major-title::after {
            content: "";
            position: absolute;
            left: 20%;
            right: 20%;
            bottom: 0;
            border-bottom: 1px solid #000
        }
    </style>

    {{-- 🔧 Panel nhập meta Template (code / name / description / is_default) --}}
    <div class="panel" id="metaPanel">
        <h5>Thông tin mẫu đề cương</h5>


        <div class="row flex">
            <div class="grow">
                <label>Mã mẫu (code)</label>
                <input type="text" id="tpl_code" placeholder="VD: CNTT-DC-2025" value="{{ $template->code ?? '' }}">
            </div>
            <div class="grow">
                <label>Tên mẫu (name)</label>
                <input type="text" id="tpl_name" placeholder="Đề cương chi tiết - Chuẩn TDMU"
                    value="{{ $template->name ?? '' }}">
            </div>
            <div style="display:flex;align-items:flex-end;gap:8px">
                <div>
                    <label>&nbsp;</label><br>
                    <input type="checkbox" id="tpl_is_default" {{ !empty($template->is_default) ? 'checked' : '' }}>
                    <span>Đặt làm mẫu mặc định</span>
                </div>
                <div class="rightActions">
                    <button class="btn btn-outline-primary" id="openSaveModal"> Lưu mẫu đề cương</button>
                </div>
            </div>
        </div>
        <div class="row">
            <label>Mô tả (description)</label>
            <textarea id="tpl_description" placeholder="Ghi chú, phạm vi áp dụng, hướng dẫn chung…"></textarea>
        </div>
    </div>

    <div class="page" id="editorPage">

        {{-- 🏛️ Khối quốc hiệu --}}


        <div class="header">
            <div class="left">
                <div class="line bold" id="gov_header" contenteditable="true">
                    {{ $template->gov_header ?? 'UBND TP. HỒ CHÍ MINH' }}</div>
                <div class="line bold" contenteditable="true" id="university_name" style="font-size:15px">
                    {{ $template->university_name ?? 'TRƯỜNG ĐẠI HỌC THỦ DẦU MỘT' }}</div>
            </div>
            <div class="right">
                <div class="line bold" contenteditable="true" id="national_header" style="font-size:15px">
                    {{ $template->national_header ?? 'CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM' }}</div>
                <div class="line italic" contenteditable="true" id="national_motto" style="font-size:15px">
                    {{ $template->national_motto ?? 'Độc lập - Tự do - Hạnh phúc' }}</div>
            </div>
        </div>

        <div class="main-title" contenteditable="true" id="main_title">
            {{ $template->main_title ?? 'ĐỀ CƯƠNG CHI TIẾT HỌC PHẦN' }}</div>
        <div class="major-title" contenteditable="true" id="major_name">
            {{ $template->major_name ?? 'NGÀNH: KỸ THUẬT PHẦN MỀM' }}</div>

        {{-- 📋 Các mục (section) --}}
        <div id="sectionContainer">
            @isset($sections)
                @forelse($sections as $i => $s)
                    <div class="section" data-id="{{ $s->id ?? $i + 1 }}">
                        <div class="section-header">
                            <div class="order_no" title="Thứ tự mục"
                                style="width:42px;text-align:center;font-weight:bold;font-size:13px;
                                background:#f9fafb;border:1px solid #d1d5db;border-radius:6px;padding:4px 0;">
                                {{ $s->order_no ?? $i + 1 }}
                            </div>

                            <input type="text" class="sec-code"
                                placeholder="Mã mục (code) – VD: S{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}"
                                value="{{ $s->code }}">

                            <input type="text" class="sec-title" placeholder="Tiêu đề (title)" value="{{ $s->title }}">

                            <div class="rightActions">
                                <button class="btn-ghost btnMoveUp">↑</button>
                                <button class="btn-ghost btnMoveDown">↓</button>
                                <button class="btn-ghost btnClone">⧉</button>
                                <button class="btn-ghost btn-danger-lite btnRemove">🗑</button>
                            </div>
                        </div>

                        {{-- Giữ nguyên HTML đã lưu (không escape) --}}
                        <div class="section-content mt-1" contenteditable="true">{!! $s->default_content !!}</div>
                    </div>
                @empty
                    {{-- Không có section nào trong DB, fallback về 1 section mặc định --}}
                    <div class="section" data-id="1">
                        <div class="section-header">
                            <div class="order_no" title="Thứ tự mục"
                                style="width:42px;text-align:center;font-weight:bold;font-size:13px;
                                background:#f9fafb;border:1px solid #d1d5db;border-radius:6px;padding:4px 0;">
                                1
                            </div>
                            <input type="text" class="sec-code" placeholder="Mã mục (code) – VD: S01" value="S01">
                            <input type="text" class="sec-title" placeholder="Tiêu đề (title)"
                                value="1. Thông tin tổng quát">
                            <div class="rightActions">
                                <button class="btn-ghost btnMoveUp">↑</button>
                                <button class="btn-ghost btnMoveDown">↓</button>
                                <button class="btn-ghost btnClone">⧉</button>
                                <button class="btn-ghost btn-danger-lite btnRemove">🗑</button>
                            </div>
                        </div>
                        <div class="section-content mt-1" contenteditable="true">
                            default content...
                        </div>
                    </div>
                @endforelse
            @else
                {{-- Chế độ tạo mới: 1 section mặc định --}}
                <div class="section" data-id="1">
                    <div class="section-header">
                        <div class="order_no" title="Thứ tự mục"
                            style="width:42px;text-align:center;font-weight:bold;font-size:13px;
                            background:#f9fafb;border:1px solid #d1d5db;border-radius:6px;padding:4px 0;">
                            1
                        </div>
                        <input type="text" class="sec-code" placeholder="Mã mục (code) – VD: S01" value="S01">
                        <input type="text" class="sec-title" placeholder="Tiêu đề (title)"
                            value="1. Thông tin tổng quát">
                        <div class="rightActions">
                            <button class="btn-ghost btnMoveUp">↑</button>
                            <button class="btn-ghost btnMoveDown">↓</button>
                            <button class="btn-ghost btnClone">⧉</button>
                            <button class="btn-ghost btn-danger-lite btnRemove">🗑</button>
                        </div>
                    </div>
                    <div class="section-content mt-1" contenteditable="true">
                        default content...
                    </div>
                </div>
            @endisset
        </div>


        <div class="text-center mt-4">
            <button id="addSection" class="btn btn-outline-success">➕ Thêm mục mới</button>
            <button id="saveDraft" class="btn btn-primary ms-2">💾 Lưu nháp</button>
        </div>
    </div>

    {{-- Modal lưu thành mẫu --}}
    <div class="modal fade" id="saveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lưu thành mẫu đề cương</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Mã mẫu (code)</label>
                        <input class="form-control" id="m_code" placeholder="CNTT-DC-2025">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Tên mẫu (name)</label>
                        <input class="form-control" id="m_name" placeholder="Đề cương chi tiết - Chuẩn TDMU">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Mô tả (description)</label>
                        <textarea class="form-control" id="m_description" rows="3"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="m_is_default">
                        <label class="form-check-label" for="m_is_default">Đặt làm mẫu mặc định</label>
                    </div>
                    <small class="text-muted d-block mt-2">* Các trường quốc hiệu/tiêu đề/ ngành được lấy từ trang soạn
                        thảo ở trên.</small>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button class="btn btn-primary" id="confirmSave">Lưu</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('sectionContainer');
            let isDirty = false;

            // Đánh dấu đã chỉnh sửa
            container.addEventListener('input', () => isDirty = true);
            document.querySelectorAll('#metaPanel input, #metaPanel textarea').forEach(el => {
                el.addEventListener('input', () => isDirty = true);
            });

            // ⚠️ Cảnh báo trước khi thoát trang
            window.addEventListener('beforeunload', function(e) {
                if (!isDirty) return;
                e.preventDefault();
                e.returnValue = 'Bạn có thay đổi chưa lưu, rời trang sẽ mất dữ liệu!';
            });

            // ====== Các hàm hỗ trợ ======
            function resequence() {
                [...container.querySelectorAll('.section')].forEach((sec, idx) => {
                    const orderEl = sec.querySelector('.order_no');
                    if (orderEl) orderEl.textContent = idx + 1;
                    const titleInput = sec.querySelector('.sec-title');
                    if (titleInput && /^\d+\.\s/.test(titleInput.value)) {
                        titleInput.value = `${idx + 1}. ` + titleInput.value.replace(/^\d+\.\s/, '');
                    }
                });
            }

            function cleanHTML(html) {
                const tmp = document.createElement('div');
                tmp.innerHTML = html;
                const allowed = ['P', 'B', 'I', 'U', 'UL', 'OL', 'LI', 'BR', 'STRONG', 'EM', 'TABLE', 'THEAD',
                    'TBODY', 'TR', 'TH', 'TD'
                ];
                tmp.querySelectorAll('*').forEach(el => {
                    if (!allowed.includes(el.tagName)) {
                        if (['SPAN', 'DIV'].includes(el.tagName)) el.replaceWith(...el.childNodes);
                        else el.remove();
                    }
                    [...el.attributes].forEach(attr => {
                        if (['style', 'class', 'lang'].includes(attr.name) || attr.name.startsWith(
                                'mso') || /^on/i.test(attr.name)) {
                            el.removeAttribute(attr.name);
                        }
                    });
                });
                return tmp.innerHTML.trim();
            }

            // Gom dữ liệu
            function collectPayload() {
                const sections = [];
                container.querySelectorAll('.section').forEach((s, i) => {
                    const code = s.querySelector('.sec-code')?.value?.trim();
                    const title = s.querySelector('.sec-title')?.value?.trim();
                    if (!code || !title) throw new Error(`Section #${i+1} thiếu mã hoặc tiêu đề.`);
                    sections.push({
                        code,
                        title,
                        order_no: parseInt(s.querySelector('.order_no')?.textContent || (i + 1),
                            10),
                        default_content: cleanHTML(s.querySelector('.section-content')?.innerHTML
                            ?.trim() || '')
                    });
                });

                const code = document.getElementById('m_code').value.trim() || document.getElementById('tpl_code')
                    .value.trim();
                const name = document.getElementById('m_name').value.trim() || document.getElementById('tpl_name')
                    .value.trim();
                if (!code || !name) throw new Error('Vui lòng nhập đầy đủ Mã mẫu và Tên mẫu.');

                return {
                    template_meta: {
                        code,
                        name,
                        description: document.getElementById('m_description').value.trim() || document
                            .getElementById('tpl_description').value.trim(),
                        is_default: document.getElementById('m_is_default').checked || document.getElementById(
                            'tpl_is_default').checked ? 1 : 0,
                        gov_header: document.getElementById('gov_header').innerText.trim(),
                        university_name: document.getElementById('university_name').innerText.trim(),
                        national_header: document.getElementById('national_header').innerText.trim(),
                        national_motto: document.getElementById('national_motto').innerText.trim(),
                        main_title: document.getElementById('main_title').innerText.trim(),
                        major_name: document.getElementById('major_name').innerText.trim(),
                    },
                    sections
                };
            }

            // ====== Sự kiện ======
            document.getElementById('saveDraft').addEventListener('click', () => {
                console.log("📄 Dữ liệu nháp:", collectPayload());
                alert("Đang ở dạng nháp (chưa lưu DB). Mở console để xem JSON đầy đủ.");
            });

            const bsModal = new bootstrap.Modal(document.getElementById('saveModal'));
            document.getElementById('openSaveModal').addEventListener('click', () => {
                document.getElementById('m_code').value = document.getElementById('tpl_code').value;
                document.getElementById('m_name').value = document.getElementById('tpl_name').value;
                document.getElementById('m_description').value = document.getElementById('tpl_description')
                    .value;
                document.getElementById('m_is_default').checked = document.getElementById('tpl_is_default')
                    .checked;
                bsModal.show();
            });

            // ====== Lưu thật ======
            document.getElementById('confirmSave').addEventListener('click', async () => {
                try {
                    const payload = collectPayload();
                    console.log("🚀 Gửi lưu thật:", payload);

                    const res = await fetch("{{ route('truongkhoa.outline-template.store') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                                .content
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await res.json();
                    if (!res.ok || !data.success) throw new Error(data.message || 'Không thể lưu mẫu.');

                    isDirty = false;
                    bsModal.hide();
                    alert("✅ Lưu thành công! Mã mẫu: " + payload.template_meta.code);
                } catch (err) {
                    console.error("❌ Lỗi khi lưu:", err);
                    alert("Lưu thất bại: " + err.message);
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('sectionContainer');




            // Thêm section mới
            document.getElementById('addSection').addEventListener('click', () => {
                const idx = container.children.length + 1;
                const div = document.createElement('div');
                div.className = 'section';
                div.innerHTML = `
      <div class="section-header">
        <div class="order_no" style="width:42px;text-align:center;font-weight:bold;font-size:13px;
         background:#f9fafb;border:1px solid #d1d5db;border-radius:6px;padding:4px 0;">
         ${idx}
    </div>
        <input type="text" class="sec-code" placeholder="Mã mục (code) – VD: S${String(idx).padStart(2,'0')}" value="S${String(idx).padStart(2,'0')}">
        <input type="text" class="sec-title" placeholder="Tiêu đề (title)" value="${idx}. Tiêu đề mới">
        
       
        <div class="rightActions">
          <button class="btn-ghost btnMoveUp">↑</button>
          <button class="btn-ghost btnMoveDown">↓</button>
          <button class="btn-ghost btnClone">⧉</button>
          <button class="btn-ghost btn-danger-lite btnRemove">🗑</button>
        </div>
      </div>
      <div contenteditable="true" class="section-content mt-1">default content...</div>
    `;
                container.appendChild(div);
                resequence();
            });

            // Ủy quyền click cho các nút hành động section
            container.addEventListener('click', (e) => {
                const btn = e.target.closest('button');
                if (!btn) return;
                const sec = e.target.closest('.section');
                if (!sec) return;

                if (btn.classList.contains('btnRemove')) {
                    sec.remove();
                    resequence();
                }
                if (btn.classList.contains('btnMoveUp')) {
                    const prev = sec.previousElementSibling;
                    if (prev) {
                        container.insertBefore(sec, prev);
                        resequence();
                    }
                }
                if (btn.classList.contains('btnMoveDown')) {
                    const next = sec.nextElementSibling;
                    if (next) {
                        container.insertBefore(next, sec);
                        resequence();
                    }
                }
                if (btn.classList.contains('btnClone')) {
                    const clone = sec.cloneNode(true);
                    container.insertBefore(clone, sec.nextElementSibling);
                    resequence();
                }
            });













            // document.getElementById('confirmSave').addEventListener('click', () => {
            //     // lấy meta từ modal (ưu tiên modal nếu có)
            //     const payload = collectPayload();
            //     payload.template_meta.code = document.getElementById('m_code').value.trim() || payload
            //         .template_meta.code;
            //     payload.template_meta.name = document.getElementById('m_name').value.trim() || payload
            //         .template_meta.name;
            //     payload.template_meta.description = document.getElementById('m_description').value.trim() ||
            //         payload.template_meta.description;
            //     payload.template_meta.is_default = document.getElementById('m_is_default').checked ? 1 :
            //         payload.template_meta.is_default;

            //     console.log("✅ Payload sẵn sàng để POST lên /outline-templates/store:", payload);
            //     alert("Đã gom dữ liệu mẫu + các section (xem console).");
            //     bsModal.hide();
            // });
        });
    </script>
@endsection
