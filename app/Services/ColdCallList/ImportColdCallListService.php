<?php
namespace App\Services\ColdCallList;

use App\Models\Country;
use App\Models\Company;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory as ImportExcel;

class ImportColdCallListService
{
    private $request;
    private $dataImport;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function execute():bool
    {
        $path = $this->request->file('excel_csv_file')->getRealPath();
        $this->dataImport['fileName'] = $this->request->file('excel_csv_file')->getClientOriginalName();
        $header = true;

        $spreadsheet = ImportExcel::load($path);
        $excelCsvData = $spreadsheet->getSheet(0)->toArray(); //getActiveSheet

        foreach ($excelCsvData as $item) {
            $row = array_filter($item, 'strlen');
            if (!empty($row)) {
                $this->dataImport['excelCsvRows'][] = $row;
            }
        }

        if (count($excelCsvData) > 0) {
            if ($this->request->has('header')) {
                foreach ($excelCsvData[0] as $key => $value) {
                    $this->dataImport['csv_header_fields'] = $value;
                }
            }
        } else {
            return false;
        }

        if ($this->request->has('header')) {
            $this->dataImport['excelCsvDataSliced'] = array_slice($excelCsvData, 1, 4);
        } else {
            $this->dataImport['excelCsvDataSliced'] = array_slice($excelCsvData, 0, 4);
        }
        return true;
    }

    public function getData():array
    {
        return $this->dataImport;
    }
}
