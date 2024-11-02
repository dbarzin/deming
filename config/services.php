<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'socialite_controller' => [
        'providers' => ! empty(env('SOCIALITE_PROVIDERS', "")) 
                             ? explode(' ', env('SOCIALITE_PROVIDERS', ""))
                             : [],
        'keycloak' => [
            'display_name' => env('KEYCLAOK_DISPLAY_NAME', 'Keycloak'),
            'allow_create_user' => env("KEYCLOAK_ALLOW_CREATE_USER", false),
            'allow_update_user' => env("KEYCLOAK_ALLOW_UPDATE_USER", false),
            // Set to null if you want role to be set explicitily
            'default_role' => env('KEYCLOAK_DEFAULT_ROLE', 'auditee'),
            'role_claim' => env('KEYCLOAK_ROLE_CLAIM', ''),
            'additional_scopes' => explode(' ', env('KEYCLOAK_ADDITIONAL_SCOPES', "")),
        ],
    ],
    
    'keycloak' => [
        'client_id' => env('KEYCLOAK_CLIENT_ID'),
        'client_secret' => env('KEYCLOAK_CLIENT_SECRET'),
        'redirect' => env('KEYCLOAK_REDIRECT_URI'),
        'base_url' => env('KEYCLOAK_BASE_URL'),   // Specify your keycloak server URL here
        'realms' => env('KEYCLOAK_REALM'),        // Specify your keycloak realm
    ],
];
