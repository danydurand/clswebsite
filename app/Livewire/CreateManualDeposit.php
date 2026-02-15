<?php

namespace App\Livewire;

use App\Helpers\Flash;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\GatewayCurrency;
use Illuminate\Support\Facades\Auth;
use App\Domain\Deposit\DepositServices;

class CreateManualDeposit extends Component
{
    use WithFileUploads;

    public $gateway_currency_id;
    public $amount;
    public $charge = 0;
    public $final_amount = 0;
    public $form_data = [];
    public $proof_file;

    public function mount()
    {
        // Inicializar con el primer gateway manual activo
        $firstManualGateway = GatewayCurrency::whereHas('gateway', function ($query) {
            $query->where('is_active', true)->where('is_manual', true);
        })->first();

        if ($firstManualGateway) {
            $this->gateway_currency_id = $firstManualGateway->id;
            $this->initializeFormFields();
        }
    }

    public function updatedGatewayCurrencyId()
    {
        $this->initializeFormFields();
        $this->calculateCharges();
    }

    public function updatedAmount()
    {
        $this->calculateCharges();
    }

    protected function initializeFormFields()
    {
        $gatewayCurrency = GatewayCurrency::with('gateway.form')->find($this->gateway_currency_id);

        if ($gatewayCurrency && $gatewayCurrency->gateway) {
            $fields = $gatewayCurrency->gateway->getFormFields();

            // Inicializar campos dinámicos
            $this->form_data = [];
            foreach ($fields as $field) {
                $this->form_data[$field['name']] = '';
            }
        }
    }

    protected function calculateCharges()
    {
        if (!$this->amount || !$this->gateway_currency_id) {
            $this->charge = 0;
            $this->final_amount = 0;
            return;
        }

        $gatewayCurrency = GatewayCurrency::find($this->gateway_currency_id);
        if (!$gatewayCurrency) {
            return;
        }

        $calculation = DepositServices::calculateFinalAmount((float) $this->amount, $gatewayCurrency);
        $this->charge = $calculation['charge'];
        $this->final_amount = $calculation['final_amount'];
    }

    public function save()
    {
        // Validación
        $this->validate([
            'amount' => 'required|numeric|min:0.01',
            'gateway_currency_id' => 'required|exists:gateway_currencies,id',
            'proof_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = Auth::user();
        $customer = $user->customer;
        $gatewayCurrency = GatewayCurrency::with('gateway')->find($this->gateway_currency_id);

        // Validar límites
        if (!DepositServices::validateAmount((float) $this->amount, $gatewayCurrency)) {
            Flash::error(__('Amount must be between :min and :max', [
                'min' => $gatewayCurrency->min_amount,
                'max' => $gatewayCurrency->max_amount,
            ]));
            return;
        }

        try {
            $deposit = DepositServices::createManualDeposit(
                $customer,
                $gatewayCurrency,
                (float) $this->amount,
                $this->form_data,
                $this->proof_file
            );

            Flash::success(__('Manual deposit submitted successfully. Awaiting admin approval.'));
            return $this->redirectRoute('deposits.index', navigate: true);

        } catch (\Exception $e) {
            Flash::error(__('Error creating deposit: :error', ['error' => $e->getMessage()]));
        }
    }

    public function render()
    {
        $manualGateways = GatewayCurrency::whereHas('gateway', function ($query) {
            $query->where('is_active', true)->where('is_manual', true);
        })->with('gateway.form')->get();

        $selectedGateway = GatewayCurrency::with('gateway.form')->find($this->gateway_currency_id);

        return view('livewire.create-manual-deposit', [
            'manualGateways' => $manualGateways,
            'selectedGateway' => $selectedGateway,
        ]);
    }
}
