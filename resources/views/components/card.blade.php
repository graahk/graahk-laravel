@props([
    'card',
    'isArtifact' => $card->type === \App\Enums\CardType::ARTIFACT,
    'text' => $card->toText(),
    'level' => $card->getLevel(),
    'rounded' => 'rounded-xl',
])

@php if ($isArtifact) { $level = 3; /* Artifacts are always level 3 */ } @endphp

<div
    data-card-id="{{ $card->id }}"
    style="background-image: url('{{ $card->getMedia()->path() }}?1')"
    {{ $attributes->merge(['class' => $rounded . '
        graahk-card has-tooltip w-full overflow-hidden
        bg-cover bg-center relative
        text-black select-none aspect-[2.5/3.5]
        isolate
    ']) }}
>
    <div class="absolute inset-0 {{ $rounded }} overflow-hidden">
        @if ($level >= 4) <div class="z-[-1] {{ $rounded }} overflow-hidden animate-foil -inset-4"></div> @endif
    </div>

    @if (! $isArtifact)
        <img src="{{ asset('images/cards/' . $card->type->value . '-' . $level . '.svg')}}" />
    @endif

    <h2 class="absolute top-[4%] left-[4%] text-center w-[14.5%] font-bold">{{ $card->cost }}</h3>

    <h3 @class([
        'absolute top-[5%] font-bold',
        'left-[21%] w-full' => ! $isArtifact,
        'left-[8%] right-[8%] bg-black p-2 bg-opacity-50 text-white text-bordered-hard text-center rounded-lg' => $isArtifact,
    ])>
        {{ $card->name }}
    </h3>

    <span
        @class([
            'tribes absolute w-[80%] text-lg',
            'bottom-[36.5%] left-[8%]' => ($level <= 1),
            'bottom-[5.5%] left-[36.5%]' => ($level >= 2),
            'left-[8%]' => $card->type === \App\Enums\CardType::RUSE,
        ])
    >
        @if (is_string($card->tribes))
            {{ $card->tribes }}
        @else
            {{ $card->getTribes()->join(', ') }}
        @endif
    </span>

    @if (filled($text))
        <p @class([
            'absolute overflow-y-auto',
            'bottom-[14%]' => $card->type !== \App\Enums\CardType::RUSE,
            'bottom-[11%]' => $card->type === \App\Enums\CardType::RUSE,
            'left-[9%] w-[82%] top-[65%]' => ($level <= 1),
            'left-[4%] w-[92%] p-2 rounded-lg bg-opacity-50' => ($level >= 2),
            'bg-white p-2 bg-opacity-75' => ($level === 2),
            'bg-black p-2 bg-opacity-50 text-white text-bordered-hard' => ($level >= 3),
            '!bottom-[4%] text-center' => $isArtifact,
        ])>
            {!! $text !!}
        </p>
    @endif

    <h4 class="absolute bottom-[2.6%] left-[4%] w-[29%] text-center font-bold">{{ $card->power }}</h4>

    {{ $slot }}
</div>
