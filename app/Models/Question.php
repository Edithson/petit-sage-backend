<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Thematique;
use App\Models\User;
use App\Models\Partie;
use App\Models\Traits\AuditableByUsers;

class Question extends Model
{
    /** @use HasFactory<\Database\Factories\QuestionFactory> */
    use HasFactory, SoftDeletes, AuditableByUsers;

    protected $guarded = [];

    protected $casts = [
        'reponses' => 'array', // Cast JSON responses to array
    ];

    public function thematique()
    {
        return $this->belongsTo(Thematique::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function partie()
    {
        return $this->belongsTo(Partie::class);
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
