@props([
    'conversation' => null, //Should be conversation  ID (Int)
    'widget' => false
])


<x-fluxchat::actions.open-chat-drawer 
        component="fluxchat.chat.info"
        dusk="show_chat_info"
        conversation="{{$conversation}}"
        :widget="$widget"
        >
{{$slot}}
</x-fluxchat::actions.open-chat-drawer>
