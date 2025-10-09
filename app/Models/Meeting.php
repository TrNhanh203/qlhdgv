<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $table = 'meetings';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'meeting_title',
        'meeting_date',
        'start_time',
        'end_time',
        'location',
        'agenda',
        'department_id',
        'status'
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function participants()
    {
        return $this->belongsToMany(Lecture::class, 'meeting_participants', 'meeting_id', 'lecture_id');
    }
} 