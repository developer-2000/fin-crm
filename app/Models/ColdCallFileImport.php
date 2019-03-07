<?php

namespace App\Models;

use Illuminate\Support\Facades\Input;

class ColdCallFileImport extends \Maatwebsite\Excel\Files\ExcelFile
{

    /*settings for csv files*/
    protected $delimiter = ',';
    protected $enclosure = '"';
    protected $lineEnding = '\r\n';

    public function getFile()
    {
        return storage_path('exports') . '/';
    }

    public function getFilters()
    {
        return [
            'chunk'
        ];
    }

}