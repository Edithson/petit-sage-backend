<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Type extends Model
{
    /** @use HasFactory<\Database\Factories\TypeFactory> */
    use HasFactory;

    // récupérer tous les utilisateurs d'un type en particulier
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
