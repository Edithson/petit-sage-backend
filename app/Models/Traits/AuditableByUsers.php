<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;

trait AuditableByUsers
{
    /**
     * Boot the trait and register model events.
     * Démarre le trait et enregistre les événements du modèle.
     */
    protected static function bootAuditableByUsers(): void
    {
        // Événement exécuté AVANT la création
        static::creating(function ($model) {
            // S'assurer que 'created_by' est rempli si l'utilisateur est authentifié
            if (Auth::check() && is_null($model->created_by)) {
                $model->created_by = Auth::id();
            }
            // L'événement 'updating' gère 'last_updated_by', mais par cohérence on peut aussi le mettre ici.
            // Cependant, les timestamps (updated_at) sont automatiques. On va se concentrer sur 'created_by' ici.
        });

        // Événement exécuté AVANT la mise à jour (y compris soft delete)
        static::updating(function ($model) {
            // S'assurer que 'last_updated_by' est rempli si l'utilisateur est authentifié
            if (Auth::check() && $model->isDirty()) {
                $model->last_updated_by = Auth::id();
            }
        });

        // Événement exécuté AVANT la soft delete
        static::deleting(function ($model) {
            // S'assurer que 'deleted_by' est rempli si l'utilisateur est authentifié
            // Note: ceci ne fonctionnera que si vous utilisez la méthode delete() sur le modèle, pas un raw query
            if (Auth::check() && $model->usesSoftDeletes()) {
                $model->deleted_by = Auth::id();
                // Nous devons forcer la sauvegarde pour enregistrer 'deleted_by' avant la suppression (qui est une mise à jour)
                $model->saveQuietly();
            }
        });

        // Événement exécuté APRES la restauration (restore)
        static::restored(function ($model) {
            // Si restauré, on annule le 'deleted_by'
            $model->deleted_by = null;
            // Et on met à jour 'last_updated_by'
            if (Auth::check()) {
                $model->last_updated_by = Auth::id();
            }
            // Sauvegarder sans déclencher l'événement 'updating'
            $model->saveQuietly();
        });
    }

    /**
     * Vérifie si le modèle utilise SoftDeletes (pour le 'deleting' event).
     */
    protected function usesSoftDeletes(): bool
    {
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this));
    }
}
