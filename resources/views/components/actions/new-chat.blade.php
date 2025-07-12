@props([
    'widget' => false
])


<x-fluxchat::actions.open-modal
        component="fluxchat.new.chat"
        :widget="$widget"
        >
{{$slot}}
</x-fluxchat::actions.open-modal>
