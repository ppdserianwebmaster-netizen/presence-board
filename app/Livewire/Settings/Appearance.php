<?php

namespace App\Livewire\Settings;

use Livewire\Attributes\Layout;
use Livewire\Component;

class Appearance extends Component
{
    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.settings.appearance');
    }
}
