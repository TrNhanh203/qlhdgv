<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    use HasFactory;

    protected $table = 'lectures';

    protected $fillable = [
        'lecturer_code',
        'full_name',
        'degree',
        'email',
        'phone',
        'department_id',
        'university_id',
        'status_id',
    ];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'lecture_roles', 'lecture_id', 'role_id')
                    ->withPivot('faculty_id', 'department_id', 'start_date', 'end_date')
                    ->withTimestamps();
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }



    
    public function university()
    {
        return $this->belongsTo(University::class, 'university_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo(StatusCode::class, 'status_id', 'id');
    }

    public function teachingDuties()
    {
        return $this->hasMany(TeachingDuty::class);
    }

    public function examProctorings()
    {
        return $this->hasMany(ExamProctoring::class);
    }

    public function workloads()
    {
        return $this->hasMany(Workload::class);
    }

    public function meetings()
    {
        return $this->belongsToMany(Meeting::class, 'meeting_participants', 'lecture_id', 'meeting_id');
    }

    public function lectureRoles()
    {
        return $this->hasMany(LectureRole::class, 'lecture_id', 'id');
    }
}
