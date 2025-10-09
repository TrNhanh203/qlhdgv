<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationProgram extends Model
{
    protected $table = 'education_programs';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'program_code',
        'program_name',
        'faculty_id',
        'education_system_code',
        'education_system_name',
    ];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'program_id');
    }
}
