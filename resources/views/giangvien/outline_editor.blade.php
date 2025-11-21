@extends($layout ?? 'layouts.appGV')

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

        .panel label {
            font-size: 13px;
            margin-bottom: 4px
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

        .section-title {
            font-weight: bold;
            font-size: 14px;
        }

        .order_no {
            width: 42px;
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            background: #f9fafb;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 4px 0;
        }

        .header-block {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            font-family: "Times New Roman", serif;
            line-height: 1.35
        }

        .header-block .left,
        .header-block .right {
            width: 48%;
            text-align: center
        }

        .main-title {
            text-align: center;
            font-weight: bold;
            margin-top: 10px;
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

        /* Floating Action Button + Menu */
        .fab-container {
            position: fixed;
            bottom: 28px;
            right: 28px;
            z-index: 999;
        }

        .fab-main {
            width: 58px;
            height: 58px;
            background: #0d6efd;
            color: #fff;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 26px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transition: transform 0.25s ease;
        }

        .fab-main.open {
            transform: rotate(45deg);
        }

        .fab-menu {
            position: absolute;
            bottom: 70px;
            right: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
            opacity: 0;
            pointer-events: none;
            transform: translateY(20px);
            transition: opacity 0.25s, transform 0.25s;
        }

        .fab-menu.show {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .fab-item {
            background: #fff;
            color: #000;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 14px;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .fab-item:hover {
            background: #f1f1f1;
        }
    </style>
    <style>
        .clo-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }

        .clo-table th,
        .clo-table td {
            border: 1px solid #333;
            padding: 8px;
            vertical-align: top;
        }

        .clo-table th {
            background: #d8e4bc;
            text-align: center;
            font-weight: bold;
        }

        .clo-table td:first-child {
            width: 120px;
            text-align: center;
            font-weight: bold;
        }
    </style>


    {{-- Panel chọn mẫu + thông tin học phần --}}
    <div class="panel">
        <h5>Soạn đề cương chi tiết</h5>

        <div class="row mb-2">
            <div class="col-md-4">
                <label>Học phần</label>
                <div class="fw-bold">
                    {{ $courseVersion->course_code ?? '---' }} -
                    {{ $courseVersion->course_name ?? 'Chưa có tên học phần' }}
                </div>
            </div>
            <div class="col-md-2">
                <label>Phiên bản</label>
                <div>Version {{ $courseVersion->version_no ?? 1 }}</div>
            </div>
            <div class="col-md-6">
                <label>Mẫu đề cương</label>
                <select id="templateSelect" class="form-select form-select-sm">
                    <option value="">-- Chọn mẫu đề cương --</option>
                    @foreach ($templates as $tpl)
                        <option value="{{ $tpl->id }}" {{ $currentTemplateId == $tpl->id ? 'selected' : '' }}>
                            {{ $tpl->code }} - {{ $tpl->name }}
                            @if ($tpl->is_default)
                                (Mặc định)
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <small class="text-muted">
            * Giảng viên hãy chọn mẫu đề cương, sau đó điền nội dung cho từng mục. Cấu trúc đề cương do Trưởng khoa định
            nghĩa.
        </small>
    </div>

    <div class="page" id="editorPage">
        {{-- Khối quốc hiệu, tùy theo template --}}
        <div id="outlineHeader">
            @if ($templateMeta)
                <div class="header-block">
                    <div class="left">
                        <div class="fw-bold" id="gov_header_view">
                            {{ $templateMeta['gov_header'] }}</div>
                        <div class="fw-bold" id="university_name_view" style="font-size:15px">
                            {{ $templateMeta['university_name'] }}</div>
                    </div>
                    <div class="right">
                        <div class="fw-bold" id="national_header_view" style="font-size:15px">
                            {{ $templateMeta['national_header'] }}</div>
                        <div class="fst-italic" id="national_motto_view" style="font-size:15px">
                            {{ $templateMeta['national_motto'] }}</div>
                    </div>
                </div>

                <div class="main-title" id="main_title_view">
                    ĐỀ CƯƠNG CHI TIẾT HỌC PHẦN
                </div>
                <div class="major-title" id="major_name_view">
                    {{ $templateMeta['major_name'] ?? 'NGÀNH: ................' }}
                </div>
            @else
                <div class="text-center text-muted">
                    Vui lòng chọn mẫu đề cương để hiển thị quốc hiệu và cấu trúc nội dung.
                </div>
            @endif
        </div>

        {{-- Các mục nội dung --}}
        <div id="sectionContainer">
            {{-- Render bằng JS từ biến initialSections --}}
        </div>

        <div class="text-center mt-4">
            <button id="btnSaveOutline" class="btn btn-primary">💾 Lưu đề cương</button>
        </div>
    </div>

    <!-- Floating Action Button -->
    <div class="fab-container">
        <div class="fab-main" id="fabMain">
            <i class="bi bi-plus-lg"></i>
        </div>

        <div class="fab-menu" id="fabMenu">

            <!-- Tiện ích 1 -->
            {{-- <a class="fab-item"
                href="{{ route('giangvien.outlines.clo.index', ['courseVersion' => $courseVersion->id]) }}">
                <i class="bi bi-list-check text-primary"></i> Soạn CLO
            </a> --}}
            <a class="fab-item" href="javascript:void(0)" id="fabCloBuilder">
                <i class="bi bi-list-check text-primary"></i> Soạn CLO
            </a>


            <!-- Tiện ích 2 -->
            <a class="fab-item" href="#">
                <i class="bi bi-diagram-3 text-success"></i> Mapping CLO – PLO
            </a>

            <!-- Tiện ích 3 -->
            {{-- <a class="fab-item" href="#">
                <i class="bi bi-journal-text text-warning"></i> Kế hoạch giảng dạy
            </a> --}}

            <!-- Tiện ích 4 -->
            {{-- <a class="fab-item" href="{{ route('giangvien.outlines.clone.select', ['assignment' => $assignment->id]) }}"> --}}
            <a class="fab-item" href="#">

                <i class="bi bi-files text-warning"></i>
                Nhân bản nhanh
            </a>

        </div>
    </div>


    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

    <script>
        let hasUnsavedChanges = false;


        document.addEventListener('DOMContentLoaded', () => {
            const courseVersionId = {{ $courseVersion->id }};
            const initialTemplateId = @json($currentTemplateId);
            const initialTemplateMeta = @json($templateMeta);
            const initialSections = @json($sections);

            const templateSelect = document.getElementById('templateSelect');
            const container = document.getElementById('sectionContainer');
            const sectionEditors = new Map();
            let currentTemplateId = initialTemplateId || null;

            function clearEditors() {
                sectionEditors.forEach((editor, el) => {
                    if (editor && editor.destroy) {
                        editor.destroy().catch(() => {});
                    }
                });
                sectionEditors.clear();
            }

            function initSectionEditor(el) {
                if (!el || sectionEditors.has(el)) return;
                ClassicEditor
                    .create(el, {
                        toolbar: [
                            'heading', '|',
                            'bold', 'italic', 'underline', 'link',
                            '|', 'bulletedList', 'numberedList',
                            '|', 'insertTable', 'undo', 'redo'
                        ]
                    })
                    .then(editor => {
                        sectionEditors.set(el, editor);
                    })
                    .catch(err => console.error('CKEditor init error:', err));
            }

            function cleanHTML(html) {
                const tmp = document.createElement('div');
                tmp.innerHTML = html;
                const allowed = [
                    'P', 'B', 'I', 'U', 'UL', 'OL', 'LI', 'BR',
                    'STRONG', 'EM', 'A',
                    'TABLE', 'THEAD', 'TBODY', 'TR', 'TH', 'TD',
                    'H1', 'H2', 'H3', 'H4',
                    'FIGURE', 'CAPTION'
                ];
                tmp.querySelectorAll('*').forEach(el => {
                    if (!allowed.includes(el.tagName)) {
                        if (['SPAN', 'DIV'].includes(el.tagName)) {
                            el.replaceWith(...el.childNodes);
                        } else {
                            el.remove();
                        }
                    }
                    [...el.attributes].forEach(attr => {
                        if (['style', 'class', 'lang'].includes(attr.name) ||
                            attr.name.startsWith('mso') ||
                            /^on/i.test(attr.name)
                        ) {
                            el.removeAttribute(attr.name);
                        }
                    });
                });
                return tmp.innerHTML.trim();
            }

            function renderHeader(meta) {
                const header = document.getElementById('outlineHeader');
                if (!meta) {
                    header.innerHTML =
                        '<div class="text-center text-muted">Vui lòng chọn mẫu đề cương để hiển thị quốc hiệu và cấu trúc nội dung.</div>';
                    return;
                }
                header.innerHTML = `
                    <div class="header-block">
                        <div class="left">
                            <div class="fw-bold" id="gov_header_view">${meta.gov_header || ''}</div>
                            <div class="fw-bold" id="university_name_view" style="font-size:15px">
                                ${meta.university_name || ''}
                            </div>
                        </div>
                        <div class="right">
                            <div class="fw-bold" id="national_header_view" style="font-size:15px">
                                ${meta.national_header || ''}
                            </div>
                            <div class="fst-italic" id="national_motto_view" style="font-size:15px">
                                ${meta.national_motto || ''}
                            </div>
                        </div>
                    </div>
                    <div class="main-title" id="main_title_view">
                        ĐỀ CƯƠNG CHI TIẾT HỌC PHẦN
                    </div>
                    <div class="major-title" id="major_name_view">
                        ${meta.major_name || 'NGÀNH: ...............'}
                    </div>
                `;
            }

            function renderSections(list) {
                clearEditors();
                container.innerHTML = '';

                if (!list || !list.length) {
                    container.innerHTML =
                        '<div class="text-muted text-center mt-3">Chưa có cấu trúc mục cho mẫu này.</div>';
                    return;
                }

                list.forEach((s, idx) => {
                    const div = document.createElement('div');
                    div.className = 'section';
                    div.dataset.sectionTemplateId = s.section_template_id;

                    div.innerHTML = `
                        <div class="section-header mb-2">
                            <div class="order_no">${idx + 1}</div>
                            <div class="section-title">
                                ${s.code ? (s.code + ' - ') : ''}${s.title || 'Mục không tên'}
                            </div>
                        </div>
                        <div class="section-content mt-1 ck-section-editor">${
                            s.content_html || ''
                        }</div>
                    `;

                    container.appendChild(div);

                    const contentEl = div.querySelector('.ck-section-editor');
                    initSectionEditor(contentEl);
                });
            }

            function collectPayload() {
                const templateId = templateSelect.value;
                if (!templateId) {
                    throw new Error('Vui lòng chọn mẫu đề cương.');
                }

                const sections = [];
                container.querySelectorAll('.section').forEach((sec, i) => {
                    const sectionTemplateId = sec.dataset.sectionTemplateId;
                    if (!sectionTemplateId) {
                        throw new Error(`Section #${i + 1} thiếu section_template_id.`);
                    }

                    const contentEl = sec.querySelector('.section-content');
                    const editor = sectionEditors.get(contentEl);
                    const rawHtml = editor ? editor.getData() :
                        (contentEl?.innerHTML?.trim() || '');

                    sections.push({
                        section_template_id: sectionTemplateId,
                        content_html: cleanHTML(rawHtml),
                    });
                });

                if (!sections.length) {
                    throw new Error('Không có mục nội dung nào để lưu.');
                }

                return {
                    template_id: templateId,
                    sections,
                };
            }

            // Sự kiện chọn template
            templateSelect.addEventListener('change', async () => {
                const templateId = templateSelect.value;
                currentTemplateId = templateId || null;

                if (!templateId) {
                    renderHeader(null);
                    clearEditors();
                    container.innerHTML =
                        '<div class="text-muted text-center mt-3">Vui lòng chọn mẫu đề cương để bắt đầu soạn.</div>';
                    return;
                }

                try {
                    const url =
                        "{{ route('giangvien.outlines.loadTemplate', ['courseVersion' => $courseVersion->id]) }}" +
                        '?template_id=' + encodeURIComponent(templateId);

                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (!res.ok || !data.success) {
                        throw new Error(data.message || 'Không thể tải mẫu đề cương.');
                    }

                    renderHeader(data.template);
                    renderSections(data.sections);
                } catch (err) {
                    console.error(err);
                    alert('Lỗi khi tải mẫu đề cương: ' + err.message);
                }
            });

            // Nút Lưu đề cương
            document.getElementById('btnSaveOutline').addEventListener('click', async () => {
                try {
                    const payload = collectPayload();

                    const res = await fetch(
                        "{{ route('giangvien.outlines.save', ['courseVersion' => $courseVersion->id]) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                    const data = await res.json();
                    if (!res.ok || !data.success) {
                        throw new Error(data.message || 'Không thể lưu đề cương.');
                    }

                    alert('✅ ' + (data.message || 'Đã lưu đề cương thành công.'));
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                } catch (err) {
                    console.error(err);
                    alert('Lưu thất bại: ' + err.message);
                }
            });

            // Khởi tạo dữ liệu ban đầu (nếu đã có nội dung)
            if (initialTemplateId && initialSections && initialSections.length) {
                if (initialTemplateMeta) {
                    renderHeader(initialTemplateMeta);
                }
                renderSections(initialSections);
            } else {
                container.innerHTML =
                    '<div class="text-muted text-center mt-3">Vui lòng chọn mẫu đề cương để bắt đầu soạn.</div>';
            }
        });


        // KIỂM TRA CÓ TEMPLATE + CÓ SECTION
        function canUseUtilities() {
            const hasTemplate = {{ $currentTemplateId ? 'true' : 'false' }};
            const hasSections = {{ count($sections) > 0 ? 'true' : 'false' }};

            if (!hasTemplate) {
                alert("⚠️ Bạn chưa chọn mẫu đề cương.\nVui lòng chọn mẫu đề cương và nhấn 'Lưu' trước khi dùng tiện ích.");
                return false;
            }

            if (!hasSections) {
                alert("⚠️ Bạn chưa lưu đề cương.\nVui lòng nhấn 'Lưu đề cương' ít nhất 1 lần để tạo danh sách mục.");
                return false;
            }

            return true;
        }


        // Nút mở tiện ích CLO Builder
        document.getElementById("fabCloBuilder")?.addEventListener("click", function() {
            if (!canUseUtilities()) return;

            window.location.href =
                "{{ route('giangvien.outlines.clo.index', ['courseVersion' => $courseVersion->id]) }}";
        });
    </script>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fabMain = document.getElementById('fabMain');
            const fabMenu = document.getElementById('fabMenu');

            fabMain.addEventListener('click', function() {
                fabMain.classList.toggle('open');
                fabMenu.classList.toggle('show');
            });

            // Đóng menu khi click ra ngoài
            document.addEventListener('click', function(e) {
                if (!fabMain.contains(e.target) && !fabMenu.contains(e.target)) {
                    fabMain.classList.remove('open');
                    fabMenu.classList.remove('show');
                }
            });
        });
    </script>
@endsection
