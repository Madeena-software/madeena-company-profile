<?php

namespace App\Livewire;

use App\Models\InabuyerMessage;
use Livewire\Component;

class Inabuyer2026Display extends Component
{
    public function render()
    {
        // Get the latest 8 messages to fit a vertical display
        $messages = InabuyerMessage::latest()->take(8)->get();

        return view('livewire.inabuyer2026-display', [
            'messages' => $messages,
        ])->layout('layouts.display');
    }
}
