<?php

namespace App\Livewire;

use App\Models\Withdrawal;
use App\Domain\Withdrawal\WithdrawalStatusEnum;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

/**
 * Componente Livewire para ver los detalles de un retiro
 * 
 * Este componente muestra toda la información de un retiro específico.
 */
class ViewWithdrawal extends Component
{
    /**
     * Retiro a visualizar
     *
     * @var Withdrawal
     */
    public Withdrawal $withdrawal;

    /**
     * Inicializa el componente
     */
    public function mount(int $id)
    {
        $customer = Auth::user()->customer;

        // Buscar el retiro
        $this->withdrawal = Withdrawal::where('id', $id)
            ->where('customer_id', $customer->id)
            ->with(['withdrawMethod'])
            ->firstOrFail();
    }

    /**
     * Renderiza el componente
     */
    public function render()
    {
        return view('livewire.view-withdrawal');
    }
}
