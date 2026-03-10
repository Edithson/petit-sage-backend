<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Thematique;
use App\Models\Question;

class Partie extends Model
{
    /** @use HasFactory<\Database\Factories\PartieFactory> */
    use HasFactory, SoftDeletes;

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
