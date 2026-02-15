<?php

namespace App\Livewire;

use App\Models\Faq;
use Livewire\Component;

class Faqs extends Component
{
    public $faqs = [];

    public function mount()
    {
        // Get all active FAQs ordered by 'order' field
        $this->faqs = Faq::where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    #[\Livewire\Attributes\Layout('components.layouts.public')]
    public function render()
    {
        return view('livewire.faqs');
    }
}
