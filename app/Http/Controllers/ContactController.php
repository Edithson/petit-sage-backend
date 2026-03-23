<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Setting;
use App\Mail\AdminContactNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::latest()->get();
        return response()->json($contacts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = Contact::create($validated);

        // Envoyer l'email à l'admin
        // Récupération de l'email de l'admin depuis les paramètres
        $setting = Setting::first();
        $adminEmail = $setting && $setting->email ? $setting->email : 'pierrebeny63@gmail.com'; // Fallback de sécurité

        // Envoi de l'email via la Queue (File d'attente)
        if ($adminEmail) {
            Mail::to($adminEmail)->queue(new AdminContactNotification($contact));
        }

        return response()->json($contact, 201);
    }

    public function show(Contact $contact)
    {
        $contact->markAsRead();
        return response()->json($contact);
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return response()->json(null, 204);
    }

    public function unreadCount()
    {
        $count = Contact::unread()->count();
        return response()->json(['count' => $count]);
    }
}
