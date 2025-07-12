@props([
    'widget' => false
])


<x-fluxchat::actions.open-modal
        component="fluxchat.new.group"
        :widget="$widget"
        >
{{$slot}}
</x-fluxchat::actions.open-modal>
