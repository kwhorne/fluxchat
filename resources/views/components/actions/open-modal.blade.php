@props([
    'component', 
    'conversation' => null,
    'widget' => false
])

<div  onclick="Livewire.dispatch('openFluxChatModal', { 
        component: '{{ $component }}', 
        arguments: { 
            conversation:`{{$conversation ?? null }}`, 
            widget: @js($widget)
        } 
    })">

    {{ $slot }}
</div>
