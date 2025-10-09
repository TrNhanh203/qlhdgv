<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationRead extends Model
{
    protected $fillable = ['notification_id', 'user_id', 'read_at'];

    public function notification()
    {
        return $this->belongsTo(SystemNotification::class, 'notification_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
