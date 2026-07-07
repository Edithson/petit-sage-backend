<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\User;
use App\Models\Badge;
use App\Models\Profil;

class BadgeUser extends Pivot
{
    protected $table = 'badge_users';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    public function profil()
    {
        return $this->belongsTo(Profil::class);
    }
}
