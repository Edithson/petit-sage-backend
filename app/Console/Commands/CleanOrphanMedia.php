<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Question;

class CleanOrphanMedia extends Command
{
    /**
     * Le nom et la signature de la commande.
     * J'ai ajouté l'option --dry-run pour tester sans rien casser.
     *
     * @var string
     */
    protected $signature = 'game:clean-media {--dry-run : Affiche les fichiers à supprimer sans les effacer réellement}';

    /**
     * La description de la commande.
     *
     * @var string
     */
    protected $description = 'Traque et supprime les fichiers audios et images orphelins (non référencés en base de données).';

    /**
     * Exécution de la commande.
     */
    public function handle()
    {
        $this->info("Début du scan des fichiers...");
        $isDryRun = $this->option('dry-run');

        $validAudioFiles = [];
        $validMediaFiles = [];

        // 1. Extraire TOUS les textes et chemins médias valides depuis la base de données
        // On utilise chunk() pour ne pas saturer la RAM si tu as des milliers de questions
        Question::chunk(100, function ($questions) use (&$validAudioFiles, &$validMediaFiles) {
            foreach ($questions as $question) {
                // Audios de l'intitulé
                if (!empty($question->intitule_text)) {
                    $validAudioFiles[] = 'audio/' . md5($question->intitule_text) . '.mp3';
                } elseif (!empty($question->intitule_media_description)) {
                    $validAudioFiles[] = 'audio/' . md5($question->intitule_media_description) . '.mp3';
                }

                // Médias de l'intitulé (images/vidéos)
                if ($question->intitule_media_url && str_contains($question->intitule_media_url, '/storage/')) {
                    $validMediaFiles[] = str_replace('/storage/', '', $question->intitule_media_url);
                }

                // Audios et Médias des options
                $options = is_string($question->reponses) ? json_decode($question->reponses, true) : $question->reponses;
                if (is_array($options)) {
                    foreach ($options as $opt) {
                        if (!empty($opt['text'])) {
                            $validAudioFiles[] = 'audio/' . md5($opt['text']) . '.mp3';
                        } elseif (!empty($opt['mediaDescription'])) {
                            $validAudioFiles[] = 'audio/' . md5($opt['mediaDescription']) . '.mp3';
                        }

                        if (!empty($opt['mediaUrl']) && str_contains($opt['mediaUrl'], '/storage/')) {
                             $validMediaFiles[] = str_replace('/storage/', '', $opt['mediaUrl']);
                        }
                    }
                }
            }
        });

        // Nettoyer les doublons dans nos tableaux
        $validAudioFiles = array_unique($validAudioFiles);
        $validMediaFiles = array_unique($validMediaFiles);
        $allValidFiles = array_merge($validAudioFiles, $validMediaFiles);

        // 2. Récupérer tous les fichiers réellement présents sur le disque
        $disk = Storage::disk('public');
        $allFilesOnDisk = array_merge($disk->files('audio'), $disk->files('questions'));

        // 3. Trouver les coupables (les fichiers sur le disque qui ne sont pas dans la liste valide)
        $orphans = array_diff($allFilesOnDisk, $allValidFiles);

        if (empty($orphans)) {
             $this->info("Aucun fichier orphelin trouvé. L'espace de stockage est propre !");
             return Command::SUCCESS;
        }

        $this->warn("Trouvé " . count($orphans) . " fichier(s) orphelin(s).");

        // 4. Procéder à la suppression
        foreach ($orphans as $orphan) {
            if ($isDryRun) {
                $this->line("[DRY-RUN] Serait supprimé : " . $orphan);
            } else {
                $disk->delete($orphan);
                $this->line("[SUPPRIMÉ] : " . $orphan);
            }
        }

        $this->info("Nettoyage terminé !");
        return Command::SUCCESS;
    }
}