<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $table = 'academic_years';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'year_code',
        'start_date',
        'end_date',
        'university_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    public function university()
    {
        return $this->belongsTo(University::class);
    }
    public function semesters()
    {
        return $this->hasMany(Semester::class, 'academic_year_id');
    }
}
