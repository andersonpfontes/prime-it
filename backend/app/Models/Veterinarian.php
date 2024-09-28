<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Veterinarian extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'specialization',
        'user_id',
    ];

    /**
     * Relacionamento com o usuário
     * O veterinário pode estar associado a um usuário.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com as marcações
     * Um veterinário pode ter várias marcações atribuídas a ele.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
