<?php

namespace App\Domain\Post;

use App\Models\Bet;
use App\Models\Bank;
use App\Models\User;
use App\Models\Event;
use App\Classes\PResponse;
use App\Services\AuthUser;
use Illuminate\Support\Str;
use App\Domain\Bet\BetTypeEnum;
use App\Domain\Bet\BetStatusEnum;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\Auth;
use App\Domain\Customer\CustomerServices;
use App\Domain\FinancialTransaction\TrxTypeEnum;

class PostServices
{

    public static function generateRouteName(string $prefix, ?string $title): string
    {

        if ($title === null) {
            return $prefix;
        }

        $cleanTitle = Str::before($title, ' - ');
        $words = explode(' ', trim($cleanTitle));

        $actions = [
            'list' => 'index',
            'view' => 'view',
            'edit' => 'edit',
            'create' => 'create',
            'new' => 'create',
        ];

        $action = 'index';
        $resourceWords = $words;

        // Check first word
        $firstWord = Str::lower($words[0] ?? '');
        if (isset($actions[$firstWord])) {
            $action = $actions[$firstWord];
            array_shift($resourceWords);
        } elseif (count($words) > 0) {
            // Check last word
            $lastWord = Str::lower(end($words));
            if (isset($actions[$lastWord])) {
                $action = $actions[$lastWord];
                array_pop($resourceWords);
            }
        }

        $resourceName = implode(' ', $resourceWords);
        $resourceName = Str::of($resourceName)->plural()->lower()->slug();

        if (!Str::endsWith($prefix, '.')) {
            $prefix .= '.';
        }

        return $prefix . $resourceName . '.' . $action;
    }



}
