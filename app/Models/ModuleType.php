<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleType extends Model
{
    use HasFactory;

    protected $table = 'module_types';

    protected $fillable = [
        'name',
        'description',
    ];

    public $timestamps = false;

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'module_type_id');
    }
}
