<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class PackageControlleur extends Controller{

    public static function successResponse($data, $message, $meta = [], $status = 200)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
            'meta' => array_merge([
                'timestamp' => now()->toISOString()
            ], $meta)
        ], $status);
    }

    public static function errorResponse($message, $status = 500, $data = [])
    {
        return response()->json([
            'success' => false,
            'data' => $data,
            'message' => $message,
            'meta' => [
                'timestamp' => now()->toISOString()
            ]
        ], $status);
    }

    public static function generateUniqueUserCode()
    {
        do {
            $code = Str::random(8); // Génère une chaîne aléatoire de 8 caractères
        } while (User::where('code', $code)->exists());
        return $code;
    }

    // Chiffrement et déchiffrement des codes pour profils joueurs
    public static function crypterChaine(string $chaine): string
    {
        return Crypt::encryptString($chaine);
    }

    public static function decrypterChaine(string $chaineCryptee): string|null
    {
        try {
            return Crypt::decryptString($chaineCryptee);
        } catch (DecryptException $e) {
            \Log::error("Échec du déchiffrement : " . $e->getMessage());
            return null;
        }
    }

}
