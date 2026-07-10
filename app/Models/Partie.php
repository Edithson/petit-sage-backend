<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Partie extends Model
{
    /** @use HasFactory<\Database\Factories\PartieFactory> */
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $guarded = [];

    public function thematique()
    {
        return $this->belongsTo(Thematique::class, 'thematique_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'partie_id');
    }
}
