<?php

    return [
        /**
         * Langue par défaut
         */
        'default_lang' => 'fr',
        /*
         * Liste des langues autorisés avec leurs poids
         */
        'allowed_lang' => [
            'fr' => 10,
            'en' => 9,
            'all' => 8,
            'es' => 8,
            'it' => 8,
            'ru' => 8
        ],
        /**
         * Le poid maximum d'une langue
         */
        'poid_max' => 10,
        /**
         * Parametre chercher dans l'url pour la langue
         */
        'param_url' => 'lang'
    ];

?>
