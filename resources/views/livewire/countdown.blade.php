@php
    $days = $to->diffInDays(now());
    $hours = $to->diffInHours(now()) % 24;
    $minutes = $to->diffInMinutes(now()) % 60;
    $seconds = $to->diffInSeconds(now()) % 60;

    $madeIt = now()->greaterThanOrEqualTo($to);
@endphp

<div
    class="
        flex absolute inset-0 flex-col items-center justify-center
        bg-countdown
    "
    x-data="{
        days: {{ $days }},
        hours: {{ $hours }},
        minutes: {{ $minutes }},
        seconds: {{ $seconds }},
        youMadeIt: {{ $madeIt ? 'true' : 'false' }},
        tick () {
            if (this.days === 0 && this.hours === 0 && this.minutes === 0 && this.seconds === 0) {
                this.youMadeIt = true;
                return;
            }

            if (this.seconds > 0) { this.seconds--; return; }
            this.seconds = 59;

            if (this.minutes > 0) { this.minutes--; return; }
            this.minutes = 59;

            if (this.hours > 0) { this.hours--; return; }
            this.hours = 23;

            if (this.days > 0) { this.days--; return; }
            this.days = 0;
        },
        init() {
            {{-- Shoot an event that all other things can catch --}}
            setInterval(() => {
                this.$dispatch('clock-tick')
                this.tick()
            }, 1000)
        }
    }"
>
    {{-- Victory --}}
    <div class="flex w-full justify-center relative">
        <img
            src="{{ asset('images/timeline/kiss.png') }}"
            class="w-[15rem]"
            x-show="youMadeIt"
            x-cloak
        />
    </div>

    {{-- Timeline --}}
    <div class="w-3/4 h-[20rem] justify-between relative">
        <img
            src="{{ asset('images/timeline/pinky.png') }}"
            class="w-[15rem] absolute right-0"
            x-show="! youMadeIt"
        />
    </div>

    <div class="w-full h-1 opacity-50 bg-black rounded-full -mb-1 relative"></div>

    <div class="w-2/4 h-1 bg-pink-400 rounded-full mb-8 relative">
        <div
            x-show="! youMadeIt"
            class="w-[15rem] h-[30rem] absolute bottom-0 animate-shroomy-bounce bg-contain bg-no-repeat bg-bottom"
            style="
                left: calc({{ $percentageUntilEndFromNow }}% - 5.5rem);
                background-image: url('{{ asset('images/timeline/shroomy.png') }}');
            "
        ></div>

        <div
            class="absolute -top-[0.6rem] -translate-x-1/2"
            style="left: {{ $percentageUntilEndFromNow }}%;"
            x-show="! youMadeIt"
        >
            <div class="absolute w-6 h-6 bg-pink-400 rounded-full"></div>
            <div class="absolute -inset-2 w-10 h-10 opacity-35 bg-pink-400 rounded-full animate-ping"></div>
        </div>
    </div>

    <template x-if="youMadeIt">
        <div class="w-full flex items-center gap-2 text-2xl justify-center">
            <span class="text-4xl font-bold">You made it!</span>
        </div>
    </template>
    <template x-if="! youMadeIt">
        <ul class="w-full flex items-center gap-2 text-2xl justify-center">
            <template x-for="item in [
                { time: days, label: 'Days' },
                { time: hours, label: 'Hours' },
                { time: minutes, label: 'Minutes' },
                { time: seconds, label: 'Seconds' },
            ]" :key="item.label">
                <li class="flex flex-col items-center w-[10rem]">
                    <p
                        class="text-4xl font-bold"
                        x-text="item.time"
                        x-show="item.time >= 10"
                    ></p>
                    <p class="text-4xl font-bold" x-show="item.time < 10 && item.time !== 0">
                        <span class="opacity-50 -mr-3">0</span>
                        <span x-text="item.time"></span>
                    </p>
                    <p class="text-4xl font-bold" x-show="item.time === 0">
                        <span class="opacity-50">0</span>
                    </p>

                    <p
                        class="opacity-50"
                        x-text="item.time === 1 ? item.label.slice(0, -1) : item.label"
                    ></p>
                </li>
            </template>
        </ul>
    </template>

    {{-- <div
        class="absolute inset-0 bg-cover bg-bottom"
        style="background-image: url('{{ asset('images/timeline/foreground.png') }}')"
    ></div> --}}
</div>
