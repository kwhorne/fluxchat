<?php

namespace Kwhorne\FluxChat\Livewire\Chat;

use Livewire\Attributes\Locked;
use Kwhorne\FluxChat\Facades\FluxChat;
use Kwhorne\FluxChat\Livewire\Chats\Chats;
use Kwhorne\FluxChat\Livewire\Concerns\ModalComponent;
use Kwhorne\FluxChat\Livewire\Concerns\Widget;
use Kwhorne\FluxChat\Models\Conversation;

class Info extends ModalComponent
{
    use Widget;

    #[Locked]
    public Conversation $conversation;

    public static function closeModalOnEscapeIsForceful(): bool
    {
        return false;
    }

    /**
     * -----------------------------
     * Delete Chat
     * */
    public function deleteChat()
    {
        abort_unless(auth()->check(), 401);

        abort_unless(auth()->user()->belongsToConversation($this->conversation), 403);
        abort_unless($this->conversation->isSelf() || $this->conversation->isPrivate(), 403, 'This operation is not available for Groups.');

        // delete conversation
        $this->conversation->deleteFor(auth()->user());

        // redirect to chats page pr
        // Dispatach event instead if isWidget
        // handle widget termination
        $this->handleComponentTermination(
            redirectRoute: route(FluxChat::indexRouteName()),
            events: [
                'close-chat',
                Chats::class => ['chat-deleted',  [$this->conversation->id]],
            ]
        );

    }

    public function placeholder()
    {
        return <<<'HTML'
        <div>
            <!-- Loading spinner... -->
            <x-fluxchat::loading-spin class="m-auto" />
        </div>
        HTML;
    }

    public function mount()
    {

        abort_if(empty($this->conversation), 404);

        abort_unless(auth()->check(), 401);
        abort_unless(auth()->user()->belongsToConversation($this->conversation), 403);

        abort_if($this->conversation->isGroup(), 403, __('fluxchat::chat.info.messages.invalid_conversation_type_error'));

        // load participants
        $this->conversation->load('participants.participantable');

    }

    public function render()
    {

        $receiver = $this->conversation->peerParticipant(auth()->user())?->participantable;

        // Pass data to the view
        return view('fluxchat::livewire.chat.info', [
            'receiver' => $receiver,
            'cover_url' => $receiver?->cover_url,
        ]);
    }
}
