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

    'accepted'             => 'Le champ :attribute doit être accepté.',
    'accepted_if'          => 'Le champ :attribute doit être accepté quand :other est :value.',
    'active_url'           => 'Le champ :attribute n\'est pas une URL valide.',
    'after'                => 'Le champ :attribute doit être une date postérieure au :date.',
    'after_or_equal'       => 'Le champ :attribute doit être une date postérieure ou égale au :date.',
    'alpha'                => 'Le champ :attribute doit contenir uniquement des lettres.',
    'alpha_dash'           => 'Le champ :attribute doit contenir uniquement des lettres, des chiffres et des tirets.',
    'alpha_num'            => 'Le champ :attribute doit contenir uniquement des lettres et des chiffres.',
    'array'                => 'Le champ :attribute doit être un tableau.',
    'ascii'                => 'Le champ :attribute ne doit contenir que des caractères alphanumériques et des symboles ASCII monocodes.',
    'before'               => 'Le champ :attribute doit être une date antérieure au :date.',
    'before_or_equal'      => 'Le champ :attribute doit être une date antérieure ou égale au :date.',
    'between'              => [
        'numeric' => 'La valeur de :attribute doit être comprise entre :min et :max.',
        'file'    => 'La taille du fichier :attribute doit être comprise entre :min et :max kilo-octets.',
        'string'  => 'Le texte :attribute doit contenir entre :min et :max caractères.',
        'array'   => 'Le tableau :attribute doit contenir entre :min et :max éléments.',
    ],
    'boolean'              => 'Le champ :attribute doit être vrai ou faux.',
    'can'                  => 'Le champ :attribute contient une valeur non autorisée.',
    'confirmed'            => 'Le champ de confirmation :attribute ne correspond pas.',
    'contains'             => 'Le champ :attribute requiert la valeur manquante.',
    'current_password'     => 'Le mot de passe est incorrect.',
    'date'                 => 'Le champ :attribute n\'est pas une date valide.',
    'date_equals'          => 'Le champ :attribute doit être une date égale à :date.',
    'date_format'          => 'Le champ :attribute ne correspond pas au format :format.',
    'decimal'              => 'Le champ :attribute doit comporter :decimal décimales.',
    'declined'             => 'Le champ :attribute doit être refusé.',
    'declined_if'          => 'Le champ :attribute doit être refusé quand :other est :value.',
    'different'            => 'Les champs :attribute et :other doivent être différents.',
    'digits'               => 'Le champ :attribute doit contenir :digits chiffres.',
    'digits_between'       => 'Le champ :attribute doit contenir entre :min et :max chiffres.',
    'dimensions'           => 'La taille de l\'image :attribute n\'est pas conforme.',
    'distinct'             => 'Le champ :attribute a une valeur en double.',
    'doesnt_end_with'      => 'Le champ :attribute ne doit pas se terminer par l\'une des valeurs suivantes : :values.',
    'doesnt_start_with'    => 'Le champ :attribute ne doit pas commencer par l\'une des valeurs suivantes : :values.',
    'email'                => 'Le champ :attribute doit être une adresse e-mail valide.',
    'ends_with'            => 'Le champ :attribute doit se terminer par l\'une des valeurs suivantes : :values.',
    'enum'                 => 'Le champ :attribute sélectionné est invalide.',
    'exists'               => 'Le champ :attribute sélectionné est invalide.',
    'extensions'           => 'Le champ :attribute doit avoir l\'une des extensions suivantes : :values.',
    'file'                 => 'Le champ :attribute doit être un fichier.',
    'filled'               => 'Le champ :attribute doit avoir une valeur.',
    'gt'                   => [
        'numeric' => 'La valeur de :attribute doit être supérieure à :value.',
        'file'    => 'La taille du fichier :attribute doit être supérieure à :value kilo-octets.',
        'string'  => 'Le texte :attribute doit contenir plus de :value caractères.',
        'array'   => 'Le tableau :attribute doit contenir plus de :value éléments.',
    ],
    'gte'                  => [
        'numeric' => 'La valeur de :attribute doit être supérieure ou égale à :value.',
        'file'    => 'La taille du fichier :attribute doit être supérieure ou égale à :value kilo-octets.',
        'string'  => 'Le texte :attribute doit contenir au moins :value caractères.',
        'array'   => 'Le tableau :attribute doit contenir au moins :value éléments.',
    ],
    'hex_color'            => 'Le champ :attribute doit être un code couleur hexadécimal valide.',
    'image'                => 'Le champ :attribute doit être une image.',
    'in'                   => 'Le champ :attribute sélectionné est invalide.',
    'in_array'             => 'Le champ :attribute n\'existe pas dans :other.',
    'integer'              => 'Le champ :attribute doit être un entier.',
    'ip'                   => 'Le champ :attribute doit être une adresse IP valide.',
    'ipv4'                 => 'Le champ :attribute doit être une adresse IPv4 valide.',
    'ipv6'                 => 'Le champ :attribute doit être une adresse IPv6 valide.',
    'json'                 => 'Le champ :attribute doit être un document JSON valide.',
    'list'                 => 'Le champ :attribute doit être une liste.',
    'lt'                   => [
        'numeric' => 'La valeur de :attribute doit être inférieure à :value.',
        'file'    => 'La taille du fichier :attribute doit être inférieure à :value kilo-octets.',
        'string'  => 'Le texte :attribute doit contenir moins de :value caractères.',
        'array'   => 'Le tableau :attribute doit contenir moins de :value éléments.',
    ],
    'lte'                  => [
        'numeric' => 'La valeur de :attribute doit être inférieure ou égale à :value.',
        'file'    => 'La taille du fichier :attribute doit être inférieure ou égale à :value kilo-octets.',
        'string'  => 'Le texte :attribute ne doit pas dépasser :value caractères.',
        'array'   => 'Le tableau :attribute ne doit pas contenir plus de :value éléments.',
    ],
    'mac_address'          => 'Le champ :attribute doit être une adresse MAC valide.',
    'max'                  => [
        'numeric' => 'La valeur de :attribute ne peut pas être supérieure à :max.',
        'file'    => 'La taille du fichier :attribute ne peut pas dépasser :max kilo-octets.',
        'string'  => 'Le texte :attribute ne peut pas dépasser :max caractères.',
        'array'   => 'Le tableau :attribute ne peut pas contenir plus de :max éléments.',
    ],
    'max_digits'           => 'Le champ :attribute ne doit pas avoir plus de :max chiffres.',
    'mimes'                => 'Le champ :attribute doit être un fichier de type : :values.',
    'mimetypes'            => 'Le champ :attribute doit être un fichier de type : :values.',
    'min'                  => [
        'numeric' => 'La valeur de :attribute doit être supérieure ou égale à :min.',
        'file'    => 'La taille du fichier :attribute doit être d\'au moins :min kilo-octets.',
        'string'  => 'Le texte :attribute doit contenir au moins :min caractères.',
        'array'   => 'Le tableau :attribute doit contenir au moins :min éléments.',
    ],
    'min_digits'           => 'Le champ :attribute doit avoir au moins :min chiffres.',
    'missing'              => 'Le champ :attribute doit être manquant.',
    'missing_if'           => 'Le champ :attribute doit être manquant quand :other est :value.',
    'missing_unless'       => 'Le champ :attribute doit être manquant sauf si :other est :value.',
    'missing_with'         => 'Le champ :attribute doit être manquant quand :values est présent.',
    'missing_with_all'     => 'Le champ :attribute doit être manquant quand :values sont présents.',
    'multiple_of'          => 'La valeur de :attribute doit être un multiple de :value.',
    'not_in'               => 'Le champ :attribute sélectionné n\'est pas valide.',
    'not_regex'            => 'Le format du champ :attribute n\'est pas valide.',
    'numeric'              => 'Le champ :attribute doit être un nombre.',
    'password'             => [
        'letters'       => 'Le champ :attribute doit contenir au moins une lettre.',
        'mixed'         => 'Le champ :attribute doit contenir au moins une lettre majuscule et une lettre minuscule.',
        'numbers'       => 'Le champ :attribute doit contenir au moins un chiffre.',
        'symbols'       => 'Le champ :attribute doit contenir au moins un symbole.',
        'uncompromised' => 'La valeur du champ :attribute est apparue dans une fuite de données. Veuillez choisir une autre valeur.',
    ],
    'present'              => 'Le champ :attribute doit être présent.',
    'present_if'           => 'Le champ :attribute doit être présent quand :other est :value.',
    'present_unless'       => 'Le champ :attribute doit être présent sauf si :other est :value.',
    'present_with'         => 'Le champ :attribute doit être présent quand :values est présent.',
    'present_with_all'     => 'Le champ :attribute doit être présent quand :values sont présents.',
    'prohibited'           => 'Le champ :attribute est interdit.',
    'prohibited_if'        => 'Le champ :attribute est interdit quand :other est :value.',
    'prohibited_unless'    => 'Le champ :attribute est interdit sauf si :other est dans :values.',
    'prohibits'            => 'Le champ :attribute interdit la présence de :other.',
    'regex'                => 'Le format du champ :attribute n\'est pas valide.',
    'required'             => 'Le champ :attribute est obligatoire.',
    'required_array_keys'  => 'Le champ :attribute doit contenir des entrées pour : :values.',
    'required_if'          => 'Le champ :attribute est obligatoire quand :other est :value.',
    'required_if_accepted' => 'Le champ :attribute est obligatoire quand :other est accepté.',
    'required_if_declined' => 'Le champ :attribute est obligatoire quand :other est décliné.',
    'required_unless'      => 'Le champ :attribute est obligatoire sauf si :other est :value.',
    'required_with'        => 'Le champ :attribute est obligatoire quand :values est présent.',
    'required_with_all'    => 'Le champ :attribute est obligatoire quand :values sont présents.',
    'required_without'     => 'Le champ :attribute est obligatoire quand :values n\'est pas présent.',
    'required_without_all' => 'Le champ :attribute est obligatoire quand aucun des champs :values n\'est présent.',
    'same'                 => 'Les champs :attribute et :other doivent correspondre.',
    'size'                 => [
        'numeric' => 'La valeur de :attribute doit être :size.',
        'file'    => 'La taille du fichier :attribute doit être de :size kilo-octets.',
        'string'  => 'Le texte :attribute doit contenir :size caractères.',
        'array'   => 'Le tableau :attribute doit contenir :size éléments.',
    ],
    'starts_with'          => 'Le champ :attribute doit commencer par l\'une des valeurs suivantes : :values.',
    'string'               => 'Le champ :attribute doit être une chaîne de caractères.',
    'timezone'             => 'Le champ :attribute doit être un fuseau horaire valide.',
    'unique'               => 'La valeur du champ :attribute est déjà utilisée.',
    'uploaded'             => 'Le fichier du champ :attribute n\'a pu être téléversé.',
    'uppercase'            => 'Le champ :attribute doit être en majuscules.',
    'url'                  => 'Le format du champ :attribute n\'est pas valide.',
    'uuid'                 => 'Le champ :attribute doit être un UUID valide.',

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
        'email' => [
            'unique' => 'Cette adresse e-mail est déjà enregistrée.',
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

    'attributes' => [
        'name'                      => 'nom',
        'email'                     => 'adresse e-mail',
        'password'                  => 'mot de passe',
        'password_confirmation'     => 'confirmation du mot de passe',
        'telephone'                 => 'téléphone',
        'sexe'                      => 'genre',
        'age'                       => 'âge',
        'niveau_id'                 => 'niveau',
        'type_id'                   => 'type d\'utilisateur',
        'profil'                    => 'photo de profil',
        'code'                      => 'code d\'activation',
        'verification_code'         => 'code de vérification',
        'intitule'                  => 'intitulé',
        'intitule.contentType'      => 'type de contenu de l\'intitulé',
        'intitule.mediaSourceType'  => 'source du média de l\'intitulé',
        'intitule.text'             => 'texte de l\'intitulé',
        'intitule.mediaDescription' => 'description du média de l\'intitulé',
        'intitule.mediaUrl'         => 'URL du média de l\'intitulé',
        'intitule.mediaFile'        => 'fichier média de l\'intitulé',
        'theme'                     => 'thématique',
        'partie_id'                 => 'partie',
        'difficulte'                => 'difficulté',
        'typeReponse'               => 'type de réponse',
        'explication'               => 'explication',
        'indice'                    => 'indice',
        'options'                   => 'options de réponse',
        'options.*.isCorrect'       => 'correction de l\'option',
        'options.*.contentType'     => 'type de contenu de l\'option',
        'options.*.text'            => 'texte de l\'option',
        'options.*.mediaSourceType' => 'source du média de l\'option',
        'options.*.mediaUrl'        => 'URL du média de l\'option',
        'options.*.mediaDescription'=> 'description du média de l\'option',
    ],

];
