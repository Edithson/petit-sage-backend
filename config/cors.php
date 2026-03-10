<?php

return [
    /*
    | Les chemins de ton API et la route Sanctum essentielle pour le CSRF
    */
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    /*
    | ATTENTION : Ne mets JAMAIS '*' ici. 
    | Mets l'URL exacte de ton app React (incluant le port, sans slash à la fin).
    */
    'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:5173'), // 5173 est le port par défaut de Vite
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    /*
    | CRUCIAL : C'est ce paramètre qui autorise le navigateur 
    | à attacher le cookie de session à la requête.
    */
    'supports_credentials' => true,
];