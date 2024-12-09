<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;

    protected $primaryKey = 'permiso_id';
    protected $table = 'permiso';
    protected $fillable = ['role_id', 'accion'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}