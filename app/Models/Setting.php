<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // la table settings est une table singleton et ne peut recevoir qu'un seul enregistrement
    protected $fillable = [
        'title_welcome',
        'description',
        'favicon',
        'phone',
        'email',
        'address',
        'social_networks',
        'schedule',
    ];

    // les casts
    protected $casts = [
        'social_networks' => 'array',
    ];

    // les scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
