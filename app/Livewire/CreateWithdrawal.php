<?php

namespace App\Livewire;

use App\Models\WithdrawMethod;
use App\Models\Customer;
use App\Domain\Withdrawal\WithdrawalServices;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

/**
 * Componente Livewire para crear un nuevo retiro - Paso 1
 * 
 * Este componente permite al cliente seleccionar un método de retiro,
 * ingresar el monto, y ver el cálculo de comisiones en tiempo real.
 */
class CreateWithdrawal extends Component
{
    /**
     * ID del método de retiro seleccionado
     *
     * @var int|null
     */
    public $selectedMethodId = null;

    /**
     * Monto del retiro
     *
     * @var float
     */
    public $amount = 0;

    /**
     * Resultado del cálculo de comisiones
     *
     * @var array
     */
    public $calculation = [
        'charge' => 0,
        'after_charge' => 0,
        'final_amount' => 0,
    ];

    /**
     * Método seleccionado
     *
     * @var WithdrawMethod|null
     */
    public $selectedMethod = null;

    /**
     * Mensaje de error de validación
     *
     * @var string|null
     */
    public $validationError = null;

    /**
     * Inicializa el componente
     */
    public function mount()
    {
        // Seleccionar el primer método por defecto
        $firstMethod = WithdrawMethod::where('is_active', true)->first();
        if ($firstMethod) {
            $this->selectedMethodId = $firstMethod->id;
            $this->selectedMethod = $firstMethod;
        }
    }

    /**
     * Se ejecuta cuando cambia el método seleccionado
     */
    public function updatedSelectedMethodId($value)
    {
        $this->selectedMethod = WithdrawMethod::find($value);
        $this->calculateCharges();
    }

    /**
     * Se ejecuta cuando cambia el monto
     */
    public function updatedAmount($value)
    {
        $this->calculateCharges();
    }

    /**
     * Calcula las comisiones y monto final
     */
    public function calculateCharges()
    {
        $this->validationError = null;

        if (!$this->selectedMethod || $this->amount <= 0) {
            $this->calculation = [
                'charge' => 0,
                'after_charge' => 0,
                'final_amount' => 0,
            ];
            return;
        }

        // Calcular usando el servicio
        $this->calculation = WithdrawalServices::calculateFinalAmount(
            $this->amount,
            $this->selectedMethod
        );

        // Validar que quede algo después de comisiones
        if ($this->calculation['after_charge'] <= 0) {
            $this->validationError = __('Withdrawal amount must be sufficient for charges');
        }
    }

    /**
     * Envía el formulario y crea la solicitud de retiro
     */
    public function submit()
    {
        $customer = Auth::user()->customer;

        // Validaciones
        $this->validate([
            'selectedMethodId' => 'required|exists:withdraw_methods,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        if (!$this->selectedMethod) {
            session()->flash('flash_error', [__('Please select a withdrawal method')]);
            return;
        }

        // Validar límites del método
        if (!WithdrawalServices::validateAmount($this->amount, $this->selectedMethod)) {
            session()->flash('flash_error', [
                __('Amount must be between :min and :max', [
                    'min' => number_format($this->selectedMethod->min_limit, 2),
                    'max' => number_format($this->selectedMethod->max_limit, 2),
                ])
            ]);
            return;
        }

        // Validar límites globales
        if (!WithdrawalServices::validateGlobalLimits($this->amount)) {
            session()->flash('flash_error', [__('Amount exceeds system limits')]);
            return;
        }

        // Validar balance
        if (!WithdrawalServices::validateBalance($customer, $this->amount)) {
            session()->flash('flash_error', [__('Insufficient balance')]);
            return;
        }

        // Validar que quede algo después de comisiones
        if ($this->calculation['after_charge'] <= 0) {
            session()->flash('flash_error', [__('Withdrawal amount must be sufficient for charges')]);
            return;
        }

        try {
            // Crear la solicitud de retiro
            $withdrawal = WithdrawalServices::createWithdrawalRequest(
                $customer,
                $this->selectedMethod,
                $this->amount
            );

            // Redirigir a la página de confirmación
            return redirect()->route('withdrawals.confirm', ['trx' => $withdrawal->trx]);
        } catch (\Exception $e) {
            session()->flash('flash_error', [$e->getMessage()]);
        }
    }

    /**
     * Computed property que retorna los métodos activos
     */
    public function getMethodsProperty()
    {
        return WithdrawMethod::where('is_active', true)->get();
    }

    /**
     * Computed property que retorna el balance del cliente
     */
    public function getCustomerBalanceProperty()
    {
        return Auth::user()->customer->balance;
    }

    /**
     * Renderiza el componente
     */
    public function render()
    {
        return view('livewire.create-withdrawal', [
            'methods' => $this->methods,
            'customerBalance' => $this->customerBalance,
        ]);
    }
}
