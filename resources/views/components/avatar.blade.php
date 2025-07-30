<div {{ $attributes->except('user')->merge([
    'class' => 'w-16 h-16 bg-surface rounded-lg overflow-hidden isolate relative',
]) }}>
    {{-- <div class="absolute inset-0 rounded-xl overflow-hidden">
        @if ($user?->has_foil_avatar)
            <div class="z-0 rounded-xl overflow-hidden animate-foil -inset-4"></div>
        @endif
    </div> --}}

    <div
        class="w-full bg-background bg-cover bg-center z-[-1] absolute inset-0"
        style="background-image: url('{{ $user?->avatar_url }}')"
    ></div>
</div>
