<?php

revenir [

     /*
     |--------------------------------------------------------------- -------------------------
     | Lignes de langage de validation
     |--------------------------------------------------------------- -------------------------
     |
     | Les lignes de langage suivantes contiennent les messages d'erreur par défaut utilisés par
     | la classe validateur. Certaines de ces règles ont plusieurs versions telles que
     | comme les règles de taille. N'hésitez pas à modifier chacun de ces messages ici.
     |
     */

     'accepted' => 'L\':attribute doit être accepté.',
     'active_url' => 'L\':attribut n\'est pas une URL valide.',
     'after' => 'Le :attribute doit être une date postérieure à :date.',
     'after_or_equal' => 'Le :attribute doit être une date postérieure ou égale à :date.',
     'alpha' => 'Le :attribut ne peut contenir que des lettres.',
     'alpha_dash' => 'Le :attribut ne peut contenir que des lettres, des chiffres, des tirets et des traits de soulignement.',
     'alpha_num' => 'Le :attribut ne peut contenir que des lettres et des chiffres.',
     'array' => 'Le :attribut doit être un tableau.',
     'before' => 'Le :attribut doit être une date avant :date.',
     'before_or_equal' => 'Le :attribute doit être une date antérieure ou égale à :date.',
     'entre' => [
         'numeric' => 'Le :attribute doit être compris entre :min et :max.',
         'file' => 'Le :attribute doit être compris entre :min et :max kilo-octets.',
         'string' => 'Le :attribute doit être compris entre :min et :max caractères.',
         'array' => 'Le :attribute doit avoir entre :min et :max éléments.',
     ],
     'boolean' => 'Le champ :attribute doit être vrai ou faux.',
     'confirmed' => 'La confirmation :attribute ne correspond pas.',
     'date' => 'Le :attribut n\'est pas une date valide.',
     'date_equals' => 'Le :attribut doit être une date égale à :date.',
     'date_format' => 'Le :attribut ne correspond pas au format :format.',
     'different' => 'Le :attribute et :other doivent être différents.',
     'digits' => 'Le :attribut doit être :digits chiffres.',
     'digits_between' => 'Le :attribut doit être compris entre :min et :max chiffres.',
     'dimensions' => 'L\':attribut a des dimensions d\'image invalides.',
     'distinct' => 'Le champ :attribute a une valeur en double.',
     'email' => 'Le :attribute doit être une adresse e-mail valide.',
     'ends_with' => 'Le :attribute doit se terminer par l\'un des éléments suivants : :values.',
     'exists' => 'L\'attribut sélectionné n\'est pas valide.',
     'file' => 'Le :attribute doit être un fichier.',
     'filled' => 'Le champ :attribute doit avoir une valeur.',
     'gt' => [
         'numeric' => 'Le :attribut doit être supérieur à :valeur.',
         'file' => 'Le :attribute doit être supérieur à :value kilo-octets.',
         'string' => 'Le :attribut doit être supérieur à :value caractères.',
         'array' => 'Le :attribute doit avoir plus de :value éléments.',
     ],
     'gte' => [
         'numeric' => 'Le :attribut doit être supérieur ou égal à :value.',
         'file' => 'Le :attribute doit être supérieur ou égal à :value kilo-octets.',
         'string' => 'Le :attribut doit être supérieur ou égal à :valeur caractères.',
         'array' => 'Le :attribute doit avoir des éléments :value ou plus.',
     ],
     'image' => 'Le :attribute doit être une image.',
     'in' => 'L\'attribut sélectionné n\'est pas valide.',
     'in_array' => 'Le champ :attribute n\'existe pas dans :other.',
     'integer' => 'Le :attribut doit être un entier.',
     'ip' => 'Le :attribute doit être une adresse IP valide.',
     'ipv4' => 'Le :attribute doit être une adresse IPv4 valide.',
     'ipv6' => 'Le :attribute doit être une adresse IPv6 valide.',
     'json' => 'Le :attribute doit être une chaîne JSON valide.',
     'lt' => [
         'numeric' => 'Le :attribut doit être inférieur à :value.',
         'file' => 'Le :attribute doit être inférieur à :value kilo-octets.',
         'string' => 'Le :attribute doit être inférieur à :value caractères.',
         'array' => 'Le :attribute doit avoir moins de :value éléments.',
     ],
     'lte' => [
         'numeric' => 'Le :attribut doit être inférieur ou égal à :value.',
         'file' => 'Le :attribute doit être inférieur ou égal à :value kilo-octets.',
         'string' => 'Le :attribut doit être inférieur ou égal à :valeur caractères.',
         'array' => 'Le :attribut ne doit pas avoir plus de :value éléments.',
     ],
     'max' => [
         'numeric' => 'Le :attribute ne peut pas être supérieur à :max.',
         'file' => 'Le :attribute ne doit pas être supérieur à :max kilo-octets.',
         'string' => 'Le :attribute ne doit pas être supérieur à :max caractères.',
         'array' => 'Le :attribute ne peut pas avoir plus de :max éléments.',
     ],
     'mimes' => 'Le :attribute doit être un fichier de type : :values.',
     'mimetypes' => 'Le :attribute doit être un fichier de type : :values.',
     'min' => [
         'numeric' => 'Le :attribut doit être au moins égal à :min.',
         'file' => 'Le :attribute doit faire au moins :min kilo-octets.',
         'string' => 'Le :attribute doit contenir au moins :min caractères.',
         'array' => 'Le :attribute doit avoir au moins :min éléments.',
     ],
     'not_in' => 'L\'attribut sélectionné est i