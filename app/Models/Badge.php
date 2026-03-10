<?php

namespace App\Models;

use App\Models\User;
use App\Models\Profil;
use App\Models\Thematique;
use App\Models\Traits\AuditableByUsers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Badge extends Model
{
    /** @use HasFactory<\Database\Factories\BadgeFactory> */
    use HasFactory, SoftDeletes, AuditableByUsers;

    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class, 'badge_users', 'badge_id', 'user_id')->withTimestamps();
    }

    public function profils()
    {
        return $this->belongsToMany(Profil::class, 'badge_users', 'badge_id', 'profil_id')->withTimestamps();
    }

    /**
     * Relation One-to-Many inverse avec la Thématique.
     */
    public function thematique()
    {
        return $this->belongsTo(Thematique::class, 'thematique_id');
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
