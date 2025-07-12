<?php

namespace Kwhorne\FluxChat\Livewire\Pages;

use Livewire\Attributes\Title;
use Livewire\Component;

class Chats extends Component
{
    #[Title('Chats')]
    public function render()
    {
        return view('fluxchat::livewire.pages.chats')
            ->layout(config('fluxchat.layout', 'fluxchat::layouts.app'));

    }
}
