<?php

namespace App\Services\PhoneCorrection\Interfaces;

interface IPhoneCorrectionHandler
{
    /**
     * Обработка номера телефона
     *
     * @param string $phone
     *
     * @return string
     */
    public function handle($phone);
}