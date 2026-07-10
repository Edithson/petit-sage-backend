<?php

namespace App\Models;

use App\Models\User;
use App\Models\Partie;
use App\Models\Thematique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Evaluation extends Model
{
    /** @use HasFactory<\Database\Factories\EvaluationFactory> */
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation Many-to-One inverse avec la Thématique.
     */
    public function thematique()
    {
        return $this->belongsTo(Thematique::class, 'thematique_id');
    }

    public function partie()
    {
        return $this->belongsTo(Partie::class, 'partie_id');
    }
}
