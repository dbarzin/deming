<?php
return [
    'welcome' => [
        'dashboard' => 'Tableau de bord',
        'domains' => 'Domaines',
        'measures' => 'Mesures de sécurité',
        'controls' => 'Controls',
        'action_plans' => "Plans d'action",
        'next_controls' => 'Contrôles plannifiés les 30 prochains jours',
        'control_status' => 'Etat des contrôles',
        'control_planning' => 'Planning des contrôles',
    ],
    'action' => [
        'index' => 'Liste des plans d\'action',
        'show' => 'Plan d\'action',
        'fields' => [
            'clause' => 'Clause',
            'name' => 'Nom',
            'action' => 'Plan d\'action',
            'plan_date' => 'Date de plannification',
            'next_date' => 'Date de revue',
            'note' => 'Score',
            'objective' => 'Objectif',
            'observation' => 'Observation',
            'action_plan' => 'Plan d\'actions'
        ],
    ],
    'attribute' => [
       'fields' => [
            'name' => 'Nom',
            'values' => 'Valeurs',
        ],
        'add' => 'Ajouter un attribut',
        'edit' => 'Modifier un attribut',
        'show' => 'Attribut',
        'index' => 'Liste des attributs',
        'choose' => 'Choisir un attribut',
        'title' => 'Attribut'
     ],
    'control' => [
        'description' => '',
        'fields' => [
            'action_plan' => 'Plan d\'actions',
            'attributes' => 'Attributs',
            'input' => 'Données',
            'choose_domain' => 'Choisir un domaine',
            'choose_period' => 'Choisir une periode',
            'choose_attribute' => 'Choisir un attribut',
            'clause' => 'Clause',
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
            'evidence' => 'Preuves',
            'score' => 'Score',
            'status' => 'Etat',
            'status_done' => 'Fait',
            'status_todo' => 'A faire',
            'status_all' => 'Tous',
            'owners' => 'Responsables',
        ],
        'checklist' => 'Fiche de contrôle',
        'list' => 'Liste des contrôles',
        'edit' => 'Modifier un contrôle de sécurité',
        'history' => 'Planning',
        'make' => 'Réaliser un contrôle',
        'plan' => 'Plannifier un contrôle',
        'radar' => 'Etat des contrôles de sécurité',
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
            'input' => 'Données',
            'attributes' => 'Attributs',
            'model' => 'Modèle',
            'indicator' => 'Indicateur',
            'action_plan' => 'Plan d\'action',
            'periodicity' => 'Périodicité',
        ],
        'show' => 'Mesure de sécurité',
        'index' => 'Liste des mesures de sécurité',
        'create' => 'Ajouter une mesure de sécurité',
        'edit' => 'Modifier une mesure de sécurité',
        'plan' => 'Plannifier un contrôle'
    ],
    'domain' => [
        'fields' => [
            'name' => 'Nom',
            'description' => 'Description',
            'measures' => '# Mesures',
        ],
        'add' => 'Ajouter un domaine',
        'edit' => 'Modifier un domaine',
        'show' => 'Domaine',
        'index' => 'Liste des domaines',
        'choose' => 'Choisir un domaine',
        'title' => 'Domaine'
    ],
    'document' => [
        'title' => [
            'storage' => 'Entrepôt de documents',
            'templates' => 'Modèles de documents',
        ],
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
            'control' => 'Modèle de fiche de contrôle',
            'report' => 'Modèle du rapport de pilotage',

        ],
        'count' => 'Nombre de documents',
        'total_size' => 'Taille totale',
    ],
    'exports' => [
        'index' => 'Exporter des données',
        'start' => 'Début',
        'end' => 'Fin',
        'report_title' => 'Rapport',
        'steering' => 'Rapport de pilotage du SMSI',
        'data_export_title' => 'Exportation des données',
        'domains_export'=> 'Exportation des domaines',
        'measures_export' => 'Exportation des mesures de sécurité',
        'controls_export' => 'Exportation des contrôles',
    ],
    'imports' => [
         'index' => 'Importer',
         'title' => 'Importer des mesures de sécurité',
    ],
    'login' => [
        'title' => 'Entrez un mot de passe',
        'identification' => 'Identification',
        'connection' => 'Connexion',
    ],
    'report' => [
        'action_plan' => [
            'id' => '#',
            'title' => 'Plan d\'action',
            'next' => 'Date de revue'
        ]
    ],
    'user' => [
        'show' => 'Afficher un utilisateur',
        'index' => 'Liste des utilisateurs',
        'edit' => 'Modifier un utilisateur',
        'add' => 'Ajouter un utilisateur',
        'fields' => [
            'login' => 'Login',
            'name' => 'Nom',
            'title' => 'Titre',
            'role' => 'Role',
            'password' => 'Password',
            'email' => 'eMail',
            'language' => 'Langue',
            'controls' => 'Contrôles de l\'utilisateur'
        ],
        'roles' => [
            'admin' => 'Administrateur',
            'user' => 'Utilisateur',
            'auditor' => 'Auditeur'
        ],
    ],
    'config' => [
        'notifications' => [
            'title' => 'Configuration de l\'envoi de notifications',
            'title_short' => 'Notifications',
            'help' => 'Cet écran permet de configurer les notifications envoyées par mail aux utilisateurs.',
            'message_subject' => 'Sujet du message',
            'sent_from' => 'Envoyé depuis',
            'to' => 'à',
            'recurence' => 'envoyer un rappel tout les'
            'recurence_day' => 'jour',
            'recurence_week' => 'semaine',
            'recurence_week' => 'deux semaines',
            'recurence_week' => 'mois',
            'delay' => 'pour les contrôles qui arrivent à échéance dans',
            'duration' => [
                'day' => 'jour',
                'days' => 'jours',
                'month' => 'mois',
                'months' => 'mois',
            ],
         ]
    ]
];
