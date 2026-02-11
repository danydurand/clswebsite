<?php

namespace App\Domain\Deposit;

use App\Models\Deposit;
use App\Models\Bank;
use App\Models\User;
use App\Models\Event;
use App\Models\Customer;
use App\Classes\PResponse;
use App\Services\AuthUser;
use Illuminate\Support\Str;
use App\Models\GatewayCurrency;
use App\Domain\Bet\BetTypeEnum;
use App\Domain\Bet\BetStatusEnum;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\Auth;
use App\Domain\Customer\CustomerServices;
use App\Domain\FinancialTransaction\TrxTypeEnum;

/**
 * Servicio de lógica de negocio para depósitos
 * 
 * Este servicio contiene toda la lógica de negocio relacionada con
 * los depósitos de clientes, incluyendo cálculos de comisiones,
 * validaciones, y procesamiento de depósitos.
 */
class DepositServices
{

    /**
     * Calcula el monto final del depósito incluyendo comisiones
     * 
     * Calcula la comisión basándose en la comisión fija y porcentual
     * del gateway seleccionado, y retorna el monto final a pagar.
     * 
     * Fórmula:
     * - charge = fixed_charge + (amount * percent_charge / 100)
     * - final_amount = amount + charge
     *
     * @param float $amount Monto base del depósito
     * @param GatewayCurrency $gatewayCurrency Gateway seleccionado
     * @return array ['charge' => float, 'final_amount' => float]
     */
    public static function calculateFinalAmount(float $amount, GatewayCurrency $gatewayCurrency): array
    {
        // Calcular comisión porcentual
        $percentCharge = ($amount * $gatewayCurrency->percent_charge) / 100;

        // Calcular comisión total (fija + porcentual)
        $charge = $gatewayCurrency->fixed_charge + $percentCharge;

        // Calcular monto final
        $finalAmount = $amount + $charge;

        return [
            'charge' => round($charge, 2),
            'final_amount' => round($finalAmount, 2),
        ];
    }

    /**
     * Crea un nuevo depósito
     * 
     * Crea un registro de depósito en la base de datos con estado "Pending"
     * para depósitos manuales. Genera un código de transacción único.
     *
     * @param Customer $customer Cliente que realiza el depósito
     * @param GatewayCurrency $gatewayCurrency Gateway seleccionado
     * @param float $amount Monto del depósito
     * @return Deposit
     * @throws \Exception Si hay un error al crear el depósito
     */
    public static function createDeposit(Customer $customer, GatewayCurrency $gatewayCurrency, float $amount): Deposit
    {
        // Calcular comisiones y monto final
        $calculation = self::calculateFinalAmount($amount, $gatewayCurrency);

        // Crear el depósito usando el método create para evitar errores de readonly
        $deposit = Deposit::create([
            'customer_id' => $customer->id,
            'gateway_id' => $gatewayCurrency->gateway_id,
            'amount' => $amount,
            'charge' => $calculation['charge'],
            'rate' => $gatewayCurrency->rate ?? 1.0,
            'final_amount' => $calculation['final_amount'],
            'trx' => self::generateCode(),
            'deposit_date' => now(),
            'status' => DepositStatusEnum::Pending,
            'from_api' => 0,
            'payment_try' => 0,
            'detail' => [
                'gateway_name' => $gatewayCurrency->gateway->name,
                'currency' => $gatewayCurrency->currency,
                'symbol' => $gatewayCurrency->symbol,
                'created_by' => $customer->name,
            ],
        ]);

        Log::info('Deposit created', [
            'deposit_id' => $deposit->id,
            'customer_id' => $customer->id,
            'amount' => $amount,
            'trx' => $deposit->trx,
        ]);

        return $deposit;
    }

    /**
     * Crea un depósito manual con comprobante
     * 
     * Crea un depósito manual que requiere aprobación del administrador.
     * Incluye el archivo de comprobante y los datos del formulario dinámico.
     *
     * @param Customer $customer Cliente que realiza el depósito
     * @param GatewayCurrency $gatewayCurrency Gateway seleccionado
     * @param float $amount Monto del depósito
     * @param array $formData Datos del formulario dinámico
     * @param \Illuminate\Http\UploadedFile|null $proofFile Archivo de comprobante
     * @return Deposit
     * @throws \Exception Si hay un error al crear el depósito
     */
    public static function createManualDeposit(
        Customer $customer,
        GatewayCurrency $gatewayCurrency,
        float $amount,
        array $formData,
        $proofFile = null
    ): Deposit {
        // Calcular comisiones y monto final
        $calculation = self::calculateFinalAmount($amount, $gatewayCurrency);

        // Guardar archivo de comprobante si existe
        $proofPath = null;
        if ($proofFile) {
            $proofPath = $proofFile->store('deposit-proofs', 'public');
        }

        // Crear el depósito con estado Pending
        $deposit = Deposit::create([
            'customer_id' => $customer->id,
            'gateway_id' => $gatewayCurrency->gateway_id,
            'amount' => $amount,
            'charge' => $calculation['charge'],
            'rate' => $gatewayCurrency->rate ?? 1.0,
            'final_amount' => $calculation['final_amount'],
            'trx' => self::generateCode(),
            'deposit_date' => now(),
            'status' => DepositStatusEnum::Pending,
            'from_api' => 0,
            'payment_try' => 0,
            'detail' => [
                'gateway_name' => $gatewayCurrency->gateway->name,
                'currency' => $gatewayCurrency->currency,
                'symbol' => $gatewayCurrency->symbol,
                'created_by' => $customer->name,
                'proof_file' => $proofPath,
                'form_data' => $formData,
                'submitted_at' => now()->toDateTimeString(),
            ],
        ]);

        Log::info('Manual deposit created', [
            'deposit_id' => $deposit->id,
            'customer_id' => $customer->id,
            'amount' => $amount,
            'trx' => $deposit->trx,
            'has_proof' => $proofFile !== null,
        ]);

        // TODO: Notificar al administrador

        return $deposit;
    }

    /**
     * Valida que el monto esté dentro de los límites del gateway
     * 
     * Verifica que el monto ingresado esté entre el mínimo y máximo
     * permitido por el gateway seleccionado.
     *
     * @param float $amount Monto a validar
     * @param GatewayCurrency $gatewayCurrency Gateway seleccionado
     * @return bool True si el monto es válido, false en caso contrario
     */
    public static function validateAmount(float $amount, GatewayCurrency $gatewayCurrency): bool
    {
        return $amount >= $gatewayCurrency->min_amount && $amount <= $gatewayCurrency->max_amount;
    }



    /**
     * Genera un código único para el depósito
     * 
     * Genera un código UUID único que se utiliza como identificador
     * de transacción (trx) para el depósito.
     *
     * @return string Código único generado
     */
    public static function generateCode(): string
    {
        return (string) Str::uuid();
    }



}
