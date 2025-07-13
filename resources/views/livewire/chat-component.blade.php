<!-- FluxChat Component -->
<div class="h-full w-full flex flex-row antialiased text-gray-200 bg-gray-900 overflow-hidden">
    <!-- Sidebar -->
    <section class="flex flex-col flex-none overflow-auto w-24 group lg:max-w-sm md:w-2/5 transition-all duration-300 ease-in-out">
        <!-- Search -->
        <div class="search-box p-4 flex-none">
            <flux:input 
                wire:model.live="searchTerm"
                placeholder="{{ __('fluxchat::messages.search_contacts') }}"
                class="bg-gray-800 border-gray-700 text-gray-200 placeholder-gray-400"
            />
        </div>
        
        <!-- Contact List -->
        <div class="flex flex-col space-y-1 mt-4 mx-2 h-full overflow-y-auto">
            @forelse($filteredContacts as $contact)
                <div 
                    wire:click="selectContact({{ is_array($contact) ? $contact['id'] : $contact->id }})"
                    class="flex items-center p-3 bg-gray-800 hover:bg-gray-700 cursor-pointer rounded-lg transition-colors duration-200 {{ $selectedContact && (is_array($selectedContact) ? $selectedContact['id'] : $selectedContact->id) === (is_array($contact) ? $contact['id'] : $contact->id) ? 'bg-gray-700' : '' }}"
                >
                    <flux:avatar 
                        name="{{ is_array($contact) ? $contact[$contactNameField] : $contact->{$contactNameField} }}"
                        size="sm"
                        class="mr-3"
                    />
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-200 truncate">
                            {{ is_array($contact) ? $contact[$contactNameField] : $contact->{$contactNameField} }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-8">
                    <p>{{ __('fluxchat::messages.no_contacts_found') }}</p>
                </div>
            @endforelse
        </div>
    </section>
    
    <!-- Chat Area -->
    @if($selectedContact)
        <section class="flex flex-col flex-auto border-l border-gray-800 min-h-0">
            <!-- Chat Header -->
            <div class="chat-header px-6 py-4 flex flex-row flex-none justify-between items-center shadow">
                <div class="flex items-center">
                    <flux:avatar 
                        name="{{ is_array($selectedContact) ? $selectedContact[$contactNameField] : $selectedContact->{$contactNameField} }}"
                        size="sm"
                        class="mr-3"
                    />
                    <div>
                        <p class="text-lg font-semibold text-gray-200">
                            {{ is_array($selectedContact) ? $selectedContact[$contactNameField] : $selectedContact->{$contactNameField} }}
                        </p>
                        <p class="text-sm text-gray-400">
                            {{ __('fluxchat::messages.online') }}
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Chat Messages -->
            <div class="chat-body p-4 flex-1 overflow-y-auto">
                @forelse($messages as $message)
                    <div class="flex {{ $message->sendable_type === get_class(Auth::user()) && $message->sendable_id === Auth::id() ? 'justify-end' : 'justify-start' }} mb-4">
                        @if($message->sendable_type !== get_class(Auth::user()) || $message->sendable_id !== Auth::id())
                            <flux:avatar 
                                name="{{ is_array($selectedContact) ? $selectedContact[$contactNameField] : $selectedContact->{$contactNameField} }}"
                                size="sm"
                                class="mr-3 mt-1"
                            />
                        @endif
                        
                        <div class="max-w-xs lg:max-w-md">
                            <div class="px-4 py-2 rounded-lg {{ $message->sendable_type === get_class(Auth::user()) && $message->sendable_id === Auth::id() ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-200' }}">
                                <p class="text-sm">{{ $message->body }}</p>
                            </div>
                            <p class="text-xs text-gray-400 mt-1 {{ $message->sendable_type === get_class(Auth::user()) && $message->sendable_id === Auth::id() ? 'text-right' : 'text-left' }}">
                                {{ $message->created_at->format('H:i') }}
                            </p>
                        </div>
                        
                        @if($message->sendable_type === get_class(Auth::user()) && $message->sendable_id === Auth::id())
                            <flux:avatar 
                                name="{{ Auth::user()->name }}"
                                size="sm"
                                class="ml-3 mt-1"
                            />
                        @endif
                    </div>
                @empty
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center text-gray-500">
                            <p class="text-lg mb-2">{{ __('fluxchat::messages.no_messages_yet') }}</p>
                            <p class="text-sm">{{ __('fluxchat::messages.start_conversation') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>
            
            <!-- Chat Input -->
            <div class="chat-footer flex-none bg-gray-800 border-t border-gray-700 mt-auto">
                <form wire:submit="sendMessage" class="flex flex-row items-center p-4 space-x-3">
                    <flux:button 
                        variant="ghost" 
                        icon="plus" 
                        size="sm"
                        class="rounded-full hover:bg-gray-700 text-gray-400"
                    />
                    
                    <flux:input 
                        wire:model="newMessage"
                        placeholder="{{ __('fluxchat::messages.type_message') }}"
                        class="flex-1 bg-gray-700 border-gray-600 text-gray-200 placeholder-gray-400"
                        wire:keydown.enter="sendMessage"
                    />
                    
                    <flux:button 
                        type="submit"
                        variant="primary" 
                        icon="paper-airplane"
                        size="sm"
                        :disabled="empty($newMessage)"
                        class="px-4"
                    >
                        {{ __('fluxchat::messages.send') }}
                    </flux:button>
                </form>
            </div>
        </section>
    @else
        <section class="flex flex-col flex-auto border-l border-gray-800 items-center justify-center">
            <div class="text-center text-gray-500">
                <flux:heading size="lg" class="text-gray-400 mb-4">{{ __('fluxchat::messages.select_contact') }}</flux:heading>
                <p>{{ __('fluxchat::messages.choose_contact') }}</p>
            </div>
        </section>
    @endif
</div>

@script
<script>
let currentChannel = null;

// Real-time støtte via Echo (kun hvis aktivert)
if (window.Echo && @js($this->isRealtimeEnabled())) {
    // Lytt til meldinger for valgt kontakt
    $wire.watch('selectedContact', (contact) => {
        // Leave previous channel
        if (currentChannel) {
            window.Echo.leaveChannel(currentChannel);
            currentChannel = null;
        }
        
        if (contact && contact.id) {
            // Get conversation ID - for now use contact ID as fallback
            const prefix = @js(config('fluxchat.broadcasting.channel_prefix', 'fluxchat'));
            const channelName = prefix + '.conversation.' + contact.id;
            currentChannel = channelName;
            
            window.Echo.channel(channelName)
                .listen('message.sent', (e) => {
                    // Refresh komponenten når ny melding mottas
                    $wire.$refresh();
                    
                    // Auto-scroll til bunns
                    setTimeout(() => {
                        const chatBody = document.querySelector('.chat-body');
                        if (chatBody) {
                            chatBody.scrollTop = chatBody.scrollHeight;
                        }
                    }, 100);
                });
        }
    });
} else {
    // Fallback: Polling for nye meldinger hvis Echo ikke er tilgjengelig
    const refreshInterval = @js(config('fluxchat.realtime.auto_refresh_interval', 5)) * 1000;
    
    setInterval(() => {
        if ($wire.selectedContact) {
            $wire.$refresh();
        }
    }, refreshInterval);
}

// Auto-scroll til bunns når komponenten refreshes
document.addEventListener('livewire:navigated', () => {
    setTimeout(() => {
        const chatBody = document.querySelector('.chat-body');
        if (chatBody) {
            chatBody.scrollTop = chatBody.scrollHeight;
        }
    }, 100);
});
</script>
@endscript
