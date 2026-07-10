<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ResponseHelper;
use App\Mail\AdminContactNotification;
use App\Models\Contact;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Log;

class ContactController extends Controller
{
    public function index()
    {
        try {
            $contacts = Contact::orderBy('created_at', 'desc')->get();
            return ResponseHelper::successResponse(
                $contacts,
                'Contacts retrieved successfully',
                ['count' => $contacts->count()]
            );
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse('Erreur lors de la récupération des contacts.' . $e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
        
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'email' => 'required|email|max:255',
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            // détection des erreurs de validation
            if ($validator->fails()) {
                return ResponseHelper::errorResponse('Erreurs de validation : ' . $validator->errors()->first(), 422, $validator->errors()->toArray());
            }

            $contact = Contact::create($validator->validated());

            // Envoyer l'email à l'admin
            // Récupération de l'email de l'admin depuis les paramètres, avec fallback sur la config
            $setting = Setting::first();
            $adminEmail = $setting && $setting->email ? $setting->email : config('mail.admin_email');

            // Envoi de l'email via la Queue (File d'attente)
            if ($adminEmail) {
                try {
                    Mail::to($adminEmail)->queue(new AdminContactNotification($contact));
                } catch (\Exception $e) {
                    Log::error('Erreur lors de l\'envoi de l\'email de contact à l\'admin : ' . $e->getMessage());
                }
            }

            return ResponseHelper::successResponse(
                $contact,
                'Contact created successfully',
                ['count' => 1],
                201
            );
        } catch (\Throwable $th) {
            return ResponseHelper::errorResponse('Erreur lors de la création du contact.' . $th->getMessage(), 500);
        }
    }

    public function show(Contact $contact)
    {
        try {
            $contact->markAsRead();
            return ResponseHelper::successResponse(
                $contact,
                'Affichage d\'une demande de contact',
                ['count' => 1]
            );
        } catch (\Throwable $th) {
            return ResponseHelper::errorResponse('Erreur lors de l\'affichage du contact.' . $th->getMessage(), 500);
        }
    }

    public function destroy(Contact $contact)
    {
        try {
            $contact->delete();
            return ResponseHelper::successResponse(
                null,
                'Contact supprimé avec succès',
                ['count' => 0],
                204
            );
        } catch (\Throwable $th) {
            return ResponseHelper::errorResponse('Erreur lors de la suppression du contact.' . $th->getMessage(), 500);
        }
    }

    public function unreadCount()
    {
        try {
            $count = Contact::unread()->count();
            return ResponseHelper::successResponse(
                ['count' => $count],
                'Compteur des contacts non lus récupéré avec succès',
                ['count' => 1]
            );
        } catch (\Throwable $th) {
            return ResponseHelper::errorResponse('Erreur lors de la récupération du compteur des contacts non lus.' . $th->getMessage(), 500);
        }
    }
}
