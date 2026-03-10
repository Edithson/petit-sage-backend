<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    use HasFactory;

    protected $table = 'password_reset_tokens'; // Nom de la table

    protected $primaryKey = 'email'; // La clé primaire est l'email

    public $incrementing = false; // L'email n'est pas un auto-incrément

    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    public $timestamps = false; // Pas de champs updated_at ici, seulement created_at

    protected $casts = [
        'created_at' => 'datetime', // Caster 'created_at' en datetime
    ];
}