<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'person_name',
        'email',
        'animal_name',
        'animal_type',
        'age',
        'symptoms',
        'appointment_date',
        'period',
        'user_id',
        'veterinarian_id',
    ];

    /**
     * Relacionamento com o usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com o veterinário
     * Um veterinário pode ser atribuído a uma marcação.
     */
    public function veterinarian()
    {
        return $this->belongsTo(Veterinarian::class)->withDefault();
    }
}
