<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id',                 
        'department_code',
        'department_name',
        'description',
        'faculty_id',
        'status_id',
    ];

    public function faculty()
{
    return $this->belongsTo(Faculty::class, 'faculty_id');
}

    public function lectures()
    {
        return $this->hasMany(Lecture::class);
    }

    public function educationPrograms()
    {
        return $this->hasMany(EducationProgram::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }
    public function status()
    {
        return $this->belongsTo(StatusCode::class, 'status_id');    
    }

} 