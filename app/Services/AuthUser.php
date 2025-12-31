<?php
namespace App\Services;

use App\Models\User;

class AuthUser
{
    private static $instance = null;
    private $user;

    private function __construct()
    {
        $this->user = User::find(auth()->id());
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new AuthUser();
        }
        return self::$instance;
    }

    public function user()
    {
        return $this->user;
    }

    public static function userType()
    {
        return self::getInstance()->user()->type;
    }
}
