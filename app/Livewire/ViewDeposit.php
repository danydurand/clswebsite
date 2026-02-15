<?php

namespace App\Livewire;

use App\Models\Deposit;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

/**
 * Componente Livewire para visualizar los detalles de un depósito
 * 
 * Muestra toda la información del depósito incluyendo:
 * - Información general (TRX, fecha, gateway)
 * - Montos (amount, charge, final_amount)
 * - Estado actual
 * - Detalles del formulario (si es manual)
 * - Comprobante de pago (si es manual)
 */
class ViewDeposit extends Component
{
    /**
     * El depósito a visualizar
     *
     * @var Deposit
     */
    public Deposit $deposit;

    /**
     * Campos del formulario dinámico (para depósitos manuales)
     *
     * @var array
     */
    public $formFields = [];

    /**
     * Inicializa el componente y verifica autorización
     *
     * @param Deposit $deposit
     * @return void|\Illuminate\Http\RedirectResponse
     */
    public function mount(Deposit $deposit)
    {
        $user = Auth::user();
        $customer = $user->customer;

        // Verificar que el depósito pertenece al cliente autenticado
        if ($deposit->customer_id !== $customer->id) {
            session()->flash('error', __('You do not have permission to view this deposit'));
            return redirect()->route('deposits.index');
        }

        $this->deposit = $deposit;

        // Si es un depósito manual, cargar los campos del formulario
        if ($deposit->gateway && $deposit->gateway->isManual()) {
            $this->formFields = $deposit->gateway->getFormFields();
        }
    }

    /**
     * Renderiza el componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.view-deposit');
    }
}
