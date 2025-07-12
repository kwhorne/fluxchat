<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Standard Laravel Valideringsspråklinjer
    |--------------------------------------------------------------------------
    |
    | Følgende språklinjer inneholder standard feilmeldinger som brukes av
    | Laravel validatorklassen. Noen av disse reglene har flere versjoner som
    | størrelse regler. Du kan justere hver av disse meldingene her.
    |
    */
    'file' => ':attribute-feltet må være en fil.',
    'image' => ':attribute-feltet må være et bilde.',
    'required' => ':attribute-feltet er påkrevd.',
    'max' => [
        'array' => ':attribute-feltet må ikke ha mer enn :max elementer.',
        'file' => ':attribute-feltet må ikke være større enn :max kilobytes.',
        'numeric' => ':attribute-feltet må ikke være større enn :max.',
        'string' => ':attribute-feltet må ikke være lenger enn :max tegn.',
    ],
    'mimes' => ':attribute må være en fil av type: :values.',

    /*
    |--------------------------------------------------------------------------
    | Tilpassede Valideringsspråklinjer
    |--------------------------------------------------------------------------
    |
    | Her kan du spesifisere tilpassede valideringsmeldinger for attributter ved å bruke
    | konvensjonen "attribute.rule" for å navngi linjene. Dette gjør det raskt å
    | spesifisere en spesifikk tilpasset språklinje for en gitt attributtregel.
    |
    */

    'custom' => [],

];
