<style>
    /* Khung A4 thu nhỏ giống trang editor */
    .outline-preview-page {
        background: #f3f4f3;
        padding: 16px 0;
        font-family: "Times New Roman", serif;
    }

    .outline-preview-inner {
        background: #fff;
        width: 210mm;
        max-width: 100%;
        margin: 0 auto;
        padding: 20mm;
        box-shadow: 0 0 5px rgba(0, 0, 0, .2);
        font-size: 14pt;
        line-height: 1.35;
    }

    .outline-preview-meta {
        font-size: 11pt;
        margin-bottom: 10px;
    }

    .outline-preview-meta .row {
        margin-bottom: 4px;
    }

    .outline-preview-section {
        margin-top: 14px;
    }

    .outline-preview-section-title {
        font-weight: bold;
        margin-bottom: 6px;
    }

    /* Vùng hiển thị nội dung giống CKEditor: ck-content */
    .outline-preview-content.ck-content {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 8px 10px;
        min-height: 40px;
        /* Cho phép xem full nội dung, không cắt bớt */
        overflow: visible;
    }

    /* Một số style cơ bản để giống CKEditor hơn */
    .outline-preview-content.ck-content p {
        margin: 0 0 4px 0;
    }

    .outline-preview-content.ck-content ul,
    .outline-preview-content.ck-content ol {
        padding-left: 24px;
        margin: 0 0 4px 0;
    }

    .outline-preview-content.ck-content table {
        border-collapse: collapse;
        width: 100%;
        margin: 4px 0;
    }

    .outline-preview-content.ck-content table,
    .outline-preview-content.ck-content th,
    .outline-preview-content.ck-content td {
        border: 1px solid #000;
    }

    .outline-preview-content.ck-content th,
    .outline-preview-content.ck-content td {
        padding: 4px 6px;
    }
</style>

<div class="outline-preview-page">
    <div class="outline-preview-inner">

        {{-- Meta đề cương nguồn --}}
        <div class="outline-preview-meta">
            <div><strong>Học phần:</strong> {{ $courseVersion->course_code }} - {{ $courseVersion->course_name }}</div>
            <div><strong>CTĐT:</strong> {{ $courseVersion->program_code }} - {{ $courseVersion->program_name }}</div>
            <div><strong>Phiên bản CTĐT:</strong> {{ $courseVersion->program_version_code ?? '---' }}</div>
            <div>
                <strong>Năm học / Học kỳ:</strong>
                {{ $courseVersion->academic_year_code ?? '---' }} / {{ $courseVersion->semester_name ?? '---' }}
            </div>
            <div>
                <strong>Version nguồn:</strong> V{{ $courseVersion->version_no }} (ID: {{ $courseVersion->id }})
                – Trạng thái: {{ $courseVersion->status ?? 'draft' }}
            </div>
        </div>

        {{-- Các mục nội dung --}}
        @forelse ($sections as $sec)
            <div class="outline-preview-section">
                <div class="outline-preview-section-title">
                    @if ($sec->code)
                        <span>{{ $sec->code }}.</span>
                    @endif
                    <span>{{ $sec->title }}</span>
                </div>

                {{-- Vùng này đóng vai trò "giống CKEditor nhưng read-only" --}}
                <div class="outline-preview-content ck-content">
                    {!! $sec->content_html !!}
                </div>
            </div>
        @empty
            <div class="mt-3 text-muted">
                Đề cương này chưa có nội dung nào.
            </div>
        @endforelse

    </div>
</div>
