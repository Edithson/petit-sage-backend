<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AllDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {

        // Utilisation de Carbon pour les timestamps
        $now = Carbon::now();

        // 1. Insertion du Niveau de base (Niveau 1 par défaut)
        // Table: niveaux
        $niveau_id = DB::table('niveaux')->insertGetId([
            'numero' => 1,
            'nom' => 'Niveau de base',
            'description' => 'Niveau 1 de base créé par défaut pour toutes les thématiques.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Insertion de la Thématique 1
        // Table: thematiques
        $thematique_id = DB::table('thematiques')->insertGetId([
            'name' => 'Le vivre-ensemble', // Thématique 1
            'description' => 'Le vivre-ensemble est un concept central qui permet de comprendre différents rapports entre les divers hommes vivant dans la même société. Il s\'agit de leurs rapports au temps, l\'espace, la générosité, la liberté, au travail, et à la politesse.', // cite: 1
            'nbr_min_point' => '10', // cite: 1
            'niveau_id' => 1, // cite: 1
            'media_url' => 'https://youtube.com/watch?v=pLDZ7dD2sLc&feature=shared', // cite: 1
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. Insertion des Parties (Sous-Thématiques)

        // Partie 1: Le petit voyage de Koulou la tortue (Numéro 1 pour la Thématique 1)
        // Table: parties
        $partie1_id = DB::table('parties')->insertGetId([
            'numero' => 1, // Numéro hiérarchique pour la Thématique 1
            'name' => 'Le petit voyage de Koulou la tortue', // [cite: 1]
            'description' => 'Lorsqu\'on voyage on tient compte du temps et de l\'espace, c\'est-à-dire de l\'heure de départ ou d\'arrivée, de la durée du voyage... notre ami la tortue n\'a pas l\'air de le savoir !', // [cite: 1]
            'thematique_id' => $thematique_id,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Partie 2: La petite tête de mule de Fari l'âne (Numéro 2 pour la Thématique 1)
        // Table: parties
        $partie2_id = DB::table('parties')->insertGetId([
            'numero' => 2, // Numéro hiérarchique pour la Thématique 1
            'name' => 'La petite tête de mule de Fari l\'âne', // // [cite: 7]
            'description' => 'La têtutesse qualifie une personne qui refuse d\'obéir à une règle/loi pour ne que chercher à obtenir de manière déterminer sa propre liberté. C\'est le cas de notre ami la Fari.', // // [cite: 7]
            'thematique_id' => $thematique_id,
            'created_at' => $now,
            'updated_at' => $now,
        ]);


        // 4. Insertion des Questions pour la Partie 1 (Koulou) - Numérotation de 1 à 17
        // Table: questions
        $questions_partie1 = [
            [
                'numero' => 1,
                'intitule_text' => 'Alors mes amies, dans la vidéo que vous venez regarder, quel est le problème de notre petite tortue Koulou par rapport au léopard ?', // [cite: 1]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Le problème se trouve dans ce que la petite tortue n\'arrive pas à faire.', // [cite: 1]
                'reponses' => json_encode([
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Son problème c\'est parce qu\'elle marche lentement'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Son problème est qu\'elle est toujours en retard'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Son problème est qu\'elle n\'est pas ponctuelle'
                    ],
                ]),
            ],
            [
                'numero' => 2,
                'intitule_text' => 'Peux-tu poser une question par rapport à la tortue ou à la panthère dans l\'histoire racontée dans la vidéo ?', // [cite: 2]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Pour se poser les bonnes questions, il faut se demander si on peut avoir quelques idées correctes aux questions qu\'on choisit de se poser.', // [cite: 2]
                'reponses' => json_encode([
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Pourquoi la tortue ne se réveille pas vite ?'
                    ],
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Pourquoi la maison de la tortue est si loin qu\'elle n\'arrive à vite?'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Qu\'est-ce que fait la tortue pour ne pas arriver à l\'heure ?'
                    ],
                ]),
            ],
            [
                'numero' => 3,
                'intitule_text' => 'Qu\'est-ce qu\'être en retard ?', // [cite: 2]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Regarde sur l\'image.', // [cite: 2]
                'reponses' => json_encode([
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Être en retard c\'est lorsque tu viens après l\'heure'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Être en retard c\'est lorsque tu ne respectes pas l\'heure'
                    ],
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Être en retard c\'est lorsque tu viens avant l\'heure'
                    ],
                ]),
            ],
            [
                'numero' => 4,
                'intitule_text' => 'Qu\'est-ce qu\'être ponctuel ?', // [cite: 2]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Regarde sur l\'image.', // [cite: 2]
                'reponses' => json_encode([
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Être en retard c\'est lorsque tu viens après l\'heure'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Être en retard c\'est lorsque tu respectes l\'heure'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Être en retard c\'est lorsque tu viens avant l\'heure'
                    ],
                ]),
            ],
            [
                'numero' => 5,
                'intitule_text' => 'A-t-on le droit d\'être en retard ? Et pourquoi ?', // [cite: 2]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Regarde sur l\'image.', // [cite: 2]
                'reponses' => json_encode([
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Non on n\'a pas le droit, parce qu\'arriver en retard comme la tortue, ce n\'est pas être respectueux de l\'autre'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Non on n\'a pas le droit, parce que ça veut dire que tu ne le méprises pas'
                    ],
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Oui on a le droit, parce qu\'on peut faire tout ce qu\'on veut'
                    ],
                ]),
            ],
            [
                'numero' => 6,
                'intitule_text' => 'Mais est-ce qu\'il y a une situation où on a le droit de venir en retard ou de ne pas venir du tout?', // [cite: 3]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Regarde sur l\'image.', // [cite: 3]
                'reponses' => json_encode([
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Oui monsieur. On peut être malade'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Oui monsieur. On peut aussi avoir un accident'
                    ],
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Non monsieur. Pas d\'excuses.'
                    ],
                ]),
            ],
            [
                'numero' => 7,
                'intitule_text' => 'Finalement a-t-on vraiment le droit d\'être en retard ?', // [cite: 3]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Regarde sur l\'image.', // [cite: 3]
                'reponses' => json_encode([
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Oui. On peut venir en retard sans se soucier des autres'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Non. On n\'a pas le droit de venir en retard car c\'est ne pas respecter les autres.'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Non. On n\'a pas le droit d\'arriver en retard parce que ceux qui nous invitent, attendent qu\'on arrive à l\'heure.'
                    ],
                ]),
            ],
            [
                'numero' => 8,
                'intitule_text' => 'Montre dans ton dessin la différence entre quelqu\'un qui est à l\'heure et quelqu\'un qui est en retard?', // [cite: 3]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Regarde sur l\'image.', // [cite: 3]
                'reponses' => json_encode([
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Utilise ton doigt sur l\'espace tactile en bas'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Fais des coloris'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Et sauvegarde ton dessin pour le montrer à tes amis et à tes parents'
                    ],
                ]),
            ],
            [
                'numero' => 9,
                'intitule_text' => 'Alors mes amies, dans la vidéo que vous avez regardé, quel peut être encore le problème de notre petite tortue Koulou par rapport au temps ?', // [cite: 3]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Le problème se trouve dans ce que la petite tortue n\'arrive pas à faire.', // [cite: 3]
                'reponses' => json_encode([
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Son problème est qu\'elle est toujours en retard'
                    ],
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Son problème c\'est parce qu\'elle marche lentement'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Son problème est qu\'elle n\'est gère pas bien son temps pour être ponctuelle'
                    ],
                ]),
            ],
            [
                'numero' => 10,
                'intitule_text' => 'Peux-tu poser une question par rapport à la tortue ou à la panthère dans l\'histoire racontée dans la vidéo ?', // [cite: 4]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Pour se poser les bonnes questions, il faut se demander si on peut avoir quelques idées correctes aux questions qu\'on choisit de se poser.', // [cite: 4]
                'reponses' => json_encode([
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Pourquoi la maison de la tortue est si loin qu\'elle n\'arrive à vite?'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Pourquoi la tortue ne se réveille pas vite ?'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Qu\'est-ce que fait la tortue pour ne pas arriver à l\'heure ?'
                    ],
                ]),
            ],
            [
                'numero' => 11,
                'intitule_text' => 'Pour toi, qu\'est-ce que bien gérer son temps ?', // [cite: 4]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Regarde sur l\'image.', // [cite: 4]
                'reponses' => json_encode([
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'C\'est organiser son temps de manière à ne pas être en retard'
                    ],
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'C\'est faire tout ce qu\'on veut de notre temps à n\'importe quel moment'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'C\'est s\'adapter au temps qu\'il fait de manière à ne pas subir le temps'
                    ],
                ]),
            ],
            [
                'numero' => 12,
                'intitule_text' => 'Tu sais ce que signifie organiser son temps? Peux-tu me dire avec des exemples ?', // [cite: 4]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Regarde sur l\'image.', // [cite: 4]
                'reponses' => json_encode([
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'C\'est ranger son temps comme on range sa chambre'
                    ],
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'C\'est faire les choses vites sans préparation comme un fou'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'C\'est partager son temps en plusieurs tranches comme on coupe un gâteau'
                    ],
                ]),
            ],
            [
                'numero' => 13,
                'intitule_text' => 'Tu peux aussi me dire avec des exemples ce que signifie s\'adapter à son temps ?', // [cite: 5]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Regarde sur l\'image.', // [cite: 5]
                'reponses' => json_encode([
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'C\'est ne pas voir le temps qu\'il fait comme vouloir sortir de la maison dans la pluie sans parapluie'
                    ],
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'C\'est faire avec le temps qu\'on nous donne comme pendant la composition'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'C\'est se mettre au rythme ou à la vitesse du temps comme un tam-tam'
                    ],
                ]),
            ],
            [
                'numero' => 14,
                'intitule_text' => 'Et ça veut dire quoi subir le temps ?', // [cite: 5]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Regarde sur l\'image.', // [cite: 5]
                'reponses' => json_encode([
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'C\'est lorsque tu dois attendre longtemps, même si c\'est long comme quand tu es en salle t\'attende à l\'hôpital'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'C\'est lorsque le temps continue a passé, même quand tu voudrais qu\'il s\'arrête comme pendant que tu dors durant la nuit'
                    ],
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'C\'est quand on est libre de décider à la place du temps comme on décide de manger ou non'
                    ],
                ]),
            ],
            [
                'numero' => 15,
                'intitule_text' => 'Peux-tu me dire alors ce que tu penses du temps: est-il le même pour tout le monde ?', // [cite: 5, 6]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Regarde sur l\'image.', // [cite: 5]
                'reponses' => json_encode([
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Oui, le temps est le même pour tous parce que 30 min à l\'école égales 30 min à la maison.'
                    ],
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Oui le temps est le même pace que le jour se lève et se couche au même moment partout'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Non, les 30 min à l\'école durent plus que les 30 min à la maison, donc le temps n\'est pas la même chose pour tout le monde'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Non, le temps n\'est pas le même, car chaque chose à son temps, il y a les temps de saison pluvieuse et les temps de saison sèche'
                    ],
                ]),
            ],
            [
                'numero' => 16,
                'intitule_text' => 'Alors mon ami, comment faut-il faire vraiment pour bien gérer son temps afin de ne plus venir en retard ?', // [cite: 6]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Regarde sur l\'image.', // [cite: 6]
                'reponses' => json_encode([
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Il faut ranger son temps, c\'est-à-dire régler son réveil pour se lever tôt le matin'
                    ],
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Il faut marcher lentement comme une tortue sur le chemin et s\'arrêter pour jouer'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'C\'est partager son temps en plusieurs activités pour faire chaque chose à son temps et sans traîner'
                    ],
                ]),
            ],
            [
                'numero' => 17,
                'intitule_text' => 'Qui peut dessiner un ami qui gère bien son temps et n\'arrive jamais en retard à l\'école ?', // [cite: 6]
                'indice' => 'Alors les amis! Regarde sur l\'image.', // [cite: 6]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'reponses' => json_encode([
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Utilise ton doigt sur l\'espace tactile en bas'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Fais des coloris'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Et sauvegarde ton dessin pour le montrer à tes amis et à tes parents'
                    ],
                ]),
            ],
        ];

        foreach ($questions_partie1 as $question) {
            DB::table('questions')->insert(array_merge($question, [
                'partie_id' => $partie1_id,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // 5. Insertion des Questions pour la Partie 2 (Fari) - Numérotation de 1 à 8
        // Table: questions
        $questions_partie2 = [
            [
                'numero' => 1,
                'intitule_text' => 'Alors mes amies. dans la vidéo que vous venez regarder, quel est le problème de notre petit Fari l\'âne par rapport au cheval?', // // [cite: 7]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Le problème se trouve dans ce que la petit âne refuse de faire ou veut faire absolument.', // // [cite: 7]
                'reponses' => json_encode([
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Son problème est qu\'il refuse d\'obéir'
                    ],
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Son problème c\'est que son maître lui tape sur les fesses tout le temps'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Son problème est qu\'il veut être libre et faire tout ce qu\'il veut'
                    ],
                ]),
            ],
            [
                'numero' => 2,
                'intitule_text' => 'Peux-tu poser une question par rapport à l\'âne ou au cheval dans l\'histoire racontée dans la vidéo ?', // [cite: 8]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Pour se poser les bonnes questions, il faut se demander si la question choisi nous fait réfléchir, si elle n\'est pas trop facile pour trouver des idées.', // [cite: 8]
                'reponses' => json_encode([
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Pourquoi l\'âne est court?'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Comment être obéissant ?'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Qu\'est-ce qu\'être obéissant ?'
                    ],
                ]),
            ],
            [
                'numero' => 3,
                'intitule_text' => 'Qu\'est-ce qu\'être obéissant ?', // [cite: 8]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Regarde sur l\'image.', // [cite: 8]
                'reponses' => json_encode([
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Être obéissant, c\'est partager quelque chose'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Etre obéissant, c\'est respecter les règles ou les lois'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Obéir, ça veut dire que tu dois faire ce que tes aînés de disent'
                    ],
                ]),
            ],
            [
                'numero' => 4,
                'intitule_text' => 'Qu\'est-ce qu\'être désobéissant ?', // [cite: 8]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Regarde sur l\'image.', // [cite: 8]
                'reponses' => json_encode([
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Être désobéissant, c\'est refuser de faire ce que tes aînés de disent'
                    ],
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Être désobéissant, c\'est partager quelque chose'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Etre désobéissant, c\'est ne pas respecter les règles ou les lois'
                    ],
                ]),
            ],
            [
                'numero' => 5,
                'intitule_text' => 'Toute les règles sont-elles justes ?', // [cite: 8]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Regarde sur l\'image.', // [cite: 8]
                'reponses' => json_encode([
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Non, toutes les lois ne sont pas juste, par exemple dire qu\'on doit: « traverser la route au feu vert » est une mauvaise loi.'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Non, toutes les règles ne sont pas bonnes, par exemple dire que : « seuls certains enfants ont le droit de jouer » est une règle injuste.'
                    ],
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Oui, toutes les règles sont bonnes et il faut les obéir'
                    ],
                ]),
            ],
            [
                'numero' => 6,
                'intitule_text' => 'Si toutes les règles ou lois ne sont pas bonnes, a-t-on le droit de désobéir aux mauvaises règles ?', // [cite: 9]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Regarde sur l\'image.', // [cite: 9]
                'reponses' => json_encode([
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Oui, on doit désobéir une règle qu\'on juge mauvaise car si on l\'obéit on se met en danger'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Oui, on doit désobéir une loi qu\'on juge injuste car si on l\'obéit on peut faire du mal à l\'autre'
                    ],
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Non, la loi c\'est la loi, il faut l\'obéir'
                    ],
                ]),
            ],
            [
                'numero' => 7,
                'intitule_text' => 'Finalement comment être vraiment obéissant ?', // [cite: 9]
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Regarde sur l\'image.', // [cite: 9]
                'reponses' => json_encode([
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Il faut d\'abord bien juger ce qu\'on nous demande de faire si c\'est juste ou injustes, bien ou mauvais'
                    ],
                    [
                        'isCorrect' => false,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Il faut obéir les règles/lois sans réfléchir car ce sont les adultes qui les ont créées'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Il faut d\'abord bien juger ce qu\'on nous demande de faire si c\'est juste ou injustes, bien ou mauvais'
                    ],
                ]),
            ],
            [
                'numero' => 8,
                'intitule_text' => 'Montre dans ton dessin la différence entre Fari et le cheval?', // Question d'activité reconstituée
                'thematique_id' => $thematique_id,
                'degre_difficulte' => '1',
                'type_reponse' => 'multiple',
                'indice' => 'Alors les amis! Regarde sur l\'image.',
                'reponses' => json_encode([
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Utilise ton doigt sur l\'espace tactile en bas'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Fais des coloris'
                    ],
                    [
                        'isCorrect' => true,
                        'contentType' => 'text',
                        'media_url' => null,
                        'media_description' => null,
                        'text' => 'Et sauvegarde ton dessin pour le montrer à tes amis et à tes parents'
                    ],
                ]),
            ],
        ];

        foreach ($questions_partie2 as $question) {
            DB::table('questions')->insert(array_merge($question, [
                'partie_id' => $partie2_id,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

    }
}
