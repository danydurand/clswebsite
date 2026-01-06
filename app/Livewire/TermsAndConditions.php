<?php

namespace App\Livewire;

use App\Models\System;
use App\Models\TermAndCondition;
use Livewire\Component;

class TermsAndConditions extends Component
{
    public $termsBySystem = [];

    public function mount()
    {
        // Get all active systems
        $systems = System::where('is_active', true)
            ->orderBy('name')
            ->get();

        // For each system, get its active terms ordered by 'order' field
        foreach ($systems as $system) {
            $terms = TermAndCondition::where('system_id', $system->id)
                ->where('is_active', true)
                ->orderBy('order')
                ->get();

            if ($terms->isNotEmpty()) {
                $this->termsBySystem[] = [
                    'system' => $system,
                    'terms' => $terms,
                ];
            }
        }
    }

    #[\Livewire\Attributes\Layout('components.layouts.public')]
    public function render()
    {
        return view('livewire.terms-and-conditions');
    }
}
