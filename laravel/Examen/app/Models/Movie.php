<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'director',
        'year',
        'genre',
        'duration',
        'synopsis',
        'cast',
        'country',
        'poster',
        'age_rating',
        'average_rating',
        'user_id',
    ];

    /**
     * Relación: Una película pertenece a un usuario (creador)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Una película puede tener muchas valoraciones
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Relación: Una película puede tener muchas reseñas
     */
    public function reviews()
    {
        return $this->hasMany(Review::class)->latest();
    }

    /**
     * Actualizar el promedio de valoraciones de la película
     */
    public function updateAverageRating()
    {
        $average = $this->ratings()->avg('score');
        $this->average_rating = round($average, 2);
        $this->save();
    }

    /**
     * Obtener la valoración de un usuario específico
     */
    public function getUserRating($userId)
    {
        return $this->ratings()->where('user_id', $userId)->first();
    }
}
