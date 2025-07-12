<?php

return [

    // new-chat component
    'chat' => [
        'labels' => [
            'heading' => ' Ny Chat',
            'you' => 'Deg',

        ],

        'inputs' => [
            'search' => [
                'label' => 'Søk i samtaler',
                'placeholder' => 'Søk',
            ],
        ],

        'actions' => [
            'new_group' => [
                'label' => 'Ny gruppe',
            ],

        ],

        'messages' => [

            'empty_search_result' => 'Ingen brukere funnet som samsvarer med søket ditt.',
        ],
    ],

    // new-group component
    'group' => [
        'labels' => [
            'heading' => ' Ny Chat',
            'add_members' => ' Legg til medlemmer',

        ],

        'inputs' => [
            'name' => [
                'label' => 'Gruppenavn',
                'placeholder' => 'Skriv inn navn',
            ],
            'description' => [
                'label' => 'Beskrivelse',
                'placeholder' => 'Valgfritt',
            ],
            'search' => [
                'label' => 'Søk',
                'placeholder' => 'Søk',
            ],
            'photo' => [
                'label' => 'Foto',
            ],
        ],

        'actions' => [
            'cancel' => [
                'label' => 'Avbryt',
            ],
            'next' => [
                'label' => 'Neste',
            ],
            'create' => [
                'label' => 'Opprett',
            ],

        ],

        'messages' => [
            'members_limit_error' => 'Medlemmer kan ikke overstige :count',
            'empty_search_result' => 'Ingen brukere funnet som samsvarer med søket ditt.',
        ],
    ],

];
