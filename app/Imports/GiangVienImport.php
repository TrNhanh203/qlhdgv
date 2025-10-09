<?php

namespace App\Imports;

use App\Models\Lecture;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class GiangVienImport implements ToCollection, WithHeadingRow
{
    protected $facultyId;
    protected $universityId;
    protected $errors = [];
    protected $messages = [];

    public function getMessages()
    {
        return $this->messages;
    }

    protected $departmentAlias = [
        'Công Nghệ Thông Tin'               => 'CNTT',
        'Bộ môn Khoa học Máy tính'          => 'KHMT',
        'Bộ môn Hệ thống Thông tin'         => 'HTTT',
        'Bộ môn Mạng và An toàn thông tin'  => 'ATTT',
        'Tiếng Anh'                         => 'EN',
    ];

    public function __construct($facultyId, $universityId)
    {
        $this->facultyId    = $facultyId;
        $this->universityId = $universityId;
    }

    public function collection(Collection $rows)
    {
        $headerMap = [
            'ma_gv'   => ['ma_gv','lecturer_code','id','mã gv','mã_gv'],
            'ho_ten'  => ['ho_va_ten_gv','ten_gv','full_name','name','họ và tên','họ_tên','hoten'],
            'hoc_vi'  => ['hoc_vi','degree'],
            'sdt'     => ['sdt','phone','điện thoại','số điện thoại'],
            'email'   => ['email','mail'],
            'bo_mon'  => ['bo_mon','department','khoa','bộ môn'],
        ];

        Log::info("📥 Import start - total rows: " . count($rows));

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; 
            try {
                $maGV   = $this->getValue($row, $headerMap['ma_gv']);
                $hoTen  = $this->getValue($row, $headerMap['ho_ten']);
                $hocVi  = $this->getValue($row, $headerMap['hoc_vi']);
                $sdt    = $this->getValue($row, $headerMap['sdt']);
                $email  = $this->getValue($row, $headerMap['email']);
                $boMon  = $this->getValue($row, $headerMap['bo_mon']);

                $maGV  = is_null($maGV) ? null : trim((string)$maGV);
                $hoTen = is_null($hoTen) ? null : trim((string)$hoTen);
                $hocVi = is_null($hocVi) ? null : trim((string)$hocVi);
                $sdt   = is_null($sdt) ? null : trim((string)$sdt);
                $email = is_null($email) ? null : trim((string)$email);
                $boMon = is_null($boMon) ? null : trim((string)$boMon);

                Log::info("➡️ Row #{$rowNumber} raw", [
                    'ma_gv' => $maGV, 'ho_ten' => $hoTen, 'hoc_vi' => $hocVi,
                    'sdt' => $sdt, 'email' => $email, 'bo_mon' => $boMon
                ]);

                if (!$hoTen || !$boMon) {
                    $msg = [];
                    if (!$hoTen) $msg[] = 'Thiếu họ tên';
                    if (!$boMon) $msg[] = 'Thiếu bộ môn';
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'message' => implode('; ', $msg)
                    ];
                    Log::warning("⚠️ Row #{$rowNumber} missing required fields", ['ho_ten'=>$hoTen,'bo_mon'=>$boMon]);
                    continue;
                }

                $aliasKey  = $this->departmentAlias[$boMon] ?? null;
                $searchKey = $aliasKey ?? $boMon;

                $department = null;
                $matchReason = null;

                if ($this->facultyId) {
                    $department = Department::where('faculty_id', $this->facultyId)
                        ->where(function($q) use ($searchKey) {
                            $q->where('department_name', $searchKey)
                              ->orWhere('department_code', $searchKey);
                        })->first();
                    $matchReason = $department ? 'faculty_exact' : null;
                }

                if (!$department && $this->universityId) {
                    $department = Department::whereHas('faculty', function($q) {
                            $q->where('university_id', $this->universityId);
                        })
                        ->where(function($q) use ($searchKey) {
                            $q->where('department_name', $searchKey)
                              ->orWhere('department_code', $searchKey);
                        })->first();
                    $matchReason = $department ? 'university_exact' : null;
                }
                if (!$department && $this->universityId) {
                    $departmentsForUniversity = Department::whereHas('faculty', function($q) {
                            $q->where('university_id', $this->universityId);
                        })->get();

                    $searchNorm = strtolower(Str::ascii($searchKey));
                    foreach ($departmentsForUniversity as $d) {
                        $nameNorm = strtolower(Str::ascii($d->department_name ?? ''));
                        $codeNorm = strtolower(Str::ascii($d->department_code ?? ''));
                        if ($nameNorm === $searchNorm || ($codeNorm && $codeNorm === $searchNorm)) {
                            $department = $d;
                            $matchReason = 'university_ascii';
                            break;
                        }
                    }
                }
                if (!$department) {
                    $assignedFacultyId = $this->facultyId;
                    if (!$assignedFacultyId && $this->universityId) {
                        $faculty = Faculty::where('university_id', $this->universityId)->first();
                        if ($faculty) {
                            $assignedFacultyId = $faculty->id;
                            Log::info("📌 Row #{$rowNumber}: dùng faculty_id={$assignedFacultyId} (faculty đầu tiên của university)");
                        }
                    }

                    if (!$assignedFacultyId) {
                        Log::error("❌ Row #{$rowNumber}: không có faculty để gán cho '{$boMon}'");
                        $this->errors[] = [
                            'row' => $rowNumber,
                            'message' => "Không tìm thấy faculty để gán cho bộ môn '{$boMon}'"
                        ];
                        continue;
                    }

                    $deptCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', Str::slug($boMon, '')), 0, 10)) ?: 'DEPT';
                    $department = Department::create([
                        'department_name' => $boMon,
                        'department_code' => $deptCode,
                        'faculty_id'      => $assignedFacultyId,
                        'status_id'       => 8,
                    ]);
                    $matchReason = 'created_new';
                    Log::warning("➕ Row #{$rowNumber}: created new department", [
                        'given' => $boMon,
                        'created_id' => $department->id,
                        'faculty_id' => $assignedFacultyId
                    ]);
                } else {
                    Log::info("✅ Row #{$rowNumber}: matched department", [
                        'department_id' => $department->id,
                        'department_name' => $department->department_name,
                        'matchReason' => $matchReason
                    ]);
                }

                /**
                 */
                $lectureWhere = [];
                if ($maGV) {
                    $lectureWhere = ['lecturer_code' => $maGV];
                } else {
                    if ($email) {
                        $lecture = Lecture::where('email', $email)->first();
                    } elseif ($sdt) {
                        $lecture = Lecture::where('phone', $sdt)->first();
                    } else {
                        $lecture = null;
                    }

                    if (!empty($lecture)) {
                        $lectureWhere = ['id' => $lecture->id];
                    }
                }

                $attributes = [
                    'full_name'     => $hoTen,
                    'degree'        => $hocVi,
                    'phone'         => $sdt,
                    'email'         => $email,
                    'department_id' => $department->id,
                    'faculty_id'    => $this->facultyId ?? $department->faculty_id,
                    'university_id' => $this->universityId,
                    'status_id'     => 1,
                ];

                if ($maGV) {
                    $attributes['lecturer_code'] = $maGV;
                }

                $lecture = null;

                if ($maGV) {
                    $lecture = Lecture::where('lecturer_code', $maGV)->first();
                } elseif ($email) {
                    $lecture = Lecture::where('email', $email)->first();
                } elseif ($sdt) {
                    $lecture = Lecture::where('phone', $sdt)->first();
                }

                if ($lecture) {
                    if ($lecture->email === $email && $lecture->phone === $sdt) {
                        $this->messages[] = "⏩ Giảng viên '{$hoTen}' (Email: {$email}) đã tồn tại, bỏ qua.";
                        Log::info("⏩ Row #{$rowNumber}: skipped, already exists", ['lecture_id' => $lecture->id]);
                    } else {
                        $lecture->update($attributes);
                        $this->messages[] = "✏️ Giảng viên '{$hoTen}' được cập nhật.";
                        Log::info("✏️ Row #{$rowNumber}: updated lecture", ['lecture_id' => $lecture->id]);
                    }
                } else {
                    $lecture = Lecture::create($attributes);
                    $this->messages[] = "👤 Giảng viên '{$hoTen}' được thêm mới.";
                    Log::info("👤 Row #{$rowNumber}: created lecture", ['lecture_id' => $lecture->id]);
                }

            } catch (\Throwable $e) {
                Log::error("❌ Import - Row #{$rowNumber} exception: " . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'row' => $row->toArray()
                ]);
                $this->errors[] = [
                    'row' => $rowNumber,
                    'message' => 'Exception: ' . $e->getMessage()
                ];
                continue;
            }
        } 
    }

    protected function getValue($row, $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            $keyNorm = strtolower(Str::ascii($key));
            foreach ($row->keys() as $col) {
                $colNorm = strtolower(Str::ascii($col));
                if ($colNorm === $keyNorm) {
                    return $row[$col];
                }
            }
        }

        foreach ($row->keys() as $col) {
            $colNorm = strtolower(Str::ascii($col));
            foreach ($possibleKeys as $key) {
                if (strpos($colNorm, strtolower(Str::ascii($key))) !== false) {
                    return $row[$col];
                }
            }
        }

        return null;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
