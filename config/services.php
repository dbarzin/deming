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
        'oidc' => [
            'display_name' => env('OIDC_DISPLAY_NAME', 'Generic OIDC'),
            'allow_create_user' => env("OIDC_ALLOW_CREATE_USER", false),
            'allow_update_user' => env("OIDC_ALLOW_UPDATE_USER", false),
            // Set to null if you want role to be set explicitily
            'default_role' => env('OIDC_DEFAULT_ROLE', 'auditee'),
            'role_claim' => env('OIDC_ROLE_CLAIM', 'role'),
            'additional_scopes' => explode(' ', env('OIDC_ADDITIONAL_SCOPES', "")),
        ],
    ],
    
    'keycloak' => [
        'client_id' => env('KEYCLOAK_CLIENT_ID'),
        'client_secret' => env('KEYCLOAK_CLIENT_SECRET'),
        'redirect' => env('KEYCLOAK_REDIRECT_URI'),
        'base_url' => env('KEYCLOAK_BASE_URL'),   // Specify your keycloak server URL here
        'realms' => env('KEYCLOAK_REALM'),        // Specify your keycloak realm
    ],

    'oidc' => [    
        'client_id' => env('OIDC_CLIENT_ID'),  
        'client_secret' => env('OIDC_CLIENT_SECRET'),  
        'host' => env('OIDC_BASE_URL'),
        'redirect' => env('OIDC_REDIRECT_URI', rtrim(env('APP_URL'), '/').'/auth/callback/oidc'),
        'authorize_endpoint' => env('OIDC_AUTHORIZE_ENDPOINT', null),
        'token_endpoint' => env('OIDC_TOKEN_ENDPOINT', null),
        'userinfo_endpoint' => env('OIDC_USERINFO_ENDPOINT', null),
        'map_user_attr' => [
            'id' => 'sub',
            'name' => 'name',
            'locale' => 'locale',
            'email' => 'email'
        ],
    ],
];
