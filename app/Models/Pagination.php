<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Pagination extends Model
{
    /* Пагинация */
    function getPagination($currentPage, $count, $countOnePage) {
        if (!$currentPage) {
            $currentPage = 1; 
        }
        $countPage = (int)ceil($count / $countOnePage);
        $countShowPage = 5; 
        $part = (int)floor($countShowPage / 2); 
        $page = [];  
        if ($countPage <= $countShowPage) {
            $maxShow = $countPage;
        } else {
            $maxShow = $countShowPage;
        }    
        /* Получаем пагинацию */
        for ($i = 1; $i <= $countPage; $i++) {
            /* Пагинация в начале */
            if ($currentPage - $part <= 0) { 
                if ($i <= $maxShow) { 
                    $page[] = $i;
                }    
            /* Пагинация в конце */
            } elseif ($currentPage + $part >= $countPage) {  
                if ($i > $countPage - $maxShow && $i <= $countPage) {
                    $page[] = $i;
                }        
            /* Пагинация в середине */    
            } else { 
                if ($i >= $currentPage - $part && $i <= $currentPage + $part)  {
                    $page[] = $i;
                }    
            }
        }
        return [$page, $currentPage, $countPage, $this->getParamPagination()];
    }

    private function getParamPagination() 
    {
        $str = '';
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            foreach($_GET as $gk => $gv) {
                if (!($gk == '_url' || $gk == 'page')) {
                    $str .= $gk . '=' . $gv . '&';
                }
            }
        } else {
            $get = strstr($_SERVER['HTTP_REFERER'], '?');
            if ($get !== false) {
                $get = explode('&', substr($get, 1));
                if ($get) {
                    foreach ($get as $g) {
                        $res = explode('=', $g);
                        if ($res[0] != 'page') {
                            $str .= $res[0] . '=' . $res[1] . '&';
                        }
                    }
                }
            } 
        }
        return ($str) ? '?' . substr($str, 0, -1) : $str;
    }
}
