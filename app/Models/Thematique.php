<?php

namespace App\Models;

use App\Models\Badge;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Partie;
use App\Models\Question;
use App\Models\Niveau;
use App\Models\Traits\AuditableByUsers;

class Thematique extends Model
{
    /** @use HasFactory<\Database\Factories\ThematiqueFactory> */
    use HasFactory, SoftDeletes, AuditableByUsers;

    protected $guarded = [];

    public function badges()
    {
        return $this->hasMany(Badge::class, 'thematique_id');
    }

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    /**
     * Relation Many-to-One (réflexive) pour le thème parent.
     */
    public function parentTheme()
    {
        return $this->belongsTo(Thematique::class, 'parent_id');
    }

    /**
     * Relation One-to-Many (réflexive) pour les sous-thèmes.
     */
    public function subThemes()
    {
        return $this->hasMany(Thematique::class, 'parent_id');
    }

    public function parties()
    {
        return $this->hasMany(Partie::class, 'thematique_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'thematique_id');
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
