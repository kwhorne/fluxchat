<?php

arch('app')
    ->expect('Kwhorne\FluxChat')
    ->not->toUse(['die', 'dd', 'dump']);

arch('Traits test ')
    ->expect('Kwhorne\FluxChat\Traits')
    ->toBeTraits();

arch('Make sure Actor is only used in Chatable Trait')
    ->expect('Kwhorne\FluxChat\Traits\Actor')
    ->toOnlyBeUsedIn('Kwhorne\FluxChat\Traits\Chatable');

arch('Make sure Actionable is used in Conversation Model')
    ->expect('Kwhorne\\FluxChat\\Traits\\Actionable')
    ->toBeUsedIn('Kwhorne\FluxChat\Models\Conversation');

arch('Ensure Widget Trait is used in Components')
    ->expect('Kwhorne\\FluxChat\\Livewire\\Concerns\Widget')
    ->toBeUsedIn([
        'Kwhorne\FluxChat\Livewire\Chat\Chat',
        'Kwhorne\FluxChat\Livewire\Chats\Chats',
        'Kwhorne\FluxChat\Livewire\New\Chat',
        'Kwhorne\FluxChat\Livewire\New\Group',
        // 'Kwhorne\FluxChat\Livewire\Chat\Group\AddMembers',
        'Kwhorne\FluxChat\Livewire\Chat\Info',
        'Kwhorne\FluxChat\Livewire\Chat\Group\Members',
    ]);
