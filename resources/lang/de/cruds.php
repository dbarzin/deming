<?php
return [
    'welcome' => [
         'dashboard' => 'Dashboard',
         'domains' => 'Domänen',
         'measures' => 'Maßnahmen',
         'controls' => 'Kontrollen',
         'action_plans' => "Aktionsplan",
         'next_controls' => 'Zeigt die Planung der nächsten 30 Tage',
         'control_status' => 'Kontrollstatus',
         'control_planning' => 'Maßnahmenplanung',
    ],
     'action' => [
        'index' => 'Aktionspläne',
        'show' => 'Aktionsplan',
        'create' => 'Aktionsplan erzeugen',
        'edit' => 'Aktionsplan ändern',
        'close' => 'Aktionsplan zäune',
        'fields' => [
            'clause' => 'Klausel',
            'name' => 'Name',
            'scope' => 'Geltungsbereich',
            'action' => 'Aktionsplan',
            'plan_date' => 'Planungsdatum',
            'next_date' => 'Reviewdatum',
            'note' => 'Punktzahl',
            'objective' => 'Zielsetzung',
            'observation' => 'Beobachtung',
            'justification' => 'Rechtfertigung',
            'action_plan' => 'Aktionsplan',
            'reference' => 'Referenz',
            'type' => 'Typ',
            'due_date' => 'Fälligkeitsdatum',
            'choose_type' => 'Typ wählen',
            'choose_scope' => 'Geltungsbereich wählen',
            'remediation' => 'Abhilfemaßnahme',
            'criticity' => 'Kritikalität',
            'cause' => 'Ursache',
            'clauses' => 'Klausel',
            'owners' => 'Besitzer',
            'status' => 'Status',
            'status_open' => 'Offen',
            'status_closed' => 'Geschlossen',
            'status_rejected' => 'Abgelehnt',
            'status_all' => 'Alle',
            'close_date' => 'Abschlussdatum',
            'progress' => 'Stand der Dinge',
         ],
        'types' => [
            'major' => 'Major',
            'minor' => 'Minor',
            'opportunity' => 'Gelegenheit',
            'observation' => 'Beobachtung'
        ],
     ],
    'attribute' => [
        'fields' => [
            'name' => 'Name',
            'values' => 'Werte',
        ],
        'add' => 'Attribut hinzufügen',
        'edit' => 'Attribut ändern',
        'show' => 'Attribut',
        'index' => 'Liste der Attribute',
        'choose' => 'Wähle ein Attribut',
        'title' => 'Attribute'
    ],
     'control' => [
         'description' => 'Kontrollen',
         'fields' => [
            'action_plan' => 'Aktionsplan',
            'attributes' => 'Attribute',
            'choose_domain' => 'Wähle Domäne',
            'choose_scope' => 'Wähle Geltungsbereich',
            'choose_period' => 'Wähle Periode',
            'choose_attribute' => 'Wähle Attribute',
            'choose_clause' => 'Wähle Klausel',
            'domain' => 'Domäne',
            'indicator' => 'Funktion',
            'measure' => 'Maßnahme',
            'model' => 'Model',
            'name' => 'Name',
            'next' => 'Nächstes',
            'note' => 'Notiz',
            'objective' => 'Zilelsetzung',
            'observations' => 'Beobachtungen',
            'plan_date' => 'Planungsdatum',
            'period' => 'Periode',
            'periodicity' => 'Wiederholung',
            'planned' => 'Eingeplant',
            'realisation_date' => 'Realisierungsdatum',
            'realized' => 'Realisiert',
            'evidence' => 'Beweis',
            'score' => 'Punktzahl',
            'status' => 'Status',
            'status_done' => 'Erledigt',
            'status_todo' => 'ToDo',
            'status_all' => 'Alle',
            'scope' => 'Geltungsbereich',
            'clause' => 'Klausel',
            'clauses' => 'Klauseln',
            'input' => 'Eingabe-Elemente',
            'owners' => 'Verantwortlich',
         ],
        'error' => [
            'made' => 'Diese Maßnahme wurde bereits umgesetzt.',
            'duplicate' => 'Diese Maßnahme existiert bereits.',
        ],
         'checklist' => 'Maßnahmentabelle',
         'create' => 'Erstelle eine Maßnahme',
         'list' => 'Liste der Maßnahmen',
         'edit' => 'Maßnahme ändern',
         'history' => 'Zeitplan',
         'make' => 'Maßnahme durchführen',
         'plan' => 'Planung einer Maßnahme',
         'radar' => 'Status der Sicherheitsmaßnahmen',
	 'status_date' => 'Stand der Kontrolle am',
         'title' => 'Maßnahmen',
         'title_singular' => 'Maßnahme',
         'groupBy' => 'Gruppiert nach',
         'calendar' => 'Kalender',
       	 'create_action' => 'Aktionsplan erstellen',
         'confirm_delete' => 'Möchten Sie die Kontrollen löschen?'
     ],
     'notification' => [
         'subject' => 'Liste der durchzuführenden Maßnahmen',
     ],
     'measure' => [
         'title' => 'Maßnahmen',
         'fields' => [
             'domain' => 'Domäne',
             'clause' => 'Klausel',
             'name' => 'Name',
             'objective' => 'Beschreibung',
             'attributes' => 'Attribute',
             'model' => 'Modell',
             'indicator' => 'Indikator',
             'action_plan' => 'Aktionsplan',
             'periodicity' => 'Wiederholung',
             'input' => 'Eingabe-Elemente',
         ],
         'show' => 'Maßnahmen',
         'index' => 'Liste der Maßnahmen',
         'create' => 'Erstelle eine Maßnahme',
         'edit' => 'Ändere eine Maßnahme',
         'plan' => 'Maßnahmenplanung'
     ],
     'domain' => [
         'fields' => [
             'framework' => 'Framework',
             'name' => 'Name',
             'description' => 'Beschreibung',
             'measures' => '# Kontrollen',
         ],
         'add' => 'Domäne hinzufügen',
         'edit' => 'Domäne ändern',
         'show' => 'Domäne',
         'index' => 'Liste der Domänen',
         'choose' => 'Wähle eine Domäne',
         'title' => 'Domänen',
         'radar' => 'Ergebnis der Kontrollen nach Domänen',
	 'measure_date' => 'Stand der Maßnahmen zum',
     ],
     'document' => [
        'title' => [
            'storage' => 'Speicher',
            'templates' => 'Vorlagen',
            'cleanup' => 'Aufbewahrungsdauer',
            'cleanup_detail' => 'Anzahl der Monate bis zur automatischen Löschung von Überprüfungen und Dokumenten',
        ],
        'month' => 'Monaten',
        'never' => 'Nie',
        'description' => 'Beschreibung',
        'list' => 'Liste der Dokumente',
        'index' => 'Dokumente',
        'fields' => [
            'name' => 'Name',
            'control' => 'Kontrolle',
            'size' => 'Größe',
            'hash' => 'Hashwert',
            'links' => 'Links',
            'status' => 'Überprüfen',
        ],
        'model' => [
            'control' => 'Vorlage Kontrollblatt',
            'report' => 'Vorlage Lenkungsbericht',
            'custom' => 'Benutzerdefiniertes Model',
        ],
        'count' => 'Anzahl an Dokumenten',
        'total_size' => 'Gesamtgröße',
     ],
    'exports' => [
         'index' => 'Exportiere Daten',
         'start' => 'Start',
         'end' => 'Ende',
         'report_title' => 'Report',
         'steering' => 'ISMS Lenkungsbericht',
         'data_export_title' => 'Datenexport',
         'users_export' => 'Exportiere Benutzern',
         'attributes_export' => 'Exportiere Attributen',
         'domains_export'=> 'Exportiere Domänen',
         'measures_export' => 'Exportiere Sicherheitsmaßnahmen',
         'controls_export' => 'Exportiere Kontrollen',
	 'import' => 'Import',
	 'actions_export' => 'Exportiere Aktionsplan',
     ],
    'group' => [
         'index' => 'Liste der Gruppen',
         'add' => 'Hinzufügen einer Benutzergruppe',
         'edit' => 'Bearbeiten einer Benutzergruppe',
         'show' => 'Anzeigen einer Benutzergruppe',
         'fields' => [
             'name' => 'Name',
             'description' => 'Beschreibung',
             'users' => 'Users',
             'controls' => 'Controls',
             ],
         ],
    'imports' => [
         'index' => 'Import',
	 'title' => 'Importiere Sicherheitsmaßnahmen',
	 'current' => 'Aktuelle Sicherheitsmaßnahme',
	 'or' => 'oder',
	 'remove_all' => 'Lösche alle anderen Maßnahmen und Kontrollen',
         'fake' => 'Erzeuge Beispielmaßnahmen',
         'format' => 'Das Importformat ist ein XLSX-Dokument mit diesen Spalten',
         'framework' => 'Rahmen',
         'framework_helper' => 'Der verwendete Sicherheitsrahmen',
         'domain' => 'Domänen-Name',
         'domain_helper' => 'Der Domänen-Name. Er wird erzeugt, wenn er noch nicht vorhanden ist.',
         'domain_description' => 'Domänen-Beschreibung',
         'domain_description_helper' => 'Die Beschreibung der Domäne',
         'clause' => 'Klausel',
         'clause_helper' => 'Wenn die Klausel existiert, wird die Sicherheitsmaßnahme aktualisiert,<br>wenn die Klausel nicht exisitiert, wird eine neue Sicherheitsmaßnahme erstellt,<br>wenn alle anderen Felder der Zeile leer sind, werden die Maßnahme, die zugehörigen Kontrollen und alle Dokumente entfernt.<br>',
         'name' => 'Name',
         'name_helper' => 'Der Name der Sicherheits-Maßnahme',
         'description' => 'Beschreibung',
         'description_helper' => 'Die Beschreibung der Sicherheits-Maßnahme',
         'attributes' => 'Attribute',
         'attributes_helper' => 'List der Tags (#... #... #...)',
         'input' => 'Eingabe-Elemente',
         'input_helper' => 'Die Eingabe-Elemente',
         'model' => 'Modell',
         'model_helper' => 'Die Prüfungen für die Bewertung des Modells',
         'indicator' => 'Indikator',
         'indicator_helper' => 'Grün, wenn ..., Orange, wenn ..., Rot, wenn...',
         'action' => 'Aktionsplan',
         'action_helper' => 'Der vorgeschlagene Aktionsplan',
         'warning' => 'Diese Aktion kann nicht rückgängig gemacht werden. Machen Sie vorher ein Backup!',
    ],
     'log' => [
        'index' => 'Liste der Logdateien',
        'title' => 'Logdatei',
        'history' => 'Änderungshistorie',
        'action' => 'Aktion',
        'subject_type' => 'Typ',
        'subject_id' => 'ID',
        'user' => 'Benutzer',
        'host' => 'Rechner',
        'timestamp' => 'Zeitstempel',
        'properties' => 'Daten',
    ],
     'login' => [
        'title' => 'Passwort eingeben',
        'identification' => 'Login',
        'connection' => 'Verbindung',
        'connection_with' => 'Verbinden mit',
         'error' => [
            'user_not_exist' => 'Benutzer nicht vorhanden',
        ],
     ],
    'report' => [
        'action_plan' => [
            'id' => '#',
            'title' => 'Aktionsplan',
            'next' => 'Reviewdatum'
        ]
    ],
    'soa' => [
        'title' => 'Erklärung der Anwendbarkeit (SoA)',
        'generate' => 'Generiere Report'
    ],
    'user' => [
         'index' => 'Liste der Benutzer',
         'edit' => 'Ändere Benutzer',
         'add' => 'Neuer Benutzer',
         'show' => 'Zeige Benutzer',
         'fields' => [
             'login' => 'UserId',
             'name' => 'Name',
             'title' => 'Titel',
             'role' => 'Rolle',
             'password' => 'Passwort',
             'email' => 'E-Mail',
             'language' => 'Sprache',
             'controls' => 'Kontrollen'
         ],
         'roles' => [
             'admin' => 'Administrator',
             'user' => 'User',
             'auditor' => 'Auditor',
             'api' => 'API',
             'auditee' => 'Auditierte',
         ],
    ],
    'config' => [
        'notifications' => [
                 'title' => 'Konfiguration der Benachrichtigungen',
                 'title_short' => 'Benachrichtigungen',
                 'help' => 'Hier können Sie die Benachrichtigungen konfigurieren, die die Benutzer per E-Mail erhalten.',
		 'message_subject' => 'Betreff',
		 'message_content' => 'Nachrichteninhalt',
		 'message_default_content' => '<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="ISO-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<p>Hier ist die Liste der Maßnahmen, die bald fällig sind:</p>
%table%
<p>Dies ist eine automatisch generierte Mail.</p>
<p>Mit freundlichen Grüßen,<br>Deming</p>
</body>
</html>',
                 'sent_from' => 'Gesendet von',
                 'to' => 'an',
                 'delay' => 'für Kontrollen die ablaufen in',
                 'recurrence' => 'Sende eine Benachrichtigung',
                 'duration' => [
                     'day' => 'Tag',
                     'days' => 'Tage',
                     'month' => 'Monat',
                     'months' => 'Monate',
                ],
            ],
        ],
    // -------------------------------------------------------------------------
    // Risikoregister
    // -------------------------------------------------------------------------
    'risk' => [

        // Seitentitel
        'list' => 'Risikoliste',
        'create' => 'Neues Risiko',
        'edit' => 'Risiko bearbeiten',
        'matrix' => 'Risikomatrix',
        'singular' => 'Risiko',
        'plural' => 'Risiken',
        'export' => 'Risiken',

        // Risikostufen (in Badges und Zählern angezeigt)
        'levels' => [
            'low' => 'Gering',
            'medium' => 'Mittel',
            'high' => 'Hoch',
            'critical' => 'Kritisch',
        ],

        // Formular- und Listenfelder
        'fields' => [
            'name' => 'Name',
            'description' => 'Beschreibung',
            'owner' => 'Verantwortlicher',
            'no_owner' => 'Nicht zugewiesen',
            'choose_owner' => 'Verantwortlichen wählen',
            'choose_status' => 'Status wählen',

            // Bewertung
            'probability' => 'Wahrscheinlichkeit',
            'probability_comment' => 'Kommentar Wahrscheinlichkeit',
            'impact' => 'Auswirkung',
            'impact_comment' => 'Kommentar Auswirkung',
            'exposure' => 'Exposition',
            'vulnerability' => 'Verwundbarkeit',
            'likelihood' => 'Eintrittswahrscheinlichkeit',
            'score' => 'Punktzahl',

            // Behandlung
            'status' => 'Behandlungsstatus',
            'status_comment' => 'Kommentar Status',
            'measures' => 'Verknüpfte Kontrollen',
            'measures_hint' => 'Erforderlich bei Status = Gemindert',
            'action_plan' => 'Verknüpfte Aktionspläne',
            'actions_hint' => 'Erforderlich bei Status = Nicht akzeptiert',

            // Planung
            'review_frequency' => 'Überprüfungshäufigkeit',
            'next_review' => 'Nächste Überprüfung',
            'overdue' => 'Überprüfung überfällig',
            'overdue_all' => 'Alle',
            'overdue_only' => 'Überfällig',

            // Dashboard / Matrix
            'total' => 'Gesamt',
            'by_status' => 'Verteilung nach Status',
            'by_risks' => 'Verteilung nach Risiken',
        ],

        // Behandlungsstatus
        'status' => [
            'not_evaluated' => 'Nicht bewertet',
            'not_accepted' => 'Nicht akzeptiert',
            'temporarily_accepted' => 'Vorläufig akzeptiert',
            'accepted' => 'Akzeptiert',
            'mitigated' => 'Gemindert',
            'transferred' => 'Übertragen',
            'avoided' => 'Vermieden',
            ],
        ],

        // -------------------------------------------------------------------------
        // Konfiguration der Risikobewertung
        // -------------------------------------------------------------------------
        'risk_scoring' => [

            // Seitentitel
            'list' => 'Risikoklassifizierungsmethoden',
            'create' => 'Neue Klassifizierung',
            'edit' => 'Klassifizierung bearbeiten',
            'activate' => 'Diese Konfiguration aktivieren',

            // Aktionen auf Stufen / Schwellenwerte
            'add_level' => 'Stufe hinzufügen',
            'add_threshold' => 'Schwellenwert hinzufügen',

            // Kontexthilfen
            'levels_hint' => 'Mindestens 2 Stufen. Der Wert muss eine eindeutige ganze Zahl sein.',
            'thresholds_hint' => 'Der letzte Schwellenwert hat keine Obergrenze (Auffangwert). Vom niedrigsten zum höchsten Score sortieren.',

            // Formularfelder
            'fields' => [
                'name' => 'Konfiguration',
                'formula' => 'Berechnung',
                'levels' => 'Stufen',
                'thresholds' => 'Klassifizierungsschwellen',
                'value' => 'Wert',
                'label' => 'Bezeichnung',
                'description' => 'Beschreibung',
                'level_key' => 'Interner Schlüssel',
                'score_max' => 'Max. Punktzahl',
                'color' => 'Farbe',
            ],

            // Verfügbare Farben für Schwellenwerte
            'colors' => [
                'success' => 'Grün',
                'warning' => 'Orange',
                'danger' => 'Rot',
                'alert' => 'Dunkelrot',
                'info' => 'Blau',
                'secondary' => 'Grau',
            ],

            // Verfügbare Formeln (Bezeichnungen)
            'formulas' => [
                'probability_x_impact' => 'Wahrscheinlichkeit × Auswirkung',
                'likelihood_x_impact' => 'Eintrittswahrscheinlichkeit × Auswirkung (BSI 200-3)',
                'additive' => 'Wahrscheinlichkeit + Auswirkung',
                'max_pi' => 'max(Wahrscheinlichkeit, Auswirkung)',
            ],

            // Standardwerte bei der Erstellung einer neuen Konfiguration
            'defaults' => [
                'probability_levels' => [
                    'rare' => 'Selten',
                    'unlikely' => 'Unwahrscheinlich',
                    'possible' => 'Möglich',
                    'likely' => 'Wahrscheinlich',
                    'very_likely' => 'Sehr wahrscheinlich',
                ],
                'exposure_levels' => [
                    'offline' => 'Offline',
                    'internal' => 'Intern',
                    'internet' => 'Internet',
                ],
                'vulnerability_levels' => [
                    'none' => 'Keine',
                    'known' => 'Bekannt',
                    'exploitable_int' => 'Intern ausnutzbar',
                    'exploitable_ext' => 'Extern ausnutzbar',
                ],
                'impact_levels' => [
                    'negligible' => 'Vernachlässigbar',
                    'low' => 'Gering',
                    'moderate' => 'Mäßig',
                    'high' => 'Hoch',
                    'critical' => 'Kritisch',
                ],
                'risk_thresholds' => [
                    'low' => 'Gering',
                    'medium' => 'Mittel',
                    'high' => 'Hoch',
                    'critical' => 'Kritisch',
                ],
            ],
        ],
    'exception' => [
        'list' => 'Ausnahmebehandlung',
        'create' => 'Neue Ausnahme',
        'edit' => 'Ausnahme bearbeiten',
        'title_singular' => 'Ausnahme',
        'title_plural' => 'Ausnahmen',
        'review_section' => 'Entscheidung validieren',
        'expired_hint' => 'Ablaufdatum überschritten.',
        'confirm_submit' => 'Diese Ausnahme zur Validierung einreichen?',
        'confirm_approve' => 'Diese Ausnahme genehmigen?',
        'confirm_reject' => 'Diese Ausnahme ablehnen?',
        'actions' => [
            'submit' => 'Absenden',
            'approve' => 'Genehmigen',
            'reject' => 'Ablehnen',
        ],
        'fields' => [
            'name' => 'Name',
            'measure' => 'Verknüpft control',
            'no_measure' => 'Keine Kontrollmaßnahme',
            'description' => 'Beschreibung',
            'justification' => 'Begründung',
            'compensating_controls' => 'Kompensationsmaßnahmen',
            'start_date' => 'Start',
            'end_date' => 'Ende',
            'status' => 'Status',
            'created_by' => 'Erstellt von',
            'submitted_by' => 'Eingereicht von',
            'approved_by' => 'Genehmigt von',
            'rejected_by' => 'Abgelehnt von',
            'approval_comment' => 'Entscheidungskommentar',
            'approval_comment_optional' => 'Optionaler Kommentar',
            'approval_comment_required' => 'Grund für die Ablehnung (erforderlich)',
            'choose_status' => 'Filtern nach Status',
            'choose_measure' => 'Nach Kontrollgruppe filtern',
            'expired_only' => 'Nur abgelaufene',
            ],
        ],
    ];
