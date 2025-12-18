<?php

namespace App\Domain\Rate;

use App\Models\Rate;

class RateServices
{

    public static function otherAreNotPublic(int $rateId): void
    {
        Rate::where('id', '!=', $rateId)
            ->update(['is_public' => false]);
    }




}
