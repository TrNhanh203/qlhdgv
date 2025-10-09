<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $table = 'exams';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'course_id',
        'academic_year_id',
        'semester_id',
        'exam_name',
        'exam_type',
        'exam_batch',
        'exam_start',
        'exam_end',
        'exam_form',
        'room_id',
        'expected_students',
        'notes'
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
     public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function proctorings()
    {
        return $this->hasMany(ExamProctoring::class);
    }
    public function status()
    {
        return $this->belongsTo(StatusCode::class, 'status_id');
    }
} 