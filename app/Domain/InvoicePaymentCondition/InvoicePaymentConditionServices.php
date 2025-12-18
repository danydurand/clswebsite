<?php

namespace App\Domain\InvoicePaymentCondition;

use App\Models\InvoicePaymentCondition;

class InvoicePaymentConditionServices
{

    public static function otherAreNotDefault(int $paymentConditionId): void
    {
        InvoicePaymentCondition::where('id', '!=', $paymentConditionId)
            ->update(['is_default' => 0]);
    }




}
