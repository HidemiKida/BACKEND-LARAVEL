<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    use HasFactory;

    protected $primaryKey = 'mesa_id';
    protected $table = 'mesa';
    protected $fillable = ['numero_mesa', 'capacidad', 'ubicacion','restaurante_id'];

    public function restaurante()
    {
        return $this->belongsTo(Restaurante::class, 'restaurante_id');
    }

    public function disponibilidad()
    {
        return $this->hasMany(Disponibilidad::class, 'mesa_id');
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'mesa_id');
    }
}