<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Groq API Configuration
    |--------------------------------------------------------------------------
    | Configuration pour l'intégration Groq (LLM - llama-3.3-70b-versatile)
    |--------------------------------------------------------------------------
    */

    'api_key'  => env('GROQ_API_KEY'),
    'base_url' => 'https://api.groq.com/openai/v1',
    'model'    => 'llama-3.3-70b-versatile',

    /*
    |--------------------------------------------------------------------------
    | Paramètres du modèle
    |--------------------------------------------------------------------------
    */
    'temperature'  => 0.3,
    'max_tokens'   => 1024,
    'stream'       => true,

    /*
    |--------------------------------------------------------------------------
    | Limites de taux (rate limiting)
    | requests_per_minute : nb de requêtes autorisées par minute par IP
    |--------------------------------------------------------------------------
    */
    'rate_limit' => [
        'requests_per_minute' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    | TTL en secondes (30 minutes = 1800)
    | Utilise Redis si disponible, sinon database
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => true,
        'ttl'     => 1800, // 30 minutes
        'prefix'  => 'ai_chat_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'enabled'    => true,
        'channel'    => 'daily', // canal de log Laravel (fichiers journaliers uniquement)
        'db_enabled' => false,   // Désactivé : la table se remplirait trop vite avec des milliers d'utilisateurs
    ],

    /*
    |--------------------------------------------------------------------------
    | Prompt système (system prompt)
    | Définit le comportement et le contexte de l'assistant IA
    |--------------------------------------------------------------------------
    */
    'system_prompt' => <<<'PROMPT'
Tu es l'assistant virtuel officiel de TOPIDEALSPACE, une marketplace e-commerce béninoise.
Ton rôle est d'aider les utilisateurs à naviguer sur la plateforme, résoudre leurs problèmes,
comprendre les fonctionnalités et répondre à leurs questions.

Règles à respecter absolument :
1. Réponds TOUJOURS en français (sauf si l'utilisateur écrit dans une autre langue, alors adapte-toi).
2. Sois concis, clair et bienveillant.
3. Si tu ne sais pas quelque chose concernant la plateforme, dis-le honnêtement et suggère d'ouvrir un ticket de support.
4. Ne divulgue jamais d'informations sensibles (clés API, mots de passe, données personnelles d'autres utilisateurs).
5. Tu représentes TOPIDEALSPACE : sois professionnel et courtois.
6. Pour guider l'utilisateur vers une page, écris toujours "la page [lien]" ou "rendez-vous sur [lien]" — N'utilise JAMAIS le mot "route" dans tes réponses.
7. Ne réponds qu'aux sujets liés à TOPIDEALSPACE et à l'aide aux utilisateurs.
8. Si l'utilisateur demande quelque chose de hors-sujet, redirige-le poliment vers les fonctionnalités de la plateforme.

Base de connaissances TOPIDEALSPACE :
- Marketplace béninoise (produits, sponsors, blog, tickets, jetons)
- Paiement Mobile Money via FedaPay en XOF (Franc CFA)
- Authentification sécurisée avec OTP email
- Images hébergées sur Cloudinary
- Support via tickets intégrés
PROMPT,
];
