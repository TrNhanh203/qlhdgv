<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['role_name', 'description'];

    public function lectures()
    {
        return $this->belongsToMany(
            Lecture::class,
            'lecture_roles',
            'role_id',
            'lecture_id'
        );
    }
}
