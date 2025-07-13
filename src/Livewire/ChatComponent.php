<?php

namespace Wirelabs\FluxChat\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use Wirelabs\FluxChat\Events\MessageSent;
use Wirelabs\FluxChat\Models\Conversation;
use Wirelabs\FluxChat\Models\Message;
use Wirelabs\FluxChat\Models\Participant;

class ChatComponent extends Component
{
    public $selectedContact = null;
    public $newMessage = '';
    public $searchTerm = '';
    public $currentLocale = 'en';
    public $contacts = [];
    
    // Configuration
    public $contactModel = null;
    public $contactNameField = 'name';
    public $contactSearchFields = ['name'];
    public $maxContacts = 10;

    public function mount($contacts = [], $contactModel = null, $locale = null)
    {
        $this->contacts = $contacts;
        $this->contactModel = $contactModel ?? config('auth.providers.users.model', \App\Models\User::class);
        $this->currentLocale = $locale ?? App::getLocale();
        $this->selectedContact = null;
    }

    public function selectContact($contactId)
    {
        if (empty($this->contacts)) {
            // If no contacts provided, try to find from contact model
            $contact = app($this->contactModel)->find($contactId);
        } else {
            // Find contact in provided contacts
            $contact = collect($this->contacts)->firstWhere('id', $contactId);
        }

        $this->selectedContact = $contact;
        
        if ($this->selectedContact) {
            $this->markConversationAsRead();
        }
    }

    public function getMessagesProperty()
    {
        if (!$this->selectedContact) {
            return collect();
        }

        $conversation = $this->findOrCreateConversation();
        
        if (!$conversation) {
            return collect();
        }

        return $conversation->messages()
            ->with('sendable')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function sendMessage()
    {
        if (empty($this->newMessage) || !$this->selectedContact) {
            return;
        }
        
        $conversation = $this->findOrCreateConversation();
        
        if (!$conversation) {
            return;
        }
        
        $message = $conversation->messages()->create([
            'sendable_id' => Auth::id(),
            'sendable_type' => get_class(Auth::user()),
            'body' => $this->newMessage,
            'type' => 'text',
        ]);
        
        // Broadcast message if real-time is enabled
        if ($this->isRealtimeEnabled()) {
            broadcast(new MessageSent($message));
        }
        
        $this->newMessage = '';
    }

    protected function findOrCreateConversation()
    {
        if (!$this->selectedContact || !Auth::check()) {
            return null;
        }

        $userId = Auth::id();
        $userType = get_class(Auth::user());
        $contactId = $this->selectedContact->id ?? $this->selectedContact['id'];
        $contactType = $this->contactModel;

        // Find existing conversation between these two participants
        $conversation = Conversation::whereHas('participants', function ($query) use ($userId, $userType) {
            $query->where('participatable_type', $userType)
                  ->where('participatable_id', $userId);
        })->whereHas('participants', function ($query) use ($contactId, $contactType) {
            $query->where('participatable_type', $contactType)
                  ->where('participatable_id', $contactId);
        })->where('is_group', false)->first();

        if (!$conversation) {
            // Create new conversation
            $conversation = Conversation::create([
                'type' => 'private',
                'is_group' => false,
            ]);

            // Add participants
            $conversation->participants()->createMany([
                [
                    'participatable_type' => $userType,
                    'participatable_id' => $userId,
                ],
                [
                    'participatable_type' => $contactType,
                    'participatable_id' => $contactId,
                ]
            ]);
        }

        return $conversation;
    }

    protected function markConversationAsRead()
    {
        $conversation = $this->findOrCreateConversation();
        
        if ($conversation) {
            $conversation->markAsRead(Auth::user());
        }
    }

    public function getFilteredContactsProperty()
    {
        if (empty($this->contacts)) {
            // If no contacts provided, query from contact model
            $query = app($this->contactModel)->query();
            
            if ($this->searchTerm) {
                $query->where(function ($q) {
                    foreach ($this->contactSearchFields as $field) {
                        $q->orWhere($field, 'like', '%' . $this->searchTerm . '%');
                    }
                });
            }
            
            return $query->take($this->maxContacts)->get();
        }

        // Filter provided contacts
        if ($this->searchTerm) {
            return collect($this->contacts)->filter(function ($contact) {
                $name = is_array($contact) ? ($contact[$this->contactNameField] ?? '') : ($contact->{$this->contactNameField} ?? '');
                return str_contains(strtolower($name), strtolower($this->searchTerm));
            })->take($this->maxContacts);
        }

        return collect($this->contacts)->take($this->maxContacts);
    }

    public function isRealtimeEnabled(): bool
    {
        return config('fluxchat.realtime.enabled', false) && 
               config('broadcasting.default') !== 'null' && 
               config('broadcasting.default') !== 'log';
    }

    public function changeLanguage($locale)
    {
        $this->currentLocale = $locale;
        App::setLocale($locale);
        session()->put('locale', $locale);
    }

    public function render()
    {
        return view('fluxchat::livewire.chat-component', [
            'messages' => $this->messages,
            'filteredContacts' => $this->filteredContacts,
        ]);
    }
}
