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
        Carbon::setTestNow('2028-04-27 00:00:00');

        $from = Carbon::parse('2026-03-29 12:00:00');
        $to = Carbon::parse('2026-04-27 00:00:02');

        $percentageUntilEndFromNow = 100 - ($to->diffInSeconds(now()) / $to->diffInSeconds($from) * 100);

        return optional(view('livewire.countdown', [
            'percentageUntilEndFromNow' => $percentageUntilEndFromNow,
            'from' => $from,
            'to' => $to,
        ]))->layout('components.layouts.game');
    }
}
