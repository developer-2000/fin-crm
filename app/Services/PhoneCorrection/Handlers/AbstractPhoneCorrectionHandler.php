<?php

namespace App\Services\PhoneCorrection\Handlers;

use App\Services\PhoneCorrection\Interfaces\IPhoneCorrectionHandler;

abstract class AbstractPhoneCorrectionHandler implements IPhoneCorrectionHandler
{
    /**
     * @var array
     */
    protected static $codes = [];

    /**
     * @var string
     */
    protected $countryCode;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @param string $phone
     */
    public function handle($phone)
    {

        //$country = mb_strtolower($country);
        $phone = preg_replace('~\D+~', '',  $phone);

        if (!$phone) {
            throw new \Exception('Invalid phone');
        }

        if (empty(static::$codes)) {
            throw new \Exception('Codes are not defined');
        }

        foreach (self::$codes as $code => $len) {
            $checkCode = preg_match('/^' . $code . '/', $phone);
            $checkLen = strlen($phone) == $len;
            $checkLenNoCode = strlen($phone) == $len - strlen($code);
            if ($checkCode && $checkLen) {
                $this->phone = $phone;
            } elseif ($checkLenNoCode) {
                $this->phone = $code . $phone;
            }
        }

        if (!$this->phone) {
            static::handle($phone);
        }
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
}