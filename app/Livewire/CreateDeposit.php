<?php

namespace App\Livewire;

use Flux\Flux;
use App\Helpers\Flash;
use Livewire\Component;
use App\Models\Gateway;
use App\Models\GatewayCurrency;
use Illuminate\Support\Facades\Auth;
use App\Domain\Deposit\DepositServices;

/**
 * Componente Livewire para crear un nuevo depósito
 * 
 * Este componente proporciona un formulario completo para que el cliente
 * cree un nuevo depósito, seleccionando el gateway y el monto, con
 * cálculo automático de comisiones.
 */
class CreateDeposit extends Component
{
    /**
     * ID de la moneda del gateway seleccionado
     *
     * @var int|null
     */
    public $gateway_currency_id;

    /**
     * Monto del depósito ingresado por el usuario
     *
     * @var float|null
     */
    public $amount;

    /**
     * Comisión calculada
     *
     * @var float
     */
    public $charge = 0;

    /**
     * Monto final calculado (amount + charge)
     *
     * @var float
     */
    public $final_amount = 0;

    /**
     * Mensaje de feedback para el usuario
     *
     * @var string
     */
    public $userMessage = '';

    /**
     * Color del mensaje (green para éxito, red para error)
     *
     * @var string
     */
    public $colorMessage = 'red';

    /**
     * Gateway currency seleccionado (cargado dinámicamente)
     *
     * @var GatewayCurrency|null
     */
    protected $selectedGatewayCurrency;

    /**
     * Inicializa el componente
     *
     * @return void
     */
    public function mount()
    {
        // Inicializar con el primer gateway activo disponible
        $firstGateway = GatewayCurrency::whereHas('gateway', function ($query) {
            $query->where('is_active', true);
        })->first();

        if ($firstGateway) {
            $this->gateway_currency_id = $firstGateway->id;
            $this->calculateCharges();
        }
    }

    /**
     * Se ejecuta cuando cambia el gateway seleccionado
     *
     * @param mixed $value
     * @return void
     */
    public function updatedGatewayCurrencyId($value)
    {
        $this->selectedGatewayCurrency = null; // Reset cache
        $this->calculateCharges();
    }

    /**
     * Se ejecuta cuando cambia el monto ingresado
     *
     * @param mixed $value
     * @return void
     */
    public function updatedAmount($value)
    {
        $this->calculateCharges();
    }

    /**
     * Calcula las comisiones y el monto final
     * 
     * Utiliza el servicio DepositServices para calcular la comisión
     * basada en el gateway seleccionado y el monto ingresado.
     *
     * @return void
     */
    public function calculateCharges()
    {
        if (!$this->gateway_currency_id || !$this->amount || $this->amount <= 0) {
            $this->charge = 0;
            $this->final_amount = 0;
            return;
        }

        $gatewayCurrency = $this->getSelectedGatewayCurrency();

        if (!$gatewayCurrency) {
            return;
        }

        $result = DepositServices::calculateFinalAmount((float) $this->amount, $gatewayCurrency);

        $this->charge = $result['charge'];
        $this->final_amount = $result['final_amount'];
    }

    /**
     * Obtiene el gateway currency seleccionado (con caché)
     *
     * @return GatewayCurrency|null
     */
    protected function getSelectedGatewayCurrency()
    {
        if (!$this->selectedGatewayCurrency) {
            $this->selectedGatewayCurrency = GatewayCurrency::with('gateway')
                ->find($this->gateway_currency_id);
        }

        return $this->selectedGatewayCurrency;
    }

    /**
     * Guarda el depósito en la base de datos
     * 
     * Valida el monto, crea el depósito usando DepositServices,
     * y redirige a la lista de depósitos.
     *
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function save()
    {
        $user = Auth::user();
        $customer = $user->customer;

        // Validar que se haya seleccionado un gateway
        if (!$this->gateway_currency_id) {
            Flash::error(__('Please select a payment gateway'));
            return;
        }

        // Validar que se haya ingresado un monto
        if (!$this->amount || $this->amount <= 0) {
            Flash::error(__('Please enter a valid amount'));
            return;
        }

        $gatewayCurrency = $this->getSelectedGatewayCurrency();

        if (!$gatewayCurrency) {
            Flash::error(__('Invalid gateway selected'));
            return;
        }

        // Validar límites del gateway
        if (!DepositServices::validateAmount((float) $this->amount, $gatewayCurrency)) {
            Flash::error(__('Amount must be between :min and :max', [
                'min' => $gatewayCurrency->min_amount,
                'max' => $gatewayCurrency->max_amount,
            ]));
            return;
        }

        try {
            // Crear el depósito
            $deposit = DepositServices::createDeposit(
                $customer,
                $gatewayCurrency,
                (float) $this->amount
            );

            Flash::success(__('Deposit created successfully'));

            // Redirigir a la lista de depósitos
            return $this->redirectRoute('deposits.index', navigate: true);

        } catch (\Exception $e) {
            Flash::error(__('Error creating deposit: :error', ['error' => $e->getMessage()]));
            return;
        }
    }

    /**
     * Renderiza el componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // Obtener todos los gateways activos con sus monedas
        $gatewayCurrencies = GatewayCurrency::whereHas('gateway', function ($query) {
            $query->where('is_active', true);
        })->with('gateway')->get();

        info(print_r($gatewayCurrencies, true));

        // Obtener el gateway seleccionado
        $selectedGateway = $this->getSelectedGatewayCurrency();

        return view('livewire.create-deposit', [
            'gatewayCurrencies' => $gatewayCurrencies,
            'selectedGateway' => $selectedGateway,
        ]);
    }
}
