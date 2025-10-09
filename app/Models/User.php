<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'university_id',
        'status_id',
        'user_type',
        'role',
        'lecture_id',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role === $roleName;
    }

    public function university()
    {
        return $this->belongsTo(University::class, 'university_id', 'id');
    }

    public function lecture()
    {
        return $this->belongsTo(Lecture::class, 'lecture_id', 'id');
    }
    public function getUniversityCode(): string
{
    if (!$this->email) {
        return 'N/A';
    }

    $domainPart = explode('@', $this->email)[1] ?? '';
    
    $code = explode('.', $domainPart)[0] ?? '';

    return strtoupper($code); 
}
    public function roles()
    {
        if ($this->lecture_id) {
            return $this->lecture->roles();
        }

        if ($this->user_type) {
            return collect([(object)['role_name' => $this->user_type]]);
        }

        return collect();
    }

public function getFacultyName()
{
    if (!$this->lecture) {
        return null;
    }

    $lecture = $this->lecture;

    if ($lecture->department?->faculty) {
        return $lecture->department->faculty->faculty_name;
    }

    $facultyRole = $lecture->roles()
        ->where('role_name', 'truongkhoa')
        ->first();

    if ($facultyRole?->pivot?->faculty_id) {
        
        return \App\Models\Faculty::find($facultyRole->pivot->faculty_id)?->faculty_name;
    }

    return null;
}

}
