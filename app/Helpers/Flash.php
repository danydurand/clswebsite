<?php

namespace App\Helpers;

class Flash
{
    public static function success($message)
    {
        $messages = session()->get('flash_success', []);
        $messages[] = $message;
        session()->flash('flash_success', $messages);
    }

    public static function error($message)
    {
        $messages = session()->get('flash_error', []);
        $messages[] = $message;
        session()->flash('flash_error', $messages);
    }

    public static function warning($message)
    {
        $messages = session()->get('flash_warning', []);
        $messages[] = $message;
        session()->flash('flash_warning', $messages);
    }
}
