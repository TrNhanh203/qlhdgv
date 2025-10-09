<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LectureRole extends Model
{
    protected $table = 'lecture_roles';

    protected $fillable = [
        'lecture_id', 'role_id', 'faculty_id', 'department_id', 'status_id', 'start_date', 'end_date'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function lecture()
    {
        return $this->belongsTo(Lecture::class, 'lecture_id');
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');    
    }
}
