@props([
    'conversation' => null, //Should be conversation  ID (Int)
    'widget' => false
])


<x-fluxchat::actions.open-chat-drawer 
        component="fluxchat.chat.group.info"
        dusk="show_group_info"
        conversation="{{$conversation}}"
        :widget="$widget"
        >
{{$slot}}
</x-fluxchat::actions.open-chat-drawer>
