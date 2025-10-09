<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusCode extends Model
{
    protected $table = 'status_codes';

    protected $fillable = ['name', 'description'];

    public $timestamps = false;
    public function exams()
    {
        return $this->hasMany(Exam::class, 'status_id');
    }
}
