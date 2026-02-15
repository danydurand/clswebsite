<?php

namespace App\Domain\Withdrawal;

use App\Models\Withdrawal;
use App\Models\WithdrawMethod;
use App\Models\Customer;
use App\Models\Setting;
use App\Models\FinancialTransaction;
use App\Domain\FinancialTransaction\TrxTypeEnum;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Servicio de lógica de negocio para retiros
 * 
 * Este servicio contiene toda la lógica de negocio relacionada con
 * los retiros de clientes, incluyendo cálculos de comisiones,
 * validaciones, y procesamiento de retiros.
 * 
 * DIFERENCIAS CLAVE CON DEPÓSITOS:
 * - Las comisiones se RESTAN del monto (no se suman)
 * - El balance se descuenta al confirmar (no al aprobar)
 * - Los retiros rechazados requieren reembolso automático
 */
class WithdrawalServices
{
    /**
     * Calcula el monto final del retiro RESTANDO comisiones
     * 
     * Fórmula para retiros (diferente a depósitos):
     * - charge = fixed_charge + (amount * percent_charge / 100)
     * - after_charge = amount - charge
     * - final_amount = after_charge * rate
     *
     * @param float $amount Monto base del retiro
     * @param WithdrawMethod $method Método de retiro seleccionado
     * @return array ['charge' => float, 'after_charge' => float, 'final_amount' => float]
     */
    public static function calculateFinalAmount(float $amount, WithdrawMethod $method): array
    {
        // Calcular comisión porcentual
        $percentCharge = ($amount * $method->percent_charge) / 100;

        // Calcular comisión total (fija + porcentual)
        $charge = $method->fixed_charge + $percentCharge;

        // IMPORTANTE: En retiros se RESTA la comisión
        $afterCharge = $amount - $charge;

        // Calcular monto final en moneda destino
        $finalAmount = $afterCharge * $method->rate;

        return [
            'charge' => round($charge, 2),
            'after_charge' => round($afterCharge, 2),
            'final_amount' => round($finalAmount, 2),
        ];
    }

    /**
     * Valida que el monto esté dentro de los límites del método
     * 
     * Verifica que el monto ingresado esté entre el mínimo y máximo
     * permitido por el método de retiro seleccionado.
     *
     * @param float $amount Monto a validar
     * @param WithdrawMethod $method Método de retiro seleccionado
     * @return bool True si el monto es válido, false en caso contrario
     */
    public static function validateAmount(float $amount, WithdrawMethod $method): bool
    {
        return $amount >= $method->min_limit && $amount <= $method->max_limit;
    }

    /**
     * Valida que el monto esté dentro de los límites globales del sistema
     * 
     * Verifica que el monto esté dentro de los límites configurados
     * en la tabla settings (min-withdrawal y max-withdrawal).
     *
     * @param float $amount Monto a validar
     * @return bool True si el monto es válido, false en caso contrario
     */
    public static function validateGlobalLimits(float $amount): bool
    {
        $minWithdrawal = (float) Setting::findByName('min-withdrawal')?->value ?? 0;
        $maxWithdrawal = (float) Setting::findByName('max-withdrawal')?->value ?? PHP_FLOAT_MAX;

        return $amount >= $minWithdrawal && $amount <= $maxWithdrawal;
    }

    /**
     * Valida que el cliente tenga balance suficiente
     * 
     * Verifica que el cliente tenga balance suficiente para
     * realizar el retiro solicitado.
     *
     * @param Customer $customer Cliente que solicita el retiro
     * @param float $amount Monto del retiro
     * @return bool True si tiene balance suficiente, false en caso contrario
     */
    public static function validateBalance(Customer $customer, float $amount): bool
    {
        return $customer->balance >= $amount;
    }

    /**
     * Crea una solicitud de retiro (status = INITIATE)
     * 
     * Crea un registro de retiro en la base de datos con estado "Initiate".
     * NO descuenta balance en este paso. El balance se descuenta al confirmar.
     *
     * @param Customer $customer Cliente que solicita el retiro
     * @param WithdrawMethod $method Método de retiro seleccionado
     * @param float $amount Monto del retiro
     * @return Withdrawal
     * @throws \Exception Si hay un error al crear el retiro
     */
    public static function createWithdrawalRequest(
        Customer $customer,
        WithdrawMethod $method,
        float $amount
    ): Withdrawal {
        // Calcular comisiones y monto final
        $calculation = self::calculateFinalAmount($amount, $method);

        // Validar que quede algo después de las comisiones
        if ($calculation['after_charge'] <= 0) {
            throw new \Exception(__('Withdrawal amount must be sufficient for charges'));
        }

        // Crear el retiro con estado INITIATE
        $withdrawal = Withdrawal::create([
            'customer_id' => $customer->id,
            'withdraw_method_id' => $method->id,
            'amount' => $amount,
            'charge' => $calculation['charge'],
            'after_charge' => $calculation['after_charge'],
            'final_amount' => $calculation['final_amount'],
            'rate' => $method->rate ?? 1.0,
            'currency' => $method->currency,
            'trx' => self::generateCode(),
            'status' => WithdrawalStatusEnum::Initiate->value,
            'withdraw_information' => null,
            'admin_feedback' => null,
        ]);

        Log::info('Withdrawal request created', [
            'withdrawal_id' => $withdrawal->id,
            'customer_id' => $customer->id,
            'amount' => $amount,
            'trx' => $withdrawal->trx,
            'status' => WithdrawalStatusEnum::Initiate->value,
        ]);

        return $withdrawal;
    }

    /**
     * Confirma el retiro y descuenta balance (status = PENDING)
     * 
     * Confirma la solicitud de retiro, descuenta el balance del cliente
     * y crea la transacción financiera. Cambia el estado a PENDING.
     * 
     * IMPORTANTE: Este es el momento en que se descuenta el balance.
     *
     * @param Withdrawal $withdrawal Retiro a confirmar
     * @param array $withdrawInfo Información del formulario dinámico
     * @return Withdrawal
     * @throws \Exception Si el retiro no está en estado INITIATE o hay error
     */
    public static function confirmWithdrawal(
        Withdrawal $withdrawal,
        array $withdrawInfo
    ): Withdrawal {
        // Validar que esté en estado INITIATE
        if ($withdrawal->status !== WithdrawalStatusEnum::Initiate->value) {
            throw new \Exception(__('Only initiated withdrawals can be confirmed'));
        }

        // Obtener el cliente
        $customer = $withdrawal->customer;

        // Validar balance nuevamente (podría haber cambiado)
        if (!self::validateBalance($customer, $withdrawal->amount)) {
            throw new \Exception(__('Insufficient balance'));
        }

        DB::beginTransaction();
        try {
            // Actualizar el retiro
            $withdrawal->update([
                'status' => WithdrawalStatusEnum::Pending->value,
                'withdraw_information' => $withdrawInfo,
            ]);

            // DESCONTAR BALANCE
            $previousBalance = $customer->balance;
            $customer->balance -= $withdrawal->amount;
            $customer->save();

            // Crear transacción financiera (DÉBITO)
            FinancialTransaction::create([
                'customer_id' => $customer->id,
                'amount' => $withdrawal->amount,
                'post_balance' => $customer->balance,
                'charge' => $withdrawal->charge,
                'trx_type' => TrxTypeEnum::Debit->value,
                'trx' => $withdrawal->trx,
                'remark' => 'withdrawal_request',
                'details' => __('Withdrawal request via :method', [
                    'method' => $withdrawal->withdrawMethod->name
                ]),
            ]);

            DB::commit();

            Log::info('Withdrawal confirmed', [
                'withdrawal_id' => $withdrawal->id,
                'customer_id' => $customer->id,
                'amount' => $withdrawal->amount,
                'previous_balance' => $previousBalance,
                'new_balance' => $customer->balance,
            ]);

            // TODO: Notificar al administrador

            return $withdrawal->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error confirming withdrawal', [
                'withdrawal_id' => $withdrawal->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Aprueba el retiro (status = SUCCESS)
     * 
     * Aprueba la solicitud de retiro. Solo cambia el estado a SUCCESS.
     * NO modifica el balance (ya fue descontado al confirmar).
     * 
     * NOTA: Esta acción debe ser ejecutada por el administrador.
     *
     * @param Withdrawal $withdrawal Retiro a aprobar
     * @param string|null $adminFeedback Comentarios del administrador
     * @return Withdrawal
     * @throws \Exception Si el retiro no está en estado PENDING
     */
    public static function approveWithdrawal(
        Withdrawal $withdrawal,
        ?string $adminFeedback = null
    ): Withdrawal {
        // Validar que esté en estado PENDING
        if ($withdrawal->status !== WithdrawalStatusEnum::Pending->value) {
            throw new \Exception(__('Only pending withdrawals can be approved'));
        }

        // Actualizar estado
        $withdrawal->update([
            'status' => WithdrawalStatusEnum::Success->value,
            'admin_feedback' => $adminFeedback,
        ]);

        Log::info('Withdrawal approved', [
            'withdrawal_id' => $withdrawal->id,
            'customer_id' => $withdrawal->customer_id,
            'amount' => $withdrawal->amount,
        ]);

        // TODO: Notificar al cliente

        return $withdrawal->fresh();
    }

    /**
     * Rechaza el retiro (status = REJECT)
     * 
     * Rechaza la solicitud de retiro. REEMBOLSA el balance al cliente
     * y crea una transacción financiera de crédito.
     * 
     * IMPORTANTE: El balance debe ser reembolsado porque ya fue descontado.
     * 
     * NOTA: Esta acción debe ser ejecutada por el administrador.
     *
     * @param Withdrawal $withdrawal Retiro a rechazar
     * @param string $adminFeedback Razón del rechazo (obligatorio)
     * @return Withdrawal
     * @throws \Exception Si el retiro no está en estado PENDING
     */
    public static function rejectWithdrawal(
        Withdrawal $withdrawal,
        string $adminFeedback
    ): Withdrawal {
        // Validar que esté en estado PENDING
        if ($withdrawal->status !== WithdrawalStatusEnum::Pending->value) {
            throw new \Exception(__('Only pending withdrawals can be rejected'));
        }

        // Validar que se proporcione feedback
        if (empty($adminFeedback)) {
            throw new \Exception(__('Admin feedback is required when rejecting a withdrawal'));
        }

        $customer = $withdrawal->customer;

        DB::beginTransaction();
        try {
            // Actualizar estado
            $withdrawal->update([
                'status' => WithdrawalStatusEnum::Reject->value,
                'admin_feedback' => $adminFeedback,
            ]);

            // REEMBOLSAR BALANCE
            $previousBalance = $customer->balance;
            $customer->balance += $withdrawal->amount;
            $customer->save();

            // Crear transacción de reembolso (CRÉDITO)
            FinancialTransaction::create([
                'customer_id' => $customer->id,
                'amount' => $withdrawal->amount,
                'post_balance' => $customer->balance,
                'charge' => 0,
                'trx_type' => TrxTypeEnum::Withdraw->value,
                'trx' => $withdrawal->trx,
                'remark' => 'withdrawal_reject',
                'details' => __('Refund for rejected withdrawal: :feedback', [
                    'feedback' => $adminFeedback
                ]),
            ]);

            DB::commit();

            Log::info('Withdrawal rejected and refunded', [
                'withdrawal_id' => $withdrawal->id,
                'customer_id' => $customer->id,
                'amount' => $withdrawal->amount,
                'previous_balance' => $previousBalance,
                'new_balance' => $customer->balance,
                'reason' => $adminFeedback,
            ]);

            // TODO: Notificar al cliente

            return $withdrawal->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting withdrawal', [
                'withdrawal_id' => $withdrawal->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Genera un código único para el retiro
     * 
     * Genera un código UUID único que se utiliza como identificador
     * de transacción (trx) para el retiro.
     *
     * @return string Código único generado
     */
    public static function generateCode(): string
    {
        return (string) Str::uuid();
    }
}
