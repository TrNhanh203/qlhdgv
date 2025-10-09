<?php

namespace App\Exports;

use App\Models\Lecture;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GiangVienExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithStyles
{
    protected string $universityId;
    
    public function __construct(string $universityId)
    {
        $this->universityId = $universityId;
    }

    public function collection()
    {
        return Lecture::with('department')
            ->where('university_id', $this->universityId)
            ->get();
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mã GV',
            'Họ và Tên GV',
            'Học Vị',
            'SĐT',
            'Email',
            'Bộ môn',
            'Chú thích',
        ];
    }

    public function map($lecture): array
    {
        static $stt = 0;
        $stt++;

        return [
            $stt,
            $lecture->lecturer_code,
            $lecture->full_name,
            $lecture->degree,
            $lecture->phone,
            $lecture->email,
            $lecture->department ? $lecture->department->department_name : 'Chưa gán',
            '',
        ];
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setARGB('FFB0C4DE'); 
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        
        $sheet->getStyle('A1:G' . $sheet->getHighestRow())->getBorders()->getAllBorders()
              ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    }
}
