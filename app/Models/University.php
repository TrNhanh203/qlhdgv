<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    protected $table = 'universities';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $casts = [
    'founded_date' => 'datetime',
];

    protected $fillable = [
        'id',
        'university_name',
        'university_type',
        'address',
        'phone',
        'email',
        'website',
        'status_id',
        'logo',
        'description',
        'founded_date',
        'fanpage',
        'created_at',
        'updated_at'
    ];
public function getLogoUrlAttribute()
    {
        if ($this->logo && file_exists(public_path('logos/'.$this->logo))) {
            return asset('logos/'.$this->logo);
        }
        return asset('images/default-logo.png');
    }
    public function getCodeShortAttribute()
    {
        if ($this->email && preg_match('/@([^.]+)\./', $this->email, $matches)) {
            return strtoupper($matches[1]);
        }

        $words = explode(' ', $this->university_name);
        $short = '';
        foreach ($words as $w) {
            if (strlen($w) > 2) $short .= strtoupper($w[0]);
        }
        return $short;
    }
        public function status()
        {
            return $this->belongsTo(StatusCode::class, 'status_id');
        }
    public function faculties()
    {
        return $this->hasMany(Faculty::class);
    }

    public function lectures()
    {
        return $this->hasMany(Lecture::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
    public function educationPrograms()
    {
         return $this->hasManyThrough(EducationProgram::class, Faculty::class); 
    }
    public function courses()
    {
        return $this->hasManyThrough(Course::class, Department::class, 'faculty_id', 'department_id');
    }
    public function academicYears()
    {
        return $this->hasMany(AcademicYear::class);
    }
    public function semesters()
    {
        return $this->hasManyThrough(Semester::class, AcademicYear::class);
    }

    public function exams()
    {
        return $this->hasManyThrough(Exam::class, Course::class);
    }

    public function meetings()
    {
        return $this->hasManyThrough(Meeting::class, Department::class);
    }

} 