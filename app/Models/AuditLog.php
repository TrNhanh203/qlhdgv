<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'table_name',
        'action',
        'entity_id',
        'logged_at',
        'changes_json',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
        'changes_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getCreatedAtAttribute()
{
    return $this->logged_at;
}

}
