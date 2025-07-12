<?php

namespace Kwhorne\FluxChat\Livewire\Chat\Group;

use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Locked;
use Livewire\WithFileUploads;
use Kwhorne\FluxChat\Enums\ParticipantRole;
use Kwhorne\FluxChat\Facades\FluxChat;
use Kwhorne\FluxChat\Jobs\DeleteConversationJob;
use Kwhorne\FluxChat\Livewire\Chat\Chat;
use Kwhorne\FluxChat\Livewire\Chats\Chats;
use Kwhorne\FluxChat\Livewire\Concerns\ModalComponent;
use Kwhorne\FluxChat\Livewire\Concerns\Widget;
use Kwhorne\FluxChat\Models\Conversation;

class Info extends ModalComponent
{
    use Widget;
    use WithFileUploads;

    #[Locked]
    public Conversation $conversation;

    public $group;

    public $description;

    // #[Validate('required', message: 'Please provide a group name.')]
    // #[Validate('max:120', message: 'Name cannot exceed 120 characters.')]
    public $groupName;

    public $photo;

    public $cover_url;

    protected $listeners = [
        'participantsCountUpdated',
    ];

    public $totalParticipants;

    public function participantsCountUpdated(int $newCount)
    {

        return $this->totalParticipants = $newCount;

    }

    private function setDefaultValues()
    {
        $this->description = $this->group?->description;
        $this->groupName = $this->group?->name;
        $this->cover_url = $this->conversation->group?->cover_url;

    }

    public function messages(): array
    {
        return [
            'groupName.required' => __('fluxchat::validation.required', ['attribute' => __('fluxchat::chat.group.info.inputs.name.label')]),
            'groupName.max' => __('fluxchat::validation.max.string', ['attribute' => __('fluxchat::chat.group.info.inputs.name.label')]),
            'description.max' => __('fluxchat::validation.max.string', ['attribute' => __('fluxchat::chat.group.info.inputs.description.label')]),
            'photo.max' => __('fluxchat::validation.max.file', ['attribute' => __('fluxchat::chat.group.info.inputs.photo.label')]),
            'photo.image' => __('fluxchat::validation.image', ['attribute' => __('fluxchat::chat.group.info.inputs.photo.label')]),
            'photo.mimes' => __('fluxchat::validation.mimes', ['attribute' => __('fluxchat::chat.group.info.inputs.photo.label')]),
        ];
    }

    public static function closeModalOnEscapeIsForceful(): bool
    {
        return false;
    }
    // public static function closeModalOnEscape(): bool
    // {
    //     return false;
    // }

    public function updatedDescription($value)
    {

        abort_unless($this->conversation->isGroup(), 405);
        // dd($value,str($value)->length() );

        $this->validate(
            ['description' => 'max:500|nullable']
        );

        $this->conversation->group?->updateOrCreate(['conversation_id' => $this->conversation->id], ['description' => $value]);
    }

    /* Update Group name when for submittted */
    public function updateGroupName()
    {

        abort_unless($this->conversation->isGroup(), 405);

        $this->validate(
            ['groupName' => 'required|max:120|nullable']
        );

        $this->conversation->group?->updateOrCreate(['conversation_id' => $this->conversation->id], ['name' => $this->groupName]);

        $this->dispatch('refresh');
    }

    /**
     * Group Photo  Configuration
     */
    public function deletePhoto()
    {

        abort_unless($this->conversation->isGroup(), 405);
        // delete photo from group

        $this->group?->cover()?->delete();

        $this->reset('photo');
        $this->cover_url = null;

        $this->dispatch('refresh');

    }

    /**
     * Group Photo  Configuration
     */
    public function updatedPhoto($photo)
    {

        abort_unless($this->conversation->isGroup(), 405);

        // validate
        $this->validate([
            'photo' => 'image|max:12024|nullable|mimes:png,jpg,jpeg,webp',
        ]);

        // create and save photo is present
        if ($photo) {

            // remove current photo
            $this->group?->cover?->delete();
            // save photo to disk
            $path = $photo->store(FluxChat::storageFolder(), FluxChat::storageDisk());
            $url = Storage::disk(FluxChat::storageDisk())->url($path);
            // create attachment
            $this->conversation->group?->cover()?->create([
                'file_path' => $path,
                'file_name' => basename($path),
                'original_name' => $photo->getClientOriginalName(),
                'mime_type' => $photo->getMimeType(),
                'url' => $url,
            ]);

            $this->cover_url = $url;
            $this->reset('photo');

            $this->dispatch('refresh')->to(Chats::class);
            $this->dispatch('refresh')->to(Chat::class);

        }

    }

    /**
     * Delete  private or self chat  */
    public function deleteGroup()
    {
        abort_unless(auth()->check(), 401);

        abort_unless(auth()->user()->belongsToConversation($this->conversation), 403, 'Forbidden: You do not have permission to delete this group.');

        abort_if($this->conversation->isPrivate(), 403, 'Operation not allowed: Private chats cannot be deleted.');

        abort_unless(auth()->user()->isOwnerOf($this->conversation), 403, 'Forbidden: You do not have permission to delete this group.');

        // Ensure all participants are removed before deleting the group
        $participantCount = $this->conversation->participants()
            ->withoutParticipantable(auth()->user())
            ->where('role', '!=', ParticipantRole::OWNER)
            ->count();

        abort_unless($participantCount == 0, 403, 'Cannot delete group: Please remove all members before attempting to delete the group.');

        // handle widget termination
        $this->handleComponentTermination(
            redirectRoute: route(FluxChat::indexRouteName()),
            events: [
                ['close-chat',  ['conversation' => $this->conversation->id]],
                Chats::class => ['chat-deleted',  [$this->conversation->id]],
            ]
        );

        // Soft Delete conversation
        $this->conversation->deleteFor(auth()->user());

        // Dispatch job to delete conversation in backgroud
        // This is done to not hold up page for user incase of long running prcoess and to also give time for widget to settle avoiding 404 livewire hydrate errors
        DeleteConversationJob::dispatch($this->conversation);

    }

    public function exitConversation()
    {
        abort_unless(auth()->check(), 401);

        $auth = auth()->user();

        // make sure owner if group cannot be removed from chat
        abort_if($auth->isOwnerOf($this->conversation), 403, 'Owner cannot exit conversation');

        // delete conversation
        $auth->exitConversation($this->conversation);

        $this->handleComponentTermination(
            redirectRoute: route(FluxChat::indexRouteName()),
            events: [
                'close-chat',
                Chats::class => ['chat-exited',  [$this->conversation->id]],
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
        abort_unless($this->conversation->isGroup(), 403, __('fluxchat::chat.info.group.messages.invalid_conversation_type_error'));
        abort_unless(auth()->user()->belongsToConversation($this->conversation), 403);

        $this->conversation = $this->conversation->load('group.conversation', 'group.cover')->loadCount('participants');

        $this->totalParticipants = $this->conversation->participants_count;
        $this->group = $this->conversation->group;

        $this->setDefaultValues();
    }

    public function render()
    {

        $participant = $this->conversation->participant(auth()->user());

        //  dd($this->isWidget(),$participant);

        // Pass data to the view
        return view('fluxchat::livewire.chat.group.info', [
            'receiver' => $this->conversation->getReceiver(),
            'participant' => $participant,
        ]);
    }
}
