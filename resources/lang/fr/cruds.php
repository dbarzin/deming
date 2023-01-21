<?php
return [
    'login' => [
        'title' => 'Entrez un mot de passe',
        'identification' => 'Identification',
        'connection' => 'Connexion',
    ],
    'control' => [
        'description' => '',
        'fields' => [
            'action_plan' => 'Plan d\'actions',
            'attributes' => 'Attributs',
            'choose_domain' => 'Choisir un domaine',
            'choose_period' => 'Choisir une periode',
            'domain' => 'Domaine',
            'indicator' => 'Fonction',
            'measure' => 'Mesure',
            'model' => 'Modèle',
            'name' => 'Nom',
            'next' => 'Suivant',
            'note' => 'Note',
            'objective' => 'Objectif',
            'observations' => 'Observations',
            'plan_date' => 'Date de plannification',
            'period' => 'Période',
            'periodicity' => 'Périodicité',
            'planned' => 'Planifié',
            'realisation_date' => 'Date de réalisation',
            'realized' => 'Réalisé',
            'report' => 'Rapport',
            'score' => 'Score',
            'status' => 'Etat',
            'status_done' => 'Fait',
            'status_todo' => 'A faire',
            'status_all' => 'Tous',
        ],
        'list' => 'Liste des contrôles',
        'edit' => 'Modifier un contrôle de sécurité',
        'history' => 'Planning',
        'make' => 'Réaliser un contrôle',
        'title' => 'Contrôles',
        'title_singular' => 'Contrôle',
    ],
    'measure' => [
        'title' => 'Mesure',
        'fields' => [
            'domain' => 'Domaine',
            'clause' => 'Clause',
            'name' => 'Nom',
            'objective' => 'Objectif',
            'attributes' => 'Attributes',
            'model' => 'Modèle',
            'indicator' => 'Indicateur (Rouge, Orange, Vert)',
            'cation_plan' => 'Plan d\'action',
            'responsible' => 'Responsable',
            'periodicity' => 'Périodicité'
        ],
        'index' => 'Liste des mesures',

    ],
    'domain' => [
        'fields' => [
            'name' => 'Nom',
            'description' => 'Description',
        ],
        'add' => 'Ajouter un domaine',
        'edit' => 'Modifier un domaine',
        'show' => 'Domaine',
        'index' => 'Liste des domaines',
        'choose' => 'Choisir un domaine',
        'title' => 'Domaine'
    ],
    'document' => [
        'title' => 'Document',
        'description' => 'Description',
        'list' => 'Liste des documents',
        'index' => 'Documents',
        'fields' => [
            'name' => 'Nom',
            'control' => 'Contrôle',
            'size' => 'Taille',
            'hash' => 'Hash',
        ],
        'model' => [
            'control' => 'Modèle de Contrôle',
            'report' => 'Rapport de pilotage',

        ],
        'count' => 'Nombre de documents',
        'total_size' => 'Taille totale',
    ],
    'user' => [
        'index' => 'Liste des utilisateurs',
        'edit' => 'Modifier un utilisateur',
        'add' => 'Ajouter un utilisateur',
        'fields' => [
            'login' => 'Login',
            'name' => 'Nom',
            'title' => 'Titre',
            'role' => 'Role',
            'password' => 'Password',
            'email' => 'eMail'
        ],
        'roles' => [
            'admin' => 'Administrateur',
            'user' => 'Utilisateur',
            'auditor' => 'Auditeur'
        ],
    ],
    'exports' => [
        'index' => 'Exporter des données',
    ]
];
