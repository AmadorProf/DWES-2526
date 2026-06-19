<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deporte extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'nombre',
        'slug',
        'descripcion_corta',
        'contenido',
        'jugadores',
        'origen',
        'imagen',
        'activo'
    ];

    /**
     * Los atributos que deben ser casteados.
     */
    protected $casts = [
        'activo' => 'boolean',
        'jugadores' => 'integer'
    ];

    /**
     * Scope para obtener solo deportes activos
     */
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }
}
