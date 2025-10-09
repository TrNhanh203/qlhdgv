<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workload extends Model
{
    protected $table = 'workloads';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'lecture_id',
        'academic_year_id',
        'semester_id',
        'hours',
        'description',
        'status'
    ];

    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
} 