<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Thematique;
use App\Models\User;
use App\Models\Traits\AuditableByUsers;

class Niveau extends Model
{
    /** @use HasFactory<\Database\Factories\NiveauFactory> */
    use HasFactory, SoftDeletes, AuditableByUsers;

    protected $guarded = [];

    public function thematiques()
    {
        return $this->hasMany(Thematique::class, 'niveau_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'niveau_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Récupère l'utilisateur ayant effectué la dernière modification.
     */
    public function lastUpdater()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    /**
     * Récupère l'utilisateur ayant soft-supprimé l'enregistrement.
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
