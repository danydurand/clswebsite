<?php

namespace App\Livewire;

use App\Models\TermAndCondition;
use Livewire\Component;

class TermsAndConditions extends Component
{
    public $terms = [];
    public $source = null; // Track where the page was called from

    public function mount()
    {
        // Get source from request to avoid Livewire URL syncing
        $this->source = request('source');

        // Get all active terms ordered by 'order' field
        $this->terms = TermAndCondition::where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    #[\Livewire\Attributes\Layout('components.layouts.public')]
    public function render()
    {
        return view('livewire.terms-and-conditions');
    }
}
