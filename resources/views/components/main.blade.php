@props([
    // 'backgroundImage' => \App\Models\Card::query()
    //     ->format(\App\Enums\Format::STANDARD)
    //     ->noTokens()
    //     ->get()
    //     ->random()
    //     ->attachment,
])

<div class="w-full min-h-screen relative">
    <div class="fixed inset-0 w-full min-h-screen gradient-background pointer-events-none"></div>
    {{ $slot }}
</div>
