<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Faculty extends Model
{
    protected $table = 'faculties';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'faculty_code',
        'faculty_name',
        'description',
        'university_id',
        'status_id',
    ];

    protected static function boot()
    {
        parent::boot();
        
    }

    public function getStatusAttribute()
    {
        return $this->status_id == 1 ? 'active' : 'inactive';
    }

    public function university()
{
    return $this->belongsTo(University::class, 'university_id', 'id');
}

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function educationPrograms()
    {
        return $this->hasMany(EducationProgram::class);
    }
    public function status()
{
    return $this->belongsTo(StatusCode::class, 'status_id');
}

}
