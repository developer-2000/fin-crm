<?php

namespace App\Services\PhoneCorrection\Handlers;

class RuPhoneCorrectionHandler extends AbstractPhoneCorrectionHandler
{
    protected static $codes = [
        73 => 11,
        74 => 11,
        75 => 11,
        78 => 11,
        79 => 11,
    ];

    public function handle($phone)
    {
        parent::handle($phone);

        if (!(strlen($phone) == array_shift(static::$codes) && substr($phone, 0, 1) == 8)) {
           throw new \Exception('Invalid phone');
        }

        $this->phone = substr_replace($phone, 7, 0, 1);
    }
}