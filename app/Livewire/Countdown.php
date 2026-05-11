<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;

class Countdown extends Component
{
    public function mount()
    {
        app('site')->title('Countdown');
    }

    public function render()
    {
        // Carbon::setTestNow('2026-04-27 00:00:00');

        $from = Carbon::parse('2026-05-05 12:00:00');
        $to = Carbon::parse('2026-06-26 22:00:00');

        $percentageUntilEndFromNow = 100 - ($to->diffInSeconds(now()) / $to->diffInSeconds($from) * 100);

        return optional(view('livewire.countdown', [
            'percentageUntilEndFromNow' => $percentageUntilEndFromNow,
            'from' => $from,
            'to' => $to,
        ]))->layout('components.layouts.game');
    }
}
