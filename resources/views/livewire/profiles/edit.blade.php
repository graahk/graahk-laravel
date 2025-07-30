<x-container class="flex flex-col gap-12 pt-12">
    <div class="flex items-center gap-8">
        <x-avatar class="w-32 h-32" :user="$user" />

        <div class="flex flex-col">
            <p class="text-3xl font-extrabold">{{ $user->username }}</p>
            <p class="opacity-50">Joined {{ $user->created_at->diffForHumans() }}</p>
        </div>
    </div>

    <div class="flex gap-4 w-full">
        <div
            class="flex flex-col gap-4 w-full"
            wire:loading.class="opacity-50"
        >
            <x-headers.h2 label="Upload new avatar" />

            <x-form.input
                label="avatar"
                wire:model.live="avatar"
                type="file"
            />

            <x-headers.h2 label="Select previous avatar" />

            <div class="grid grid-cols-5 gap-4 w-full mb-4">
                @foreach ($user->getOldAvatars() as $avatar)
                    <div
                        style="background-image: url('{{ $avatar->path() }}')"
                        class="w-full aspect-square bg-cover bg-center rounded-lg cursor-pointer opacity-80 hover:opacity-100 transition-opacity"
                        wire:click="setCardAvatar({{ $avatar->id }})"
                    ></div>
                @endforeach
            </div>

            <x-headers.h2 label="Select a card avatar" />

            <div class="grid grid-cols-5 gap-4 w-full">
                @foreach ($cards as $card)
                    <div
                        style="background-image: url('{{ $card->attachment->path() }}')"
                        class="w-full aspect-square bg-cover bg-center rounded-lg cursor-pointer opacity-80 hover:opacity-100 transition-opacity"
                        wire:click="setCardAvatar({{ $card->attachment->id }}, {{ (int) ($card->getLevel() === 4) }})"
                    ></div>
                @endforeach
            </div>
        </div>

        <form
            wire:submit.prevent="update"
            class="flex flex-col gap-6 w-full"
        >
            <x-headers.h2 label="General" />

            <x-form.input
                label="Username"
                wire:model="username"
            />

            <x-form.input
                label="E-mail"
                wire:model="email"
            />

            <div>
                <x-form.button label="Update profile" />
            </div>
        </form>
    </div>
</x-container>
