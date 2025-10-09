<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'rooms';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'type',
        'category',
        'capacity',
        'location',
        'university_id',
        'status_id'
    ];
    protected static function boot()
    {
        parent::boot();
        
    }
    
    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function teachingDuties()
    {
        return $this->hasMany(TeachingDuty::class);
    }

    public function examProctorings()
    {
        return $this->hasMany(ExamProctoring::class);
    }
} 