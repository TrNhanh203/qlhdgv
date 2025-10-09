<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamProctoring extends Model
{
    protected $table = 'exam_proctorings';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'exam_id',
        'lecture_id',
        'room_id',
        'exam_date',
        'start_time',
        'end_time',
        'role',
        'status'
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
    public function status() {
    return $this->belongsTo(StatusCode::class, 'status', 'code');
    }
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
} 