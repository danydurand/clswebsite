<?php

namespace App\Livewire;

use App\Models\Withdrawal;
use App\Domain\Withdrawal\WithdrawalServices;
use App\Domain\Withdrawal\WithdrawalStatusEnum;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

/**
 * Componente Livewire para confirmar un retiro - Paso 2
 * 
 * Este componente muestra el resumen del retiro y solicita
 * la información adicional requerida por el formulario dinámico.
 */
class ConfirmWithdrawal extends Component
{
    /**
     * Retiro a confirmar
     *
     * @var Withdrawal
     */
    public Withdrawal $withdrawal;

    /**
     * Información del formulario dinámico
     *
     * @var array
     */
    public $withdrawInfo = [];

    /**
     * Inicializa el componente
     */
    public function mount(string $trx)
    {
        $customer = Auth::user()->customer;

        // Buscar el retiro
        $this->withdrawal = Withdrawal::where('trx', $trx)
            ->where('customer_id', $customer->id)
            ->where('status', WithdrawalStatusEnum::Initiate->value)
            ->with(['withdrawMethod.form'])
            ->firstOrFail();

        // Validar que el método esté activo
        if (!$this->withdrawal->withdrawMethod->is_active) {
            abort(404, __('Withdrawal method is not available'));
        }

        // Inicializar campos del formulario dinámico
        if ($this->withdrawal->withdrawMethod->form) {
            $formData = $this->withdrawal->withdrawMethod->form->form_data ?? [];
            foreach ($formData as $field) {
                $this->withdrawInfo[$field['name']] = '';
            }
        }
    }

    /**
     * Confirma el retiro
     */
    public function submit()
    {
        $customer = Auth::user()->customer;

        // Validar formulario dinámico
        if ($this->withdrawal->withdrawMethod->form) {
            $formData = $this->withdrawal->withdrawMethod->form->form_data ?? [];
            $rules = [];

            foreach ($formData as $field) {
                $fieldRules = [];
                if ($field['required'] ?? false) {
                    $fieldRules[] = 'required';
                }
                if (!empty($fieldRules)) {
                    $rules['withdrawInfo.' . $field['name']] = implode('|', $fieldRules);
                }
            }

            $this->validate($rules);
        }

        // Validar balance nuevamente
        if (!WithdrawalServices::validateBalance($customer, $this->withdrawal->amount)) {
            session()->flash('flash_error', [__('Insufficient balance')]);
            return redirect()->route('withdrawals');
        }

        try {
            // Confirmar el retiro (descuenta balance y crea transacción)
            WithdrawalServices::confirmWithdrawal($this->withdrawal, $this->withdrawInfo);

            session()->flash('flash_success', [__('Withdrawal submitted successfully')]);
            return redirect()->route('withdrawals');
        } catch (\Exception $e) {
            session()->flash('flash_error', [$e->getMessage()]);
        }
    }

    /**
     * Renderiza el componente
     */
    public function render()
    {
        return view('livewire.confirm-withdrawal');
    }
}
