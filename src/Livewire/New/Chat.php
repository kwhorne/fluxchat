<?php

namespace Kwhorne\FluxChat\Livewire\New;

use Kwhorne\FluxChat\Facades\FluxChat;
use Kwhorne\FluxChat\Livewire\Concerns\ModalComponent;
use Kwhorne\FluxChat\Livewire\Concerns\Widget;
use Kwhorne\FluxChat\Livewire\Widgets\FluxChat as WidgetsFluxChat;

class Chat extends ModalComponent
{
    use Widget;

    public $users = [];

    public $search;

    public static function modalAttributes(): array
    {
        return [
            'closeOnEscape' => true,
            'closeOnEscapeIsForceful' => true,
            'destroyOnClose' => true,
            'closeOnClickAway' => true,
        ];

    }

    /**
     * Search For users to create conversations with
     */
    public function updatedsearch()
    {

        // Make sure it's not empty
        if (blank($this->search)) {

            $this->users = [];
        } else {

            $this->users = auth()->user()->searchChatables($this->search);
        }
    }

    public function createConversation($id, string $class)
    {

        // resolve model from params -get model class
        $model = app($class);
        $model = $model::find($id);

        if ($model) {
            $createdConversation = auth()->user()->createConversationWith($model);

            if ($createdConversation) {

                // close dialog
                $this->closeFluxChatModal();

                // redirect to conversation
                $this->handleComponentTermination(
                    redirectRoute: route(FluxChat::viewRouteName(), [$createdConversation->id]),
                    events: [
                        WidgetsFluxChat::class => ['open-chat',  ['conversation' => $createdConversation->id]],
                    ]
                );

            }
        }
    }

    public function mount()
    {

        abort_unless(auth()->check(), 401);
    }

    public function render()
    {
        return view('fluxchat::livewire.new.chat');
    }
}
