<?php

namespace App\Livewire;

use App\Models\InabuyerMessage;
use Livewire\Component;

class Inabuyer2026Display extends Component
{
    public function render()
    {
        $messages = InabuyerMessage::visible()->latest()->get();

        return view('livewire.inabuyer2026-display', [
            'messages' => $messages,
        ])->layout('layouts.display');
    }
}
