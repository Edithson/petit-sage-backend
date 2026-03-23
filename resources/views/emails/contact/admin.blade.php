<x-mail::message>
# Nouveau message de contact

Un utilisateur a laissé un message sur la plateforme Petit Sage.

**Détails de l'expéditeur :**
- **Nom :** {{ $contact->name ?? 'Non renseigné' }}
- **Email :** [{{ $contact->email }}](mailto:{{ $contact->email }})
- **Sujet :** {{ $contact->subject }}

**Message :**
<x-mail::panel>
{{ $contact->message }}
</x-mail::panel>

<x-mail::button :url="'mailto:' . $contact->email">
Répondre à {{ $contact->name ?? 'cet utilisateur' }}
</x-mail::button>

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>