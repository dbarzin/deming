<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Das :attribute muss akzeptiert werden.',
    'active_url' => 'Das :attribute ist keine valide URL.',
    'after' => 'Das :attribute Muss ein Datum nach dem :date erhalten.',
    'after_or_equal' => 'Das :attribute muß ein Datum nach oder gleich :date erhalten.',
    'alpha' => 'Das :attribute darf nur aus Buchstaben bestehen.',
    'alpha_dash' => 'Das :attribute darf nur Buchstaben, Nummern, Querstriche und Unterstriche entehalten.',
    'alpha_num' => 'Das :attribute darf nur Buchstaben und Zahlen enthalten.',
    'array' => 'Das :attribute muß eine Auflistung sein.',
    'before' => 'Das :attribute benötigt ein Datum vor dem :date.',
    'before_or_equal' => 'Das :attribute benötigt ein Datum vor oder gleich dem :date.',
    'between' => [
        'numeric' => 'Das :attribute muß zwischen :min und :max liegen.',
        'file' => 'Das :attribute muß zwischen :min und :max kilobytes liegen.',
        'string' => 'Das :attribute muß zwischen :min und :max Zeichen haben.',
        'array' => 'Das :attribute muß zwischen :min und :max Einträge vorweisen.',
    ],
    'boolean' => 'Das :attribute Feld muss Wahr oder Falsch sein.',
    'confirmed' => 'Die :attribute Bestätigung stimmt nicht überein.',
    'date' => ':attribute ist kein gültiges Datum.',
    'date_equals' => 'Das :attribute muss ein Datum sein, das dem :date entspricht.',
    'date_format' => 'Das :attribute entspricht nicht dem Format :format.',
    'different' => ':attribute und :other müssen sich unterscheiden.',
    'digits' => ':attribute muß :digits Stellen haben.',
    'digits_between' => ':attribute muß zwischen :min und :max Stellen haben.',
    'dimensions' => 'Das :attribute hat flasche Bilddimensionen.',
    'distinct' => 'Das :attribute Fekd hat einen doppelten Wert.',
    'email' => ':attribute muß eine gültige Mailadresse sein.',
    'ends_with' => ':attribute muß mit einem der folgenden Werte aufhören: :values.',
    'exists' => 'Das ausgewählte :attribute ist ungültig.',
    'file' => ':attribute muß eine Datei sein.',
    'filled' => ':attribute muß einen Wert haben.',
    'gt' => [
        'numeric' => ':attribute muß größer als :value sein.',
        'file' => ':attribute muß größer als :value kilobytes sein.',
        'string' => ':attribute muß länger als :value Zeichen sein.',
        'array' => ':attribute muß mehr als :value Elemente beinhalten.',
    ],
    'gte' => [
        'numeric' => ':attribute muß größer oder gleich :value sein.',
        'file' => ':attribute muß größer oder gleich :value kilobytes sein.',
        'string' => ':attribute muß mehr oder gleich :value Zeichen haben.',
        'array' => 'The :attribute muß :value oder mehr Elemente beinhalten.',
    ],
    'image' => ':attribute muß ein Bild sein.',
    'in' => 'Das gewählte :attribute ist ungültig.',
    'in_array' => 'Das :attribute Feld existiert nicht in :other.',
    'integer' => ':attribute muß eine Zahl sein.',
    'ip' => ':attribute muß eine gültige IP-Adresse sein.',
    'ipv4' => ':attribute muß eine gültige IPv4 Adresse sein.',
    'ipv6' => ':attribute muß eine gültige IPv6 Adresse sein.',
    'json' => ':attribute muß ein gültiger JSON-String sein.',
    'lt' => [
        'numeric' => ':attribute muß kleiner als :value sein.',
        'file' => ':attribute muß kleiner als :value kilobytes sein.',
        'string' => ':attribute muß kürzer als :value Zeichen sein.',
        'array' => ':attribute muß weniger als :value Elemente beinhalten.',
    ],
    'lte' => [
        'numeric' => ':attribute muß kleiner oder gleich :value sein.',
        'file' => ':attribute muß kleiner oder gleich :value kilobytes sein.',
        'string' => ':attribute muß kleiner oder gleich :value Zeichen haben.',
        'array' => 'The :attribute muß :value oder weniger Elemente beinhalten.',
    ],
    'max' => [
        'numeric' => ':attribute darf nicht größer sein als :max.',
        'file' => ':attribute darf nicht größer sein als :max kilobytes.',
        'string' => ':attribute darf nicht größer sein als :max Zeichen.',
        'array' => ':attribute darf nicht mehr als :max Elemente haben.',
    ],
    'mimes' => ':attribute muß eine Datei vom Typ: :values sein.',
    'mimetypes' => ':attribute muß eine Datei vom Typ: :values sein.',
    'min' => [
        'numeric' => ':attribute muß mindestens :min sein.',
        'file' => ':attribute muß mindestens :min kilobytes haben.',
        'string' => ':attribute muß mindestens :min Zeichen lang sein.',
        'array' => ':attribute muß mindestens :min Elemente haben.',
    ],
    'not_in' => 'Das ausgewählte :attribute ist ungültig.',
    'not_regex' => 'Das gewählte Format :attribute ist ungültig.',
    'numeric' => ' :attribute muß eine Nummer sein.',
    'password' => 'Das Passwort ist falsch.',
    'present' => ' :attribute muß vorhanden sein.',
    'regex' => 'Das Format von :attribute ist ungültig.',
    'required' => 'Das Feld :attribute wird benötigt.',
    'required_if' => 'Das Feld :attribute wird benötigt wenn :other den Wert :value hat.',
    'required_unless' => 'Das Feld :attribute wurd benötigt wenn :other nicht in :values enthalten ist.',
    'required_with' => ':attribute wird benötigt wenn :values vorhanden ist.',
    'required_with_all' => ':attribute wird benötigt wenn :values vorhanden sind.',
    'required_without' => ':attribute wird benötigt wenn :values nicht vorhanden ist.',
    'required_without_all' => ':attribute ist erforderlich, wenn :values nicht vorhanden sind.',
    'same' => ' :attribute und :other müssen übereinstimmen.',
    'size' => [
        'numeric' => ':attribute muß die Größe :size haben.',
        'file' => ':attribute muß :size kilobytes groß sein.',
        'string' => ':attribute muß :size Zeichen lang sein.',
        'array' => ' :attribute muß :size Elemente beinhalten.',
    ],
    'starts_with' => ':attribute muß mit :values anfangen.',
    'string' => ':attribute muß ein String sein.',
    'timezone' => ' :attribute muß eine gültige Zeitzone sein.',
    'unique' => ' :attribute wurde bereits ausgewählt.',
    'uploaded' => ' :attribute konnte nicht hochgeladen werden.',
    'url' => 'Das Format von :attribute ist ungültig.',
    'uuid' => ':attribute muß eine gültige UUID sein.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
