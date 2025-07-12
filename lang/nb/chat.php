<?php

return [

    /**-------------------------
     * Chat
     *------------------------*/
    'labels' => [

        'you_replied_to_yourself' => 'Du svarte på deg selv',
        'participant_replied_to_you' => ':sender svarte på deg',
        'participant_replied_to_themself' => ':sender svarte på seg selv',
        'participant_replied_other_participant' => ':sender svarte på :receiver',
        'you' => 'Deg',
        'user' => 'Bruker',
        'replying_to' => 'Svarer på :participant',
        'replying_to_yourself' => 'Svarer på deg selv',
        'attachment' => 'Vedlegg',
    ],

    'inputs' => [
        'message' => [
            'label' => 'Melding',
            'placeholder' => 'Skriv en melding',
        ],
        'media' => [
            'label' => 'Media',
            'placeholder' => 'Media',
        ],
        'files' => [
            'label' => 'Filer',
            'placeholder' => 'Filer',
        ],
    ],

    'message_groups' => [
        'today' => 'I dag',
        'yesterday' => 'I går',

    ],

    'actions' => [
        'open_group_info' => [
            'label' => 'Gruppeinfo',
        ],
        'open_chat_info' => [
            'label' => 'Chat-info',
        ],
        'close_chat' => [
            'label' => 'Lukk chat',
        ],
        'clear_chat' => [
            'label' => 'Tøm chat-historikk',
            'confirmation_message' => 'Er du sikker på at du vil tømme chat-historikken? Dette vil bare tømme din chat og vil ikke påvirke andre deltakere.',
        ],
        'delete_chat' => [
            'label' => 'Slett chat',
            'confirmation_message' => 'Er du sikker på at du vil slette denne chatten? Dette vil bare fjerne chatten fra din side og vil ikke slette den for andre deltakere.',
        ],

        'delete_for_everyone' => [
            'label' => 'Slett for alle',
            'confirmation_message' => 'Er du sikker?',
        ],
        'delete_for_me' => [
            'label' => 'Slett for meg',
            'confirmation_message' => 'Er du sikker?',
        ],
        'reply' => [
            'label' => 'Svar',
        ],

        'exit_group' => [
            'label' => 'Forlat gruppe',
            'confirmation_message' => 'Er du sikker på at du vil forlate denne gruppen?',
        ],
        'upload_file' => [
            'label' => 'Fil',
        ],
        'upload_media' => [
            'label' => 'Bilder og videoer',
        ],
    ],

    'messages' => [

        'cannot_exit_self_or_private_conversation' => 'Kan ikke forlate egen eller privat samtale',
        'owner_cannot_exit_conversation' => 'Eier kan ikke forlate samtale',
        'rate_limit' => 'For mange forsøk! Vennligst rolig deg ned',
        'conversation_not_found' => 'Samtale ikke funnet.',
        'conversation_id_required' => 'En samtale-ID er påkrevd',
        'invalid_conversation_input' => 'Ugyldig samtaleinndata.',
    ],

    /**-------------------------
     * Info Component
     *------------------------*/

    'info' => [
        'heading' => [
            'label' => 'Chat-info',
        ],
        'actions' => [
            'delete_chat' => [
                'label' => 'Slett chat',
                'confirmation_message' => 'Er du sikker på at du vil slette denne chatten? Dette vil bare fjerne chatten fra din side og vil ikke slette den for andre deltakere.',
            ],
        ],
        'messages' => [
            'invalid_conversation_type_error' => 'Kun private og egen samtaler tillatt',
        ],

    ],

    /**-------------------------
     * Group Folder
     *------------------------*/

    'group' => [

        // Group info component
        'info' => [
            'heading' => [
                'label' => 'Gruppeinfo',
            ],
            'labels' => [
                'members' => 'Medlemmer',
                'add_description' => 'Legg til gruppebeskrivelse',
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
                'photo' => [
                    'label' => 'Foto',
                ],
            ],
            'actions' => [
                'delete_group' => [
                    'label' => 'Slett gruppe',
                    'confirmation_message' => 'Er du sikker på at du vil slette denne gruppen?',
                    'helper_text' => 'Før du kan slette gruppen, må du fjerne alle gruppemedlemmer.',
                ],
                'add_members' => [
                    'label' => 'Legg til medlemmer',
                ],
                'group_permissions' => [
                    'label' => 'Grupperettigheter',
                ],
                'exit_group' => [
                    'label' => 'Forlat gruppe',
                    'confirmation_message' => 'Er du sikker på at du vil forlate gruppen?',

                ],
            ],
            'messages' => [
                'invalid_conversation_type_error' => 'Kun gruppesamtaler tillatt',
            ],
        ],
        // Members component
        'members' => [
            'heading' => [
                'label' => 'Medlemmer',
            ],
            'inputs' => [
                'search' => [
                    'label' => 'Søk',
                    'placeholder' => 'Søk medlemmer',
                ],
            ],
            'labels' => [
                'members' => 'Medlemmer',
                'owner' => 'Eier',
                'admin' => 'Admin',
                'no_members_found' => 'Ingen medlemmer funnet',
            ],
            'actions' => [
                'send_message_to_yourself' => [
                    'label' => 'Send melding til deg selv',

                ],
                'send_message_to_member' => [
                    'label' => 'Send melding til :member',

                ],
                'dismiss_admin' => [
                    'label' => 'Fjern som admin',
                    'confirmation_message' => 'Er du sikker på at du vil fjerne :member som admin?',
                ],
                'make_admin' => [
                    'label' => 'Gjør til admin',
                    'confirmation_message' => 'Er du sikker på at du vil gjøre :member til admin?',
                ],
                'remove_from_group' => [
                    'label' => 'Fjern',
                    'confirmation_message' => 'Er du sikker på at du vil fjerne :member fra denne gruppen?',
                ],
                'load_more' => [
                    'label' => 'Last inn mer',
                ],

            ],
            'messages' => [
                'invalid_conversation_type_error' => 'Kun gruppesamtaler tillatt',
            ],
        ],
        // add-Members component
        'add_members' => [
            'heading' => [
                'label' => 'Legg til medlemmer',
            ],
            'inputs' => [
                'search' => [
                    'label' => 'Søk',
                    'placeholder' => 'Søk',
                ],
            ],
            'labels' => [

            ],
            'actions' => [
                'save' => [
                    'label' => 'Lagre',

                ],

            ],
            'messages' => [
                'invalid_conversation_type_error' => 'Kun gruppesamtaler tillatt',
                'members_limit_error' => 'Medlemmer kan ikke overstige :count',
                'member_already_exists' => ' Allerede lagt til i gruppen',
            ],
        ],
        // permissions component
        'permisssions' => [
            'heading' => [
                'label' => 'Rettigheter',
            ],
            'inputs' => [
                'search' => [
                    'label' => 'Søk',
                    'placeholder' => 'Søk',
                ],
            ],
            'labels' => [
                'members_can' => 'Medlemmer kan',

            ],
            'actions' => [
                'edit_group_information' => [
                    'label' => 'Rediger gruppeinformasjon',
                    'helper_text' => 'Dette inkluderer navn, ikon og beskrivelse',
                ],
                'send_messages' => [
                    'label' => 'Send meldinger',
                ],
                'add_other_members' => [
                    'label' => 'Legg til andre medlemmer',
                ],

            ],
            'messages' => [
            ],
        ],

    ],

];
