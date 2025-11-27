<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GiangVienCloMappingController extends Controller
{
    public function index($courseVersionId)
    {
        // Thông tin version + CTĐT + năm học/học kỳ
        $courseVersion = DB::table('outline_course_versions as ocv')
            ->join('outline_program_courses as opc', 'ocv.program_course_id', '=', 'opc.id')
            ->join('courses as c', 'opc.course_id', '=', 'c.id')
            ->join('outline_program_versions as opv', 'opc.program_version_id', '=', 'opv.id')
            ->join('education_programs as ep', 'opv.education_program_id', '=', 'ep.id')
            ->leftJoin('semesters as s', 'opc.semester_id', '=', 's.id')
            ->leftJoin('academic_years as ay', 'opc.academic_year_id', '=', 'ay.id')
            ->where('ocv.id', $courseVersionId)
            ->select(
                'ocv.id',
                'ocv.version_no',
                'ocv.status',
                'opc.program_version_id',

                'c.course_code',
                'c.course_name',

                'ep.program_code',
                'ep.program_name',
                'opv.version_code as program_version_code',

                'ay.year_code as academic_year_code',
                's.semester_name'
            )
            ->first();

        if (!$courseVersion) {
            abort(404, 'Không tìm thấy phiên bản đề cương.');
        }

        // CLO của version này
        $clos = DB::table('outline_clos')
            ->where('course_version_id', $courseVersionId)
            ->orderBy('code')
            ->get();

        if ($clos->isEmpty()) {
            return redirect()
                ->route('giangvien.outlines.clo.index', ['courseVersion' => $courseVersionId])
                ->with('error', 'Vui lòng soạn CLO trước khi thực hiện mapping.');
        }

        // PLO của CTĐT tương ứng
        $plos = DB::table('outline_plos')
            ->where('program_version_id', $courseVersion->program_version_id)
            ->orderBy('code')
            ->get();

        if ($plos->isEmpty()) {
            return redirect()
                ->route('giangvien.outlines.edit', ['courseVersion' => $courseVersionId])
                ->with('error', 'CTĐT này chưa được khai báo chuẩn đầu ra chương trình (PLO). Vui lòng bổ sung PLO trước khi thực hiện mapping.');
        }

        // PI (chỉ số) thuộc các PLO đó
        $pis = DB::table('outline_pis as pi')
            ->join('outline_plos as plo', 'pi.plo_id', '=', 'plo.id')
            ->where('plo.program_version_id', $courseVersion->program_version_id)
            ->orderBy('plo.code')
            ->orderBy('pi.code')
            ->select(
                'pi.id',
                'pi.code',
                'pi.description',
                'plo.id as plo_id',
                'plo.code as plo_code'
            )
            ->get();

        if ($pis->isEmpty()) {
            return redirect()
                ->route('giangvien.outlines.edit', ['courseVersion' => $courseVersionId])
                ->with('error', 'CTĐT này chưa được khai báo các chỉ báo PI cho PLO. Vui lòng bổ sung PI trước khi thực hiện mapping.');
        }

        $cloIds = $clos->pluck('id')->all();

        // Mapping CLO–PLO
        $cloPloMaps = DB::table('outline_clo_plo_maps')
            ->whereIn('clo_id', $cloIds)
            ->get();

        $cloPloMatrix = [];
        foreach ($cloPloMaps as $m) {
            $cloPloMatrix[$m->clo_id][$m->plo_id] = $m->level; // I/R/M/A
        }

        // Mapping CLO–PI
        $cloPiMaps = DB::table('outline_clo_pi_maps')
            ->whereIn('clo_id', $cloIds)
            ->get();

        $cloPiMatrix = [];
        foreach ($cloPiMaps as $m) {
            $cloPiMatrix[$m->clo_id][$m->pi_id] = $m->level; // I/R/M/A
        }

        return view('giangvien.clo_mapping', [
            'courseVersion'  => $courseVersion,
            'clos'           => $clos,
            'plos'           => $plos,
            'pis'            => $pis,
            'cloPloMatrix'   => $cloPloMatrix,
            'cloPiMatrix'    => $cloPiMatrix,
        ]);
    }


    public function save(Request $request, $courseVersionId)
    {
        $userId = Auth::id();
        $now    = now();

        // Lấy lại CLO để biết clo_id hợp lệ
        $clos = DB::table('outline_clos')
            ->where('course_version_id', $courseVersionId)
            ->orderBy('id')
            ->get();

        if ($clos->isEmpty()) {
            return back()->with('error', 'Chưa có CLO để thực hiện mapping.');
        }

        $cloIds = $clos->pluck('id')->all();

        $cloPloInput = $request->input('clo_plo', []); // [clo_id][plo_id] => level
        $cloPiInput  = $request->input('clo_pi', []);  // [clo_id][pi_id]  => level

        // Các mức độ hợp lệ trên UI
        $validLevels = ['I', 'R', 'M', 'A'];

        DB::beginTransaction();

        try {
            // Xoá mapping cũ
            DB::table('outline_clo_plo_maps')
                ->whereIn('clo_id', $cloIds)
                ->delete();

            DB::table('outline_clo_pi_maps')
                ->whereIn('clo_id', $cloIds)
                ->delete();

            // Ghi lại mapping CLO–PLO
            foreach ($cloPloInput as $cloId => $row) {
                foreach ($row as $ploId => $level) {
                    $level = strtoupper(trim($level));

                    if (!in_array($level, $validLevels, true)) {
                        continue;
                    }

                    DB::table('outline_clo_plo_maps')->insert([
                        'clo_id'     => $cloId,
                        'plo_id'     => $ploId,
                        'level'      => $level,   // dùng luôn I/R/M/A
                        'weight'     => 1,
                        'created_by' => $userId,
                        'created_at' => $now,
                    ]);
                }
            }

            // Ghi lại mapping CLO–PI
            foreach ($cloPiInput as $cloId => $row) {
                foreach ($row as $piId => $level) {
                    $level = strtoupper(trim($level));

                    if (!in_array($level, $validLevels, true)) {
                        continue;
                    }

                    DB::table('outline_clo_pi_maps')->insert([
                        'clo_id'     => $cloId,
                        'pi_id'      => $piId,
                        'level'      => $level,
                        'weight'     => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }


            DB::commit();

            return back()->with('success', 'Đã lưu mapping CLO – PLO/PI (I–R–M–A) thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Lỗi khi lưu mapping: ' . $e->getMessage());
        }
    }


    public function preview($courseVersionId)
    {
        // Lấy lại thông tin version giống index()
        $courseVersion = DB::table('outline_course_versions as ocv')
            ->join('outline_program_courses as opc', 'ocv.program_course_id', '=', 'opc.id')
            ->join('courses as c', 'opc.course_id', '=', 'c.id')
            ->join('outline_program_versions as opv', 'opc.program_version_id', '=', 'opv.id')
            ->join('education_programs as ep', 'opv.education_program_id', '=', 'ep.id')
            ->leftJoin('semesters as s', 'opc.semester_id', '=', 's.id')
            ->leftJoin('academic_years as ay', 'opc.academic_year_id', '=', 'ay.id')
            ->where('ocv.id', $courseVersionId)
            ->select(
                'ocv.id',
                'ocv.version_no',
                'ocv.status',
                'opc.program_version_id',

                'c.course_code',
                'c.course_name',

                'ep.program_code',
                'ep.program_name',
                'opv.version_code as program_version_code',

                'ay.year_code as academic_year_code',
                's.semester_name'
            )
            ->first();

        if (!$courseVersion) {
            abort(404, 'Không tìm thấy phiên bản đề cương.');
        }

        // CLO
        $clos = DB::table('outline_clos')
            ->where('course_version_id', $courseVersionId)
            ->orderBy('code')
            ->get();

        if ($clos->isEmpty()) {
            return redirect()
                ->route('giangvien.outlines.clo.index', ['courseVersion' => $courseVersionId])
                ->with('error', 'Vui lòng soạn CLO trước khi xem bảng mapping.');
        }

        // PLO & PI (giống index)
        $plos = DB::table('outline_plos')
            ->where('program_version_id', $courseVersion->program_version_id)
            ->orderBy('code')
            ->get();

        if ($plos->isEmpty()) {
            return redirect()
                ->route('giangvien.outlines.edit', ['courseVersion' => $courseVersionId])
                ->with('error', 'CTĐT này chưa được khai báo PLO. Vui lòng bổ sung PLO trước khi mapping.');
        }

        $pis = DB::table('outline_pis as pi')
            ->join('outline_plos as plo', 'pi.plo_id', '=', 'plo.id')
            ->where('plo.program_version_id', $courseVersion->program_version_id)
            ->orderBy('plo.code')
            ->orderBy('pi.code')
            ->select(
                'pi.id',
                'pi.code',
                'pi.description',
                'plo.id as plo_id',
                'plo.code as plo_code'
            )
            ->get();

        if ($pis->isEmpty()) {
            return redirect()
                ->route('giangvien.outlines.edit', ['courseVersion' => $courseVersionId])
                ->with('error', 'CTĐT này chưa được khai báo các chỉ báo PI. Vui lòng bổ sung PI trước khi mapping.');
        }

        $cloIds = $clos->pluck('id')->all();

        // Mapping CLO–PI
        $cloPiMaps = DB::table('outline_clo_pi_maps')
            ->whereIn('clo_id', $cloIds)
            ->get();

        $cloPiMatrix = [];
        foreach ($cloPiMaps as $m) {
            $cloPiMatrix[$m->clo_id][$m->pi_id] = $m->level; // I/R/M/A
        }

        // Danh sách section để chọn chèn
        $sections = DB::table('outline_section_contents as c')
            ->join('outline_section_templates as st', 'c.section_template_id', '=', 'st.id')
            ->where('c.course_version_id', $courseVersionId)
            ->orderBy('st.order_no')
            ->select('st.id', 'st.code', 'st.title')
            ->get();

        if ($sections->isEmpty()) {
            return redirect()
                ->route('giangvien.outlines.edit', ['courseVersion' => $courseVersionId])
                ->with('error', 'Đề cương chưa có mục nội dung nào. Vui lòng chọn mẫu và lưu đề cương trước khi chèn bảng mapping.');
        }

        return view('giangvien.clo_mapping_preview', [
            'courseVersion' => $courseVersion,
            'clos'          => $clos,
            'plos'          => $plos,
            'pis'           => $pis,
            'cloPiMatrix'   => $cloPiMatrix,
            'sections'      => $sections,
        ]);
    }


    public function renderToSection(Request $request, $courseVersionId)
    {
        $sectionTemplateId = $request->input('section_template_id');
        $mode = $request->input('insert_mode', 'append'); // append | prepend | replace

        if (!$sectionTemplateId) {
            return back()->with('error', 'Vui lòng chọn mục đề cương để chèn bảng mapping.');
        }

        // Lấy thông tin cơ bản
        $courseVersion = DB::table('outline_course_versions as ocv')
            ->join('outline_program_courses as opc', 'ocv.program_course_id', '=', 'opc.id')
            ->where('ocv.id', $courseVersionId)
            ->select('ocv.id', 'opc.program_version_id')
            ->first();

        if (!$courseVersion) {
            return back()->with('error', 'Không tìm thấy phiên bản đề cương.');
        }

        // CLO
        $clos = DB::table('outline_clos')
            ->where('course_version_id', $courseVersionId)
            ->orderBy('code')
            ->get();

        if ($clos->isEmpty()) {
            return back()->with('error', 'Chưa có CLO để chèn bảng mapping.');
        }

        // PLO + PI
        $plos = DB::table('outline_plos')
            ->where('program_version_id', $courseVersion->program_version_id)
            ->orderBy('code')
            ->get();

        $pis = DB::table('outline_pis as pi')
            ->join('outline_plos as plo', 'pi.plo_id', '=', 'plo.id')
            ->where('plo.program_version_id', $courseVersion->program_version_id)
            ->orderBy('plo.code')
            ->orderBy('pi.code')
            ->select(
                'pi.id',
                'pi.code',
                'plo.id as plo_id',
                'plo.code as plo_code'
            )
            ->get();

        $cloIds = $clos->pluck('id')->all();

        $cloPiMaps = DB::table('outline_clo_pi_maps')
            ->whereIn('clo_id', $cloIds)
            ->get();

        if ($cloPiMaps->isEmpty()) {
            return back()->with('error', 'Chưa có dữ liệu mapping CLO – PI để chèn.');
        }

        // Xây matrix
        $cloPiMatrix = [];
        foreach ($cloPiMaps as $m) {
            if (!empty($m->level)) {
                $cloPiMatrix[$m->clo_id][$m->pi_id] = $m->level;
            }
        }

        // Lọc CLO/PI có mapping
        $usedPiIds = [];
        foreach ($cloPiMatrix as $cloId => $row) {
            foreach ($row as $piId => $level) {
                if (!empty($level)) {
                    $usedPiIds[$piId] = true;
                }
            }
        }
        $filteredPis = $pis->filter(fn($pi) => isset($usedPiIds[$pi->id]));
        $pisByPlo   = $filteredPis->groupBy('plo_id');

        $usedCloIds = [];
        foreach ($cloPiMatrix as $cloId => $row) {
            foreach ($row as $piId => $level) {
                if (!empty($level)) {
                    $usedCloIds[$cloId] = true;
                    break;
                }
            }
        }
        $filteredClos = $clos->filter(fn($clo) => isset($usedCloIds[$clo->id]));

        if ($filteredPis->isEmpty() || $filteredClos->isEmpty()) {
            return back()->with('error', 'Không có dòng/ cột mapping nào để chèn.');
        }

        // ==== Build HTML bảng (giống preview, đơn giản hoá chút) ====
        $html  = '<table border="1" cellspacing="0" cellpadding="4" style="border-collapse:collapse;width:100%">';
        $html .= '<thead>';

        // Hàng 1
        $html .= '<tr>';
        $html .= '<th rowspan="3" style="text-align:center;vertical-align:middle;">CLO</th>';
        $html .= '<th colspan="' . $filteredPis->count() . '" style="text-align:center;">PLO và PI</th>';
        $html .= '</tr>';

        // Hàng 2: PLO
        $html .= '<tr>';
        foreach ($pisByPlo as $ploId => $pisOfPlo) {
            $plo = $plos->firstWhere('id', $ploId);
            $colspan = $pisOfPlo->count();
            $html .= '<th colspan="' . $colspan . '" style="text-align:center;">' . e($plo?->code ?? 'PLO') . '</th>';
        }
        $html .= '</tr>';

        // Hàng 3: PI
        $html .= '<tr>';
        foreach ($pisByPlo as $ploId => $pisOfPlo) {
            $plo = $plos->firstWhere('id', $ploId);
            foreach ($pisOfPlo as $pi) {
                $html .= '<th style="text-align:center;">' . e(($plo?->code ?? 'PLO') . '.' . $pi->code) . '</th>';
            }
        }
        $html .= '</tr>';

        $html .= '</thead><tbody>';

        // Hàng CLO
        foreach ($filteredClos as $clo) {
            $html .= '<tr>';
            $html .= '<td><strong>' . e($clo->code) . '</strong></td>';

            foreach ($pisByPlo as $ploId => $pisOfPlo) {
                foreach ($pisOfPlo as $pi) {
                    $level = $cloPiMatrix[$clo->id][$pi->id] ?? '';
                    $html .= '<td style="text-align:center;">' . e($level) . '</td>';
                }
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        // Lấy nội dung section hiện tại
        $contentRow = DB::table('outline_section_contents')
            ->where('course_version_id', $courseVersionId)
            ->where('section_template_id', $sectionTemplateId)
            ->first();

        if (!$contentRow) {
            return back()->with('error', 'Không tìm thấy nội dung mục đề cương đã chọn.');
        }

        $oldHtml = $contentRow->content_html ?? '';

        // Áp dụng insert_mode
        switch ($mode) {
            case 'prepend':
                $newHtml = $html . '<br/>' . $oldHtml;
                break;
            case 'replace':
                $newHtml = $html;
                break;
            case 'append':
            default:
                $newHtml = $oldHtml . '<br/>' . $html;
                break;
        }

        DB::table('outline_section_contents')
            ->where('course_version_id', $courseVersionId)
            ->where('section_template_id', $sectionTemplateId)
            ->update([
                'content_html' => $newHtml,
                'updated_at'   => now(),
            ]);

        return redirect()
            ->route('giangvien.outlines.edit', ['courseVersion' => $courseVersionId])
            ->with('success', 'Đã chèn bảng mapping CLO – PI vào đề cương.');
    }
}
