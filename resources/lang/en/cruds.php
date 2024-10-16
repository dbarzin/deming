<?php
return [
    'welcome' => [
         'dashboard' => 'Dashboard',
         'domains' => 'Domains',
         'measures' => 'Controls',
         'controls' => 'Measurements',
         'action_plans' => "Action Plans",
         'next_controls' => 'Checks scheduled for the next 30 days',
         'control_status' => 'Measurement status',
         'control_planning' => 'Schedule measure',
    ],
     'action' => [
         'index' => 'Action plans',
         'show' => 'Action Plan',
         'fields' => [
            'clause' => 'Clause',
            'name' => 'Name',
            'scope' => 'Scope',
            'action' => 'Action plan',
            'plan_date' => 'Planning date',
            'next_date' => 'Review date',
            'note' => 'Score',
            'objective' => 'Objective',
            'observation' => 'Observation',
            'action_plan' => 'Action Plan',
         ],
     ],
    'attribute' => [
        'fields' => [
            'name' => 'Name',
            'values' => 'Values',
        ],
        'add' => 'Add attribute',
        'edit' => 'Edit attribute',
        'show' => 'Attribute',
        'index' => 'List of attributes',
        'choose' => 'Choose an attribute',
        'title' => 'Attribute'
    ],
     'control' => [
         'description' => '',
         'fields' => [
            'action_plan' => 'Action Plan',
            'attributes' => 'Attributes',
            'choose_domain' => 'Choose domain',
            'choose_scope' => 'Choose scope',
            'choose_period' => 'Choose period',
            'choose_attribute' => 'Choose attribute',
            'domain' => 'Domain',
            'indicator' => 'Function',
            'measure' => 'Measure',
            'model' => 'Model',
            'name' => 'Name',
            'next' => 'Next',
            'note' => 'Note',
            'objective' => 'Objective',
            'observations' => 'Observations',
            'plan_date' => 'Planning date',
            'period' => 'Period',
            'periodicity' => 'Periodicity',
            'planned' => 'Planned',
            'realisation_date' => 'Realization date',
            'realized' => 'Realized',
            'evidence' => 'Evidence',
            'score' => 'Score',
            'status' => 'State',
            'status_done' => 'Done',
            'status_todo' => 'To do',
            'status_all' => 'All',
            'scope' => 'Scope',
            'clause' => 'Clause',
            'clauses' => 'Clauses',
            'input' => 'Input',
            'owners' => 'Responsibles',
         ],
        'error' => [
            'made' => 'This measurement has already been made.',
            'duplicate' => 'This measurement already exists.',
        ],
         'checklist' => 'Measure sheet',
         'create' => 'Create a measure',
         'list' => 'Measurement list',
         'edit' => 'Edit measurement',
         'history' => 'Schedule',
         'make' => 'Make a measurement',
         'plan' => 'Schedule a measurement',
         'radar' => 'Security measurement status',
         'title' => 'Measurements',
         'title_singular' => 'Measurement',
        'groupBy' => 'Groop by',
     ],
     'notification' => [
         'subject' => 'Measurement list to carry out',
     ],
     'measure' => [
         'title' => 'Control',
         'fields' => [
             'domain' => 'Domain',
             'clause' => 'Clause',
             'name' => 'Name',
             'objective' => 'Description',
             'attributes' => 'Attributes',
             'model' => 'Model',
             'indicator' => 'Indicator',
             'action_plan' => 'Action Plan',
             'periodicity' => 'Periodicity',
             'input' => 'Input',
         ],
         'show' => 'Control',
         'index' => 'Control list',
         'create' => 'Add a control',
         'edit' => 'Edit a control',
         'plan' => 'Measurement planning'
     ],
     'domain' => [
         'fields' => [
             'framework' => 'Framework',
             'name' => 'Name',
             'description' => 'Description',
             'measures' => '# Controls',
         ],
         'add' => 'Add Domain',
         'edit' => 'Edit Domain',
         'show' => 'Domain',
         'index' => 'List of domains',
         'choose' => 'Choose domain',
         'title' => 'Domain'
     ],
     'document' => [
        'title' => [
            'storage' => 'Storage',
            'templates' => 'Templates',
        ],
         'description' => 'Description',
         'list' => 'List of documents',
         'index' => 'Documents',
         'fields' => [
             'name' => 'Name',
             'control' => 'Control',
             'size' => 'Size',
             'hash' => 'Hash',
         ],
         'model' => [
            'control' => 'Control sheet template',
            'report' => 'Steering report template',
            'custom' => 'Custom model',
         ],
         'count' => 'Number of documents',
         'total_size' => 'Total Size',
     ],
    'exports' => [
         'index' => 'Export data',
         'start' => 'Start',
         'end' => 'End',
         'report_title' => 'Report',
         'steering' => 'ISMS steering report',
         'data_export_title' => 'Data Export',
         'domains_export'=> 'Export domains ',
         'measures_export' => 'Export security measures',
         'controls_export' => 'Export controls',
         'import' => 'Import',
     ],
    'imports' => [
         'index' => 'Import',
         'title' => 'Import security measures',
     ],
     'login' => [
         'title' => 'Enter a password',
         'identification' => 'Login',
         'connection' => 'Connection',
     ],
    'report' => [
        'action_plan' => [
            'id' => '#',
            'title' => 'Action plan',
            'next' => 'Review date'
        ]
    ],
    'soa' => [
        'title' => 'Statement of Applicability (SoA)',
        'generate' => 'Generate report'
    ],
    'user' => [
         'index' => 'List of users',
         'edit' => 'Edit User',
         'add' => 'Add User',
         'show' => 'Show user',
         'fields' => [
             'login' => 'UserId',
             'name' => 'Name',
             'title' => 'Title',
             'role' => 'Role',
             'password' => 'Password',
             'email' => 'email',
             'language' => 'Language',
             'controls' => 'Controls'
         ],
         'roles' => [
             'admin' => 'Administrator',
             'user' => 'User',
             'auditor' => 'Auditor',
             'api' => 'API',
             'auditee' => 'Auditee',
         ],
    ],
    'config' => [
        'notifications' => [
                 'title' => 'Notifications configuration',
                 'title_short' => 'Notifications',
                 'help' => 'This screen allows you to configure the notifications sent by email to users.',
                 'message_subject' => 'Message subject',
                 'message_content' => 'Message content',
                 'message_default_content' => '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="ISO-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<p>Here is the list of measurement that will be due soon:</p>
%table%
<p>This is an automatically generated email</p>
<p>Best regards,<br>Deming</p>
</body>
</html>',
                 'sent_from' => 'Sent from',
                 'to' => 'to',
                 'delay' => 'for controls that expire within',
                 'recurrence' => 'Send a notification',
                 'duration' => [
                     'day' => 'day',
                     'days' => 'days',
                     'month' => 'month',
                     'months' => 'months',
                ],
            ],
        ],
    ];
