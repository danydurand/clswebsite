<?php
use App\Models\Setting;
use App\Services\AuthUser;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Log;
use App\Services\NavigationServices;
use Illuminate\Support\Facades\Session;
use App\Services\NotificationServices as NS;


if (!function_exists('low')) {

    function low(string $value, int $length): string
    {
        return strtolower(substr($value,0,$length));
    }
}

if (!function_exists('up')) {

    function up(?string $value, int $length): string
    {
        return $value ? strtoupper(substr($value, 0, $length)) : '';
    }
}

if (!function_exists('length')) {

    function length(string $value, int $length): string
    {
        return substr($value,0,$length);
    }
}


if (!function_exists('sett')) {

    function sett(string $name, string $returnType, $defaultValue = null)
    {
        $setting = Setting::findByName($name);
        if ($setting instanceof Setting) {
            switch ($returnType) {
                case 'int':
                case 'integer':
                    return (int) $setting->value;
                case 'float':
                case 'double':
                    return (float) $setting->value;
                case 'bool':
                case 'boolean':
                    return (bool) $setting->value;
                default:
                    return $setting->value;
            }
        } else {
            if (!is_null($defaultValue)) {
                switch ($returnType) {
                    case 'int':
                    case 'integer':
                        return (int) $defaultValue;
                    case 'float':
                    case 'double':
                        return (float) $defaultValue;
                    case 'bool':
                    case 'boolean':
                        return (bool) $defaultValue;
                    default:
                        return $defaultValue;
                }
            }
        }
        return null;
    }
}

if (!function_exists('successMess')) {

    function successMess(string $title, string $body, bool $persist=false)
    {
        NS::success($title, $body, $persist);
    }
}

if (!function_exists('dangerMess')) {

    function dangerMess(string $errorTitle, string $errorBody, bool $persist=true)
    {
        NS::danger($errorTitle, $errorBody, $persist);
    }
}

if (!function_exists('warningMess')) {

    function warningMess(string $errorTitle, string $errorBody='', bool $persist=true)
    {
        NS::warning($errorTitle, $errorBody, $persist);
    }
}

if (!function_exists('format_period')) {

    function format_period($endtime, $starttime): string
    {
        $duration = $endtime - $starttime;
        $hours = (int) ($duration / 60 / 60);
        $minutes = (int) ($duration / 60) - $hours * 60;
        $seconds = (int) $duration - $hours * 60 * 60 - $minutes * 60;
        return ($hours == 0 ? "00" : $hours) . ":" . ($minutes == 0 ? "00" : ($minutes < 10 ? "0" . $minutes : $minutes)) . ":" . ($seconds == 0 ? "00" : ($seconds < 10 ? "0" . $seconds : $seconds));
    }
}


if (!function_exists('navigationLinks')) {

    function navigationLinks(string $name, $record, string $page='view'): array
    {

        $navigationLinks = [];

        if (Session::has("$name-modKeys")) {

            // info('The session has modKeys for '.$name);

            $navServ = new NavigationServices();
            $modKeys = Session::get("$name-modKeys");

            $panelName = Filament::getCurrentPanel()->getId();

            $intPosi = array_search($record->id, $modKeys) + 1;
            $navLink = $navServ::navLinks("filament.$panelName.resources.$name.$page",$intPosi-1,$modKeys);

            $first = explode(', ',$navLink['F']);
            $frout = $first[0];
            $fpara = $first[1];

            $previ = explode(', ',$navLink['P']);
            $prout = $previ[0];
            $ppara = $previ[1];

            $next  = explode(', ',$navLink['N']);
            $nrout = $next[0];
            $npara = $next[1];

            $last  = explode(', ',$navLink['L']);
            $lrout = $last[0];
            $lpara = $last[1];

            $navigationLinks = [
                Action::make('first')
                    ->label(fn () => config('box2go.nav.labels') ? __('First'): '')
                    ->labeledFrom('sm')
                    ->icon('heroicon-o-chevron-double-left')
                    ->keyBindings(config('box2go.nav.key-binding.first'))
                    ->tooltip(__('Go to First'))
                    ->url(route($frout, $fpara))
                    ->disabled(fn ():bool => $record->id == $fpara),
                Action::make('previous')
                    ->label(fn () => config('box2go.nav.labels') ? __('Prev'): '')
                    ->labeledFrom('sm')
                    ->keyBindings(config('box2go.nav.key-binding.previous'))
                    ->tooltip(__('Go to Previous'))
                    ->icon('heroicon-o-chevron-left')
                    ->url(route($prout, $ppara))
                    ->disabled(fn ():bool => $record->id == $fpara),
                Action::make('next')
                    ->label(fn () => config('box2go.nav.labels') ? __('Next'): '')
                    ->labeledFrom('sm')
                    ->icon('heroicon-o-chevron-right')
                    ->iconPosition('after')
                    ->keyBindings(config('box2go.nav.key-binding.next'))
                    ->tooltip(__('Go to Next'))
                    ->url(route($nrout, $npara))
                    ->disabled(fn ():bool => $record->id == $lpara),
                Action::make('last')
                    ->label(fn () => config('box2go.nav.labels') ? __('Last'): '')
                    ->labeledFrom('sm')
                    ->icon('heroicon-o-chevron-double-right')
                    ->iconPosition('after')
                    ->keyBindings(config('box2go.nav.key-binding.last'))
                    ->tooltip(__('Go to Last'))
                    ->url(route($lrout, $lpara))
                    ->disabled(fn ():bool => $record->id == $lpara),
            ];

        }

        return $navigationLinks;
    }
}

if (!function_exists('forgetModelsKeys')) {

    function forgetModelsKeys()
    {
        // info('Deleting model keys !!!');
        $keysToDelete = [];

        foreach (session()->all() as $key => $value) {
            // info('key: '.$key);
            if (str_ends_with($key, '-modKeys')) {
                // info('key deletable');
                $keysToDelete[] = $key;
            }
        }

        foreach ($keysToDelete as $key) {
            // info('deleting key: '.$key);
            session()->forget($key);
        }

    }
}

if (!function_exists('nl2br2')) {

    function nl2br2($string) : string
    {
        return str_replace(array("\r\n", "\r", "\n"), ",", $string);
    }
}


if (!function_exists('t')) {

    function t($message,$type='info')
    {
        // Must log the messages or not
        $doit = config('lottery.log.doit');
        if ($doit) {
            // Users' messages that must be logged
            $logUsers = config('lottery.log.users', ['master']);
            $username = AuthUser::getInstance()->user()->name;
            if (in_array($username, $logUsers)) {
                // Logging the User messages
                Log::$type($message);
            }
        }
    }
}

if (!function_exists('tw')) {

    function tw($message)
    {
        // Other possible types: error, warning, addMessage
        t($message,'warning');
    }
}

if (!function_exists('te')) {

    function te($message)
    {
        // Other possible types: error, warning, addMessage
        t($message,'error');
    }
}

if (!function_exists('calculateTime')) {

    function calculateTime($endtime, $starttime): string
    {
        $duration = $endtime - $starttime;
        $hours = (int) ($duration / 60 / 60);
        $minutes = (int) ($duration / 60) - $hours * 60;
        $seconds = (int) $duration - $hours * 60 * 60 - $minutes * 60;
        return ($hours == 0 ? "00" : $hours) . ":" . ($minutes == 0 ? "00" : ($minutes < 10 ? "0" . $minutes : $minutes)) . ":" . ($seconds == 0 ? "00" : ($seconds < 10 ? "0" . $seconds : $seconds));
    }
}

if (!function_exists('yesNoOptions')) {

    function yesNoOptions(): array
    {
        return [true => __('Yes'), false => __('No')];
    }
}

if (!function_exists('onlyCharsAndNumbers')) {

    function onlyCharsAndNumbers(string $string): string
    {
        return preg_replace("/[^A-Za-z0-9\s]/", "", $string);
    }

}

if (!function_exists('deleteSpecialChars')) {

    function deleteSpecialChars(string $string): string
    {
        $specialCharsMap = [
            'ñ' => 'N', 'á' => 'A', 'é' => 'E', 'í' => 'I', 'ó' => 'O', 'ú' => 'U',
            'Ñ' => 'N', 'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            '"' => '', '&' => 'y', chr(13) => '', '\\' => '', '*' => '', "'" => '',
            ';' => '',
        ];
        $newLine = array("\r\n", "\n", "\r");
        $string = str_replace($newLine, "", $string);
        return strtr($string, $specialCharsMap);
    }

}

if (!function_exists('cleanString')) {

    function cleanString(string $string): string
    {
        $string = onlyCharsAndNumbers($string);
        $string = deleteSpecialChars($string);
        $string = mb_convert_encoding($string, 'UTF-8', 'latin1');
        return $string;
    }

}
