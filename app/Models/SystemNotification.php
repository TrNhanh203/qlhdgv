<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'created_by',
        'is_global',
        'university_id',
        'start_at',
        'end_at',
        'is_active',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reads()
    {
        return $this->hasMany(NotificationRead::class, 'notification_id');
    }
}
