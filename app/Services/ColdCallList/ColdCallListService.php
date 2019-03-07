<?php
namespace App\Services\ColdCallList;

use Illuminate\Http\Request;
use App\Models\ColdCallList;
use App\Models\ColdCallFile;
use App\Services\PhoneCorrection\PhoneCorrectionService;

class ColdCallListService
{
    public function __construct()
    {
        //code ...
    }

    public function create(Request $request):bool
    {
        if (! $this->validate($request)) {
            return false;
        }

        $list = $request->all();
        $coldCallFile = ColdCallFile::create(
                  [
                    'file_name' => $list['file_name'],
                    'status' => 'inactive',
                    'comment' => $list['comment'],
                    'company_id' => $list['company'],
                    'geo' => $list['country']
                  ]
              );

        $excelCsvData = json_decode($request->get('excel_csv_data'), true);
        if ($list['header'] === 1) {
            $excelCsvData = array_slice($excelCsvData, 1);
        }

        $this->setColdCallList($excelCsvData, $coldCallFile, $request->fields);
        return true;
    }

    private function setColdCallList($excelCsvData, $coldCallFile, $fields)
    {
        foreach ($excelCsvData as $key => $value) {
            $array = $this->getSerializeData($value, $fields);

            $correctPhone = (new PhoneCorrectionService)
                          ->customCorrectionForCountry(
                            $coldCallFile->geo,
                            $array['телефон']
                          );

            if(!isset($phones[$correctPhone[0]]))
            $list = ColdCallList::create(
                  [
                    'cold_call_file_id' => $coldCallFile->id,
                    'proc_status' => 1,
                    'phone_number' => json_encode($correctPhone, JSON_UNESCAPED_UNICODE),
                    'add_info' => json_encode($array, JSON_UNESCAPED_UNICODE)
                  ]
                );
            $phones[$correctPhone[0]] = 'phone';
        }
    }

    private function getSerializeData($data, $fields)
    {
        foreach (config('app.db_fields_cold_call_table') as $index => $field) {
            $res = array_search($field, $fields);
            if (!empty($res) || $res === 0) {
                if ($field !== 'дополнительная инфа') {
                    $array[$field] = $data[array_search($field, $fields)];
                }
            }
            $newArray = [];
            foreach ($fields as $key2 => $value) {
                if ($value == 'дополнительная инфа') {
                    $newArray[$key2] = $value;
                    foreach ($newArray as $key3 => $r) {
                        if (isset($data[$key3])) {
                            $infoArray[$key3] = $data[$key3];
                        }
                    }
                }
            }
        }
        $array['инфа'] = array_values($infoArray);

        return $array;
    }

    private function validate(Request $request)
    {
        if (! $request->isMethod('post')) {
            exit("post");
            return false;
        }

        $phone = array_search('телефон', $request->input('fields'));
        if (! empty($phone) || $phone !== 0) {
            exit("stop");
            return false;
        }

        return true;
    }
}
