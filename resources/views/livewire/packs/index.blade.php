<x-container class="py-12">
    <div
        class="relative w-1/3"
        x-data="{
            current: 0,
            open: false,
            next() {
                if (! this.open) {
                    this.open = true
                    return
                }

                this.current++
            },
        }"
    >
        <div
            class="absolute top-0"
            style="z-index: 101; width: 130%; left: 5%; max-width: auto"
            x-show="! open"
            x-on:click="next()"
            x-transition:leave="animate-pack-open"
        >
            <img src="{{ $set->attachment->path() }}" style="max-width: none;  width: 82%;" />
        </div>

        @foreach ($cards as $card)
            <div
                class="absolute top-0 w-full h-full"
                style="left: {{ 10 - ($loop->index * 0.5) }}%; z-index: {{ 100 - $loop->index }}"
                x-show="current <= {{ $loop->index }}"
                x-on:click="next()"
                x-transition:leave="animate-pack-next-card"
            >
                <x-card :card="$card" :has-tooltip="false" />
            </div>
        @endforeach
    </div>
</x-container>
