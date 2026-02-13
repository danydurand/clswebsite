<?php

namespace App\Domain\Withdrawal;

enum WithdrawalStatusEnum: string
{
    case Initiate = 'initiate';  // Solicitud creada, esperando confirmación del usuario
    case Pending = 'pending';    // Confirmada por usuario, esperando aprobación del admin
    case Success = 'success';    // Aprobada por el administrador
    case Reject = 'reject';      // Rechazada por el administrador

    /**
     * Obtiene la etiqueta traducida del estado
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::Initiate => __('Initiate'),
            self::Pending => __('Pending'),
            self::Success => __('Approved'),
            self::Reject => __('Rejected'),
        };
    }

    /**
     * Obtiene el color para el badge según el estado
     */
    public function getColor(): string
    {
        return match ($this) {
            self::Initiate => 'zinc',
            self::Pending => 'amber',
            self::Success => 'lime',
            self::Reject => 'red',
        };
    }

    /**
     * Verifica si el retiro puede ser cancelado
     */
    public function canBeCancelled(): bool
    {
        return $this === self::Initiate;
    }

    /**
     * Verifica si el retiro puede ser aprobado/rechazado
     */
    public function canBeProcessed(): bool
    {
        return $this === self::Pending;
    }
}
