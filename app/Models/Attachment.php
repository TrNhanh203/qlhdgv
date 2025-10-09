<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $table = 'attachments';

    protected $fillable = [
        'module_type_id',
        'entity_id',
        'file_name',
        'file_path',
        'uploaded_by',
        'uploaded_at',
    ];

    public $timestamps = false; 

    protected $dates = [
        'uploaded_at',
    ];
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    public function moduleType()
    {
        return $this->belongsTo(ModuleType::class, 'module_type_id');
    }
    public function course()
    {
        return $this->belongsTo(Course::class, 'entity_id');
    }
}
