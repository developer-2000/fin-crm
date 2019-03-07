<?php

namespace App\Services\PhoneCorrection;

class PhoneCorrectionService
{
    const ERROR_NO = 0;
    const ERROR_YES = 1;

    private $codePhoneNumber = [
        'ua' => [ // Украина
                  //код оператора => длинна номера
                  '38050' => 12,
                  '38039' => 12,
                  '38063' => 12,
                  '38066' => 12,
                  '38067' => 12,
                  '38068' => 12,
                  '38073' => 12,
                  '38091' => 12,
                  '38092' => 12,
                  '38093' => 12,
                  '38094' => 12,
                  '38095' => 12,
                  '38096' => 12,
                  '38097' => 12,
                  '38098' => 12,
                  '38099' => 12,
                  //городские
                  '38043' => 12,
                  '38042' => 12,
                  '38049' => 12,
                  '38056' => 12,
                  '38062' => 12,
                  '38041' => 12,
                  '38061' => 12,
                  '38034' => 12,
                  '38044' => 12,
                  '38045' => 12,
                  '38052' => 12,
                  '38058' => 12,
                  '38059' => 12,
                  '38064' => 12,
                  '38033' => 12,
                  '38032' => 12,
                  '38051' => 12,
                  '38048' => 12,
                  '38053' => 12,
                  '38036' => 12,
                  '38069' => 12,
                  '38065' => 12,
                  '38054' => 12,
                  '38035' => 12,
                  '38031' => 12,
                  '38057' => 12,
                  '38055' => 12,
                  '38038' => 12,
                  '38047' => 12,
                  '38046' => 12,
                  '38037' => 12,
        ],
        'ru' => [ // Россия
                  '73' => 11,//3123456789
                  '74' => 11,
                  '75' => 11,
                  '78' => 11,
                  '79' => 11,
        ],
        'kz' => [ // Казахстан
                  '76' => 11,
                  '77' => 11,
        ],
        'by' => [ // Белоруссия
                  '375' => 12,
        ],
        'uz' => [ // Узбекистан
                  '998' => 12,
        ],
        'pl' => [ // Польша
                  '48' => 11,
        ],
        'kg' => [ // Киргизия
                  '996' => 12,
        ],
        'vn' => [ //Вьетнам
                  '84' => [11, 12],
        ],
        'in' => [ //индия
                  '91' => 12,
        ],
        'id' => [ //индонезия
                  '62' => [11, 12, 13, 14],
        ],
    ];

    /**
     * Проверяет корректность номера телефона
     * Если номер некорректный, он попадает в некорректные номера
     * @param $country
     * @param $phone
     * @return array
     */
    public function customCorrectionForCountry($country, $phone)
    {
        $country = mb_strtolower($country);
        try{
            if ($phone && isset($this->codePhoneNumber[$country])) {

                $phone = preg_replace('/[^0-9]/', '', $phone);

                if (!$phone) {
                    return [$phone, self::ERROR_YES];
                }

                $operatorCodes = $this->codePhoneNumber[$country];
                foreach ($operatorCodes as $operatorCode => $numberLength) {
                    if (strlen($operatorCode) < $numberLength && !is_array($numberLength)) {
                        $phoneLengthWithoutCode = $numberLength - strlen($operatorCode);
                        if ($phoneLengthWithoutCode > 0)
                            switch ($country) {
                                case 'ua':
                                    {
                                        if (strlen($phone) == 11 && substr($phone, 0, 2) == '80')
                                            $phone = '3' . $phone;
                                        elseif (strlen($phone) == 10 && $phone[0] == 0)
                                            $phone = '38' . $phone;
                                        elseif (strlen($phone) == 9)
                                            $phone = '380' . $phone;
                                        break;
                                    }
                                case 'ru' :
                                    {
                                        if (strlen($phone) == 11 && $phone[0] == 8 && ($phone[1] == 3 || $phone[1] == 4
                                                || $phone[1] == 5 || $phone[1] == 8 || $phone[1] == 9)
                                        )
                                            $phone = '7' . substr($phone, 1, strlen($phone));
                                        elseif (strlen($phone) == 10 && ($phone[0] == 3 || $phone[0] == 4
                                                || $phone[0] == 5 || $phone[0] == 8 || $phone[0] == 9)
                                        )
                                            $phone = '7' . $phone;
                                        else
                                            // return [$phone, self::ERROR_YES];
                                            break;
                                    }
                                case 'kz' :
                                    {
                                        if (strlen($phone) == 11 && $phone[0] == 8 && ($phone[1] == 6 || $phone[1] == 7))
                                            $phone = '7' . substr($phone, 1, strlen($phone));
                                        elseif (strlen($phone) == 10 && ($phone[0] == 6 || $phone[0] == 7))
                                            $phone = '7' . $phone;
                                        //else
                                        //return [$phone, self::ERROR_YES];
                                        break;
                                    }
                                case 'by':
                                    {
                                        if (strlen($phone) == 11 && substr($phone, 0, 2) == '75')
                                            $phone = '3' . $phone;
                                        elseif (strlen($phone) == 10 && $phone[0] == 5)
                                            $phone = '37' . $phone;
                                        elseif (strlen($phone) == 9)
                                            $phone = '375' . $phone;
                                        break;
                                    }
                                case 'uz':
                                    {
                                        if (strlen($phone) == 11 && substr($phone, 0, 2) == '98')
                                            $phone = '9' . $phone;
                                        elseif (strlen($phone) == 10 && $phone[0] == 8)
                                            $phone = '99' . $phone;
                                        elseif (strlen($phone) == 9)
                                            $phone = '998' . $phone;
                                        break;
                                    }
                                case 'pl':
                                    {
                                        if (strlen($phone) == 10 && $phone[0] = 8)
                                            $phone = '4' . $phone;
                                        elseif (strlen($phone) == 9)
                                            $phone = '48' . $phone;
                                        break;
                                    }
                                case 'kg':
                                    {
                                        if ($phone[0] == 0)
                                            $phone = substr($phone, 1, strlen($phone));
                                        if (strlen($phone) == 11 && substr($phone, 0, 2) == '96')
                                            $phone = '9' . $phone;
                                        elseif (strlen($phone) == 10 && $phone[0] == 6)
                                            $phone = '99' . $phone;
                                        elseif (strlen($phone) == 9)
                                            $phone = '996' . $phone;
                                        break;
                                    }

                                case 'in':
                                    {
                                        if (strlen($phone) == 10)
                                            $phone = '91' . $phone;
                                        elseif (strlen($phone) == 11 && $phone[0] == 1)
                                            $phone = '9' . $phone;
                                        break;
                                    }
                            }
                        if (preg_match('/^' . $operatorCode . '[0-9]{' . $phoneLengthWithoutCode . '}$/', $phone)) {
                            return [$phone, self::ERROR_NO];
                        }
                    }
                    if (strlen($operatorCode) < $numberLength && is_array($numberLength)) {
                        foreach ($numberLength as $item) {
                            $phoneLengthWithoutCodeArray = $item - strlen($operatorCode);
                            if ($phoneLengthWithoutCodeArray > 0) {
                                switch ($country) {
                                    case 'id':
                                        {
                                            if (strlen($phone) == 11 && $phone[0] != 0 && $phone[0] != 6 && $phone[1] != 2) {
                                                $phone = '62' . $phone;
                                            } elseif (strlen($phone) == 11 && $phone[0] == 0) {
                                                $phone = substr_replace($phone, '', 0, 1);
                                                $phone = '62' . $phone;
                                            } elseif (strlen($phone) == 12 && $phone[0] == 2) {
                                                $phone = '6' . $phone;
                                            } elseif (strlen($phone) == 12 && $phone[0] == 0) {
                                                $phone[0] = 2;
                                                $phone = '6' . $phone;
                                            } elseif (strlen($phone) == 13 && $phone[0] == 0) {
                                                $phone[0] = 2;
                                                $phone = '6' . $phone;
                                            } elseif (strlen($phone) == 10 && $phone[0] == 0) {
                                                $phone = substr_replace($phone, '', 0, 1);
                                                $phone = '62' . $phone;
                                            }
                                            break;
                                        }
                                    case 'vn':
                                        {
                                            if ($phone[0] == 0) {
                                                if (strlen($phone) == 10) {
                                                    $phone = "84" . substr($phone, 1);
                                                } elseif (strlen($phone) == 11) {
                                                    $phone = "84" . substr($phone, 1);
                                                }
                                            } elseif ($phone[0] != 0 && $phone[0] != 8 && $phone[1] != 4) {
                                                $phone = "84" . $phone;
                                            } elseif ($phone[0] == 8 && $phone[1] == 4 && $phone[2] == 0) {
                                                $phone = substr_replace($phone, '', 2, 1);
                                            }
                                            break;
                                        }
                                }
                                if (preg_match('/^' . $operatorCode . '[0-9]{' . $phoneLengthWithoutCodeArray . '}$/', $phone)) {
                                    return [$phone, self::ERROR_NO];
                                }
                            }
                        }
                    }
                }
            }
        }catch (\Exception $exception){
        }

        return [$phone, self::ERROR_YES];
    }

    /*public function make($phone, $country)
    {
        $country = mb_strtolower($country);
        $phone = preg_replace('~\D+~', '',  $phone);
        if ($phone && isset($this->codePhoneNumber[$country])) {
            $dataCode = $this->codePhoneNumber[$country];
            foreach ($dataCode as $code => $len) {
                $checkCode = preg_match('/^' . $code . '/', $phone);
                $checkLen = strlen($phone) == $len;
                $checkLenNoCode = strlen($phone) == $len - strlen($code);
                if ($checkCode && $checkLen) {
                    return [$phone, self::ERROR_NO];
                } elseif ($checkLenNoCode) {
                    return [$code . $phone, self::ERROR_NO];
                }
            }

            $methodName = 'customCorrectionForCountry' . mb_strtoupper($country);
            if (method_exists($this, $methodName)) {
                return $this->$methodName($phone, $dataCode);
            }
        }

        return [$phone, self::ERROR_YES];
    }

    private function customCorrectionForCountryUA($phone, $dataCode)
    {
        foreach ($dataCode as $code => $len) {
            if (strlen($phone) >= $len - strlen($code)) {
                $missingNumberCount = $positionNextNumber = $len - strlen($phone);
                if ($missingNumberCount > 0){
                    $newCode = substr($code, 0, $missingNumberCount);
                    $codeNextNumber = ((string)$code){$positionNextNumber};
                    if ($codeNextNumber == substr($phone, 0, 1)) {
                        return [$newCode . $phone, self::ERROR_YES];
                    }
                }
            }
        }

        return [$phone, self::ERROR_YES];
    }

    private function customCorrectionForCountryKZ($phone, $dataCode)
    {
        if (strlen($phone) == array_shift($dataCode) && substr($phone, 0, 1) == 8) {
            return [substr_replace($phone, 7, 0, 1), self::ERROR_NO];
        }

        return [$phone, self::ERROR_YES];
    }

    private function customCorrectionForCountryRU($phone, $dataCode)
    {
        if (strlen($phone) == array_shift($dataCode) && substr($phone, 0, 1) == 8) {
            return [substr_replace($phone, 7, 0, 1), self::ERROR_NO];
        }

        return [$phone, self::ERROR_YES];
    }

    private function customCorrectionForCountryVN($phone, $dataCode)
    {
        if (strlen($phone) == 10 || strlen($phone) == 11) {
            return [84 . $phone, self::ERROR_NO];
        }

        return [$phone, self::ERROR_YES];
    }*/
}