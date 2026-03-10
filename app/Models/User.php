<?php

namespace App\Models;

use App\Models\Type;
use App\Models\Badge;
use App\Models\Niveau;
use App\Models\Question;
use App\Models\Evaluation;
use App\Models\Profil;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Traits\AuditableByUsers;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, AuditableByUsers;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'google_id',
        'name',
        'email',
        'sexe',
        'age',
        'telephone',
        'profil',
        'password',
        'type_id',
        'niveau_id',
        'email_verified_at',
        'created_by',
        'last_updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relation Many-to-Many avec les Badges via la table pivot badge_users.
     */
    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'badge_users', 'user_id', 'badge_id')->withTimestamps();
    }

    /**
     * Relation One-to-Many avec les Evaluations.
     */
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'user_id');
    }

    /**
     * Relation One-to-Many avec les Questions.
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'user_id');
    }

    public function niveau()
    {
        return $this->belongsTo(Niveau::class, 'niveau_id');
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function profiles()
    {
        return $this->hasMany(Profil::class, 'user_id');
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


