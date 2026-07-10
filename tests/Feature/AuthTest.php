<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Type;
use App\Models\PasswordResetToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer les types d'utilisateurs requis pour l'inscription
        Type::create(['id' => 1, 'name' => 'Apprenti', 'description' => 'Apprenti']);
        Type::create(['id' => 2, 'name' => 'Parent', 'description' => 'Parent']);
        Type::create(['id' => 3, 'name' => 'Admin', 'description' => 'Admin']);
    }

    /**
     * Test de la première étape de l'inscription (Génération & envoi de code).
     */
    public function test_user_can_request_registration_and_receive_verification_code(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/register', [
            'name' => 'Jean Apprenti',
            'email' => 'jean@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'telephone' => '0102030405',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'email',
                'name',
                'password',
                'telephone',
            ])
            ->assertJsonFragment([
                'email' => 'jean@example.com',
                'name' => 'Jean Apprenti',
            ]);

        // Vérifier que le code a été stocké en base de données
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'jean@example.com',
        ]);

        // Vérifier qu'un email de vérification a bien été envoyé
        Mail::assertQueued(VerificationCodeMail::class, function ($mail) {
            return $mail->hasTo('jean@example.com');
        });
    }

    /**
     * Test de la validation d'inscription avec des champs manquants.
     */
    public function test_registration_validation_fails_on_missing_fields(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => '123',
            'password_confirmation' => '456',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    /**
     * Test de la deuxième étape de l'inscription (Vérification du code et création).
     */
    public function test_user_can_verify_email_and_complete_registration(): void
    {
        // 1. Générer le token de vérification manuellement en base
        $verificationCode = '123456';
        PasswordResetToken::create([
            'email' => 'jean@example.com',
            'token' => $verificationCode,
            'created_at' => now(),
        ]);

        // 2. Simuler l'appel de vérification
        $response = $this->postJson('/api/verify-email', [
            'email' => 'jean@example.com',
            'verification_code' => $verificationCode,
            'name' => 'Jean Apprenti',
            'password' => Hash::make('password123'), // Le mot de passe haché passé par l'étape 1
            'telephone' => '0102030405',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'type_id',
                ]
            ]);

        // 3. Vérifier que l'utilisateur existe dans la base de données
        $this->assertDatabaseHas('users', [
            'email' => 'jean@example.com',
            'name' => 'Jean Apprenti',
            'type_id' => 1, // Devrait être Apprenti par défaut
        ]);

        // 4. Vérifier que le token temporaire a été détruit
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'jean@example.com',
        ]);
    }

    /**
     * Test de l'inscription avec un code de vérification incorrect ou expiré.
     */
    public function test_registration_fails_with_invalid_code(): void
    {
        PasswordResetToken::create([
            'email' => 'jean@example.com',
            'token' => '123456',
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/verify-email', [
            'email' => 'jean@example.com',
            'verification_code' => '999999', // Mauvais code
            'name' => 'Jean Apprenti',
            'password' => Hash::make('password123'),
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Code de vérification invalide ou expiré.'
            ]);
    }

    /**
     * Test de la connexion (Login).
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        // Créer un utilisateur de test en base
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'type_id' => 1,
            'code' => 'TESTCODE',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'type_id',
                ]
            ]);

        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test de la connexion avec identifiants incorrects.
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'type_id' => 1,
            'code' => 'TESTCODE',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Identifiants incorrects.'
            ]);

        $this->assertGuest();
    }
}
