<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Type;
use App\Models\Question;
use App\Models\Thematique;
use App\Models\Partie;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $normalUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer les types
        Type::create(['id' => 1, 'name' => 'Apprenti', 'description' => 'Apprenti']);
        Type::create(['id' => 2, 'name' => 'Parent', 'description' => 'Parent']);
        Type::create(['id' => 3, 'name' => 'Admin', 'description' => 'Admin']);

        // Créer l'admin
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'type_id' => 3,
            'code' => 'ADMINCODE',
        ]);

        // Créer l'apprenti normal
        $this->normalUser = User::create([
            'name' => 'Normal User',
            'email' => 'normal@example.com',
            'password' => Hash::make('password123'),
            'type_id' => 1,
            'code' => 'NORMALCODE',
        ]);
    }

    /**
     * Test que les modifications sur le modèle Question sont journalisées.
     */
    public function test_question_operations_are_logged_automatically(): void
    {
        // Agir en tant qu'admin
        $this->actingAs($this->adminUser);

        // Créer thématique et partie pour la question
        $theme = Thematique::create(['name' => 'Theme test']);
        $partie = Partie::create(['name' => 'Partie test', 'thematique_id' => $theme->id, 'numero' => 1]);

        // 1. Création
        $question = Question::create([
            'intitule_text' => 'Quelle est la capitale de la France ?',
            'thematique_id' => $theme->id,
            'partie_id' => $partie->id,
            'degre_difficulte' => 1,
            'type_reponse' => 'unique',
            'reponses' => json_encode([]),
            'numero' => 1,
        ]);

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => Question::class,
            'subject_id' => $question->id,
            'description' => 'created',
        ]);

        // 2. Modification
        $question->update(['intitule_text' => 'Quelle est la capitale de l\'Espagne ?']);

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => Question::class,
            'subject_id' => $question->id,
            'description' => 'updated',
        ]);

        // 3. Suppression
        $question->delete();

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => Question::class,
            'subject_id' => $question->id,
            'description' => 'deleted',
        ]);
    }

    /**
     * Test que les événements d'authentification sont journalisés.
     */
    public function test_authentication_events_are_logged(): void
    {
        // 1. Connexion réussie
        event(new Login('web', $this->normalUser, false));

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'auth',
            'description' => 'login',
            'subject_type' => User::class,
            'subject_id' => $this->normalUser->id,
            'causer_id' => $this->normalUser->id,
        ]);

        // 2. Déconnexion
        event(new Logout('web', $this->normalUser));

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'auth',
            'description' => 'logout',
            'subject_type' => User::class,
            'subject_id' => $this->normalUser->id,
            'causer_id' => $this->normalUser->id,
        ]);

        // 3. Échec de connexion
        event(new Failed('web', null, ['email' => 'fake@example.com', 'password' => 'wrong']));

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'auth',
            'description' => 'login_failed',
            'properties->email' => 'fake@example.com',
        ]);
    }

    /**
     * Test de l'accès à l'API d'administration des logs.
     */
    public function test_only_admin_can_access_activity_logs_api(): void
    {
        // Créer un log bidon
        activity('test')->log('dummy_action');

        // 1. Visiteur non connecté -> 401
        $response = $this->getJson('/api/admin/activity-logs');
        $response->assertStatus(401);

        // 2. Utilisateur standard -> 403
        $this->actingAs($this->normalUser);
        $response = $this->getJson('/api/admin/activity-logs');
        $response->assertStatus(403);

        // 3. Administrateur -> 200 avec structure de pagination
        $this->actingAs($this->adminUser);
        $response = $this->getJson('/api/admin/activity-logs');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'current_page',
                    'data',
                    'total',
                ]
            ]);
    }

    /**
     * Test des filtres de recherche de l'API.
     */
    public function test_activity_logs_api_filters(): void
    {
        $this->actingAs($this->adminUser);

        // Créer différents types de logs
        activity('auth')->log('login');
        activity('default')->log('created');

        // Filtrer par log_name = auth
        $response = $this->getJson('/api/admin/activity-logs?log_name=auth');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));
        $this->assertEquals('auth', $response->json('data.data.0.log_name'));

        // Filtrer par action = created (comprend la création manuelle + les 2 créations d'utilisateurs en setUp)
        $response = $this->getJson('/api/admin/activity-logs?action=created');
        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data.data'));
        $this->assertEquals('created', $response->json('data.data.0.description'));
    }

    /**
     * Test que les opérations sur Setting et Profil sont journalisées.
     */
    public function test_setting_and_profil_operations_are_logged(): void
    {
        $this->actingAs($this->adminUser);

        // 1. Modifier Setting
        $setting = \App\Models\Setting::create([
            'title_welcome' => 'Bienvenue',
            'email' => 'admin@example.com',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => \App\Models\Setting::class,
            'subject_id' => $setting->id,
            'description' => 'created',
        ]);

        $setting->update(['title_welcome' => 'Nouveau Bienvenue']);

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => \App\Models\Setting::class,
            'subject_id' => $setting->id,
            'description' => 'updated',
        ]);

        // 2. Créer un Profil
        $profil = \App\Models\Profil::create([
            'name' => 'Enfant Joueur',
            'sexe' => 'Masculin',
            'age' => 8,
            'user_id' => $this->normalUser->id,
            'code' => 'KIDCODE1',
            'password' => Hash::make('123456'),
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => \App\Models\Profil::class,
            'subject_id' => $profil->id,
            'description' => 'created',
        ]);
    }

    /**
     * Test de l'endpoint personnel de récupération des logs (/activity-logs/me).
     */
    public function test_user_can_access_their_own_logs_only(): void
    {
        // 1. Créer un log causé par normalUser et un autre causé par adminUser
        activity('default')->causedBy($this->normalUser)->log('normal_action');
        activity('default')->causedBy($this->adminUser)->log('admin_action');

        // 2. Tenter d'accéder sans être connecté -> 401
        $response = $this->getJson('/api/activity-logs/me');
        $response->assertStatus(401);

        // 3. Connecté en tant que normalUser -> reçoit uniquement son action
        $this->actingAs($this->normalUser);
        $response = $this->getJson('/api/activity-logs/me');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));
        $this->assertEquals('normal_action', $response->json('data.data.0.description'));

        // 4. Connecté en tant que adminUser -> reçoit uniquement son action (ou celles qu'il a causées)
        $this->actingAs($this->adminUser);
        $response = $this->getJson('/api/activity-logs/me');
        $response->assertStatus(200);
        
        $descriptions = collect($response->json('data.data'))->pluck('description');
        $this->assertTrue($descriptions->contains('admin_action'));
        $this->assertFalse($descriptions->contains('normal_action'));
    }
}
