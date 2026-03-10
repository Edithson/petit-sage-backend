<x-mail::message>
# Bonjour,

Merci de vous être inscrit sur {{ config('app.name') }}. 
Pour valider votre adresse email, veuillez utiliser le code ci-dessous :

<x-mail::panel>
**{{ $code }}**
</x-mail::panel>

Ce code expirera dans 15 minutes. Si vous n'avez pas demandé ce code, vous pouvez ignorer cet email.

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>