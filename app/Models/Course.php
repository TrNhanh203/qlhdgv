<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    public $incrementing = true; // id là auto increment
    protected $keyType = 'int';

    protected $fillable = [
        'course_code',
        'course_name',
        'course_group_id',
        'course_group',
        'credit',
        'education_program_id',
        'department_id',
        'semester_id',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }


    public function faculty()
    {
        return $this->hasOneThrough(
            Faculty::class,
            Department::class,
            'id', // khóa ngoại của Department trong bảng Faculty
            'id', // khóa chính của Faculty
            'department_id', // khóa ngoại của Course trong Department
            'faculty_id'   // khóa ngoại trong Department liên kết tới Faculty
        );
    }
    public function syllabuses()
    {
        return $this->hasMany(Attachment::class, 'entity_id')
            ->whereHas('moduleType', function ($q) {
                $q->where('name', 'course_syllabus');
            });
    }

    // Quan hệ tới EducationProgram
    public function educationProgram()
    {
        // return $this->belongsTo(EducationProgram::class, 'education_program_id');
        return $this->belongsTo(EducationProgram::class);
    }

    // Các quan hệ khác nếu cần, ví dụ TeachingDuty
    public function teachingDuties()
    {
        return $this->hasMany(TeachingDuty::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
