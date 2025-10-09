<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeachingDuty extends Model
{
    protected $table = 'teaching_duties';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'duty_type',
        'lecture_id',
        'course_id',
        'academic_year_id',
        'semester_id',
        'room_id',
        'teaching_date',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'teaching_date' => 'date',
        'start_time'    => 'datetime',
        'end_time'      => 'datetime',
    ];

    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
