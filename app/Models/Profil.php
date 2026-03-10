<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Badge;
use App\Models\Niveau;
use App\Models\User;
use App\Models\Evaluation;

class Profil extends Model
{
    /** @use HasFactory<\Database\Factories\ProfilFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'badge_users', 'profil_id', 'badge_id')->withTimestamps();
    }

    /**
     * Relation One-to-Many avec les Evaluations.
     */
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'profil_id');
    }

    public function niveau()
    {
        return $this->belongsTo(Niveau::class, 'niveau_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
