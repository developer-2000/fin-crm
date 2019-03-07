<?php

function dateProcessing($time)
{
    if ($time) {
        $hour = floor($time / 3600);
        $sec = $time - ($hour * 3600);
        $min = floor($sec / 60);
        $sec = $sec - ($min * 60);
        if ($hour) {
            if ($hour < 10) {
                $hour = '0' . $hour;
            }
            $hour .= ":";
        } else {
            $hour = '';
        }
        if ($min < 10) {
            $min = '0' . $min;
        }
        if ($sec < 10) {
            $sec = '0' . $sec;
        }
        return $hour . $min . ":" . $sec;
    }
    return '00:00:00';

}

function renderInputDataForModeration($data)
{
    if (is_array($data) || is_object($data)) {
        foreach ($data as $key => $value) {
            echo '<div class="value">' . $key . ' :';
            renderInputDataForModeration($value);
            echo '</div>';
        }
    } else {
        echo "<span>" . $data . '</span>';
    }
}

/**
 * Возвращает сумму прописью
 * @author runcore
 * @uses morph(...)
 */
function num2str($num, $currency) {
    $nul='ноль';
    $ten=array(
        array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
        array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
    );
    $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
    $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
    $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
    $unit=array( // Units
                 array('копейка' ,'копейки' ,'копеек',	 1),
                 array('рубль'   ,'рубля'   ,'рублей'    ,0),
                 array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
                 array('миллион' ,'миллиона','миллионов' ,0),
                 array('миллиард','милиарда','миллиардов',0),
    );
    //
    list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
    $out = array();
    if (intval($rub)>0) {
        foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
            if (!intval($v)) continue;
            $uk = sizeof($unit)-$uk-1; // unit key
            $gender = $unit[$uk][3];
            list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
            // mega-logic
            $out[] = $hundred[$i1]; # 1xx-9xx
            if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
            else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
            // units without rub & kop
            if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
        } //foreach
    }
    else $out[] = $nul;
    $out[] = $currency; // rub /*morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2])*/
    $out[] = ''; // kop /*$kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2])*/
    return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}

/**
 * Склоняем словоформу
 * @ author runcore
 */
function morph($n, $f1, $f2, $f5) {
    $n = abs(intval($n)) % 100;
    if ($n>10 && $n<20) return $f5;
    $n = $n % 10;
    if ($n>1 && $n<5) return $f2;
    if ($n==1) return $f1;
    return $f5;
}

function hex2RGB($hexStr)
{
    list($r, $g, $b) = sscanf($hexStr, "#%02x%02x%02x");
    return [
        'r' => $r,
        'g' => $g,
        'b' => $b,
    ];
}

function rgb2hex($rgbArray)
{
    $hex = '';
    if (is_array($rgbArray) || count($rgbArray)) {
        foreach ($rgbArray as $color) {
            $hexItem = dechex($color);
            $colors[] = strlen($hexItem) > 1 ? $hexItem : '0' . $hexItem;
        }
        $hex = '#' . implode('', $colors);
    }
    return $hex;
}

function colorForLabel($hexStr, $k = 5)
{
    $rgb = hex2RGB($hexStr);
    foreach ($rgb as &$value) {
        $value += $k;
        if ($value > 255) $value = 255;
        if ($value < 0) $value = 0;
    }

    return rgb2hex($rgb);
}


function parseDate($date)
{
  if($date){
    return Carbon\Carbon::parse($date)->format("Y-m-d H:i:s");
  }
  return null;
}

/**
 * @param $value (example: 'menu.Orders')
 * @return mixed
 * function special for registration new translate
 */
function registration_trans($value)
{
    return $value;
}

/**
 * @param $value (example: 'Orders')
 * @param $group (example: 'general')
 * @return string
 * return translate if exists
 */
function checkTranslate($group, $value)
{
    $translation = trans($group . '.' . $value);
    if ($translation != $group . '.' . $value) {
        return $translation;
    }

    return $value;
}

function transNotSaved(string $str):string
{
  return trans('alerts.data-block').' "'. trans($str).'" '
        . trans('alerts.data-not-saved');
}

function transSaved(string $str):string
{
  return trans('alerts.data-block').' "'. trans($str).'" '
        . trans('alerts.successfully-saved');
}

function transChanged(string $str):string
{
  return trans('alerts.data-block').' "'. trans($str)
        .'" '. trans('alerts.data-successfully-changed');
}

function transNotChanged(string $str):string
{
  return trans('alerts.data-block').' "'. trans($str)
        .'" '. trans('alerts.data-successfully-changed');
}
