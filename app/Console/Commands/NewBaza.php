<?php

namespace App\Console\Commands;

use App\Models\Api\Posts\Novaposhta;
use App\Models\Api\Posts\Viettel;
use App\Models\ColdCallList;
use Illuminate\Console\Command;
use App\Models\Variables;
use App\Services\PhoneCorrection;
class NewBaza extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'new_baza';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'new baza';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $fileData = file('public/upload/bs5_RU_5391_03.06.2014-13.01.2015 - Sheet1.csv');
            foreach ($fileData as $line) {
                $row = explode(";", $line);
                $array = [
                    'фио'                 => $row[1],
                    'телефон'             => + preg_replace( '/[^0-9]/', '',  trim($row[0])),
                    'дополнительная инфа' => $row[2] .', '. str_replace(array("\n", "\r"), ' ',  $row[3])
                ];
                $exist = ColdCallList::where('phone_number', '['. str_replace(array(" ", "  "), '',  $row[0]).',0]')->where('cold_call_file_id', 190)->first();

                if (!$exist) {
                    $phone = (new PhoneCorrection\PhoneCorrectionService)->customCorrectionForCountry('ru',
                        preg_replace( '/[^0-9]/', '', trim($row[0])));
                    $mPhone = [intval($phone[0]), $phone[1]];
                    ColdCallList::create(['cold_call_file_id' => 190,
                        'phone_number' => json_encode($mPhone),
                        'proc_status' => 1,
                        'add_info'          => json_encode($array, JSON_UNESCAPED_UNICODE)]);
                }
            }
        } catch (\Exception $exception) {
            echo $exception;
        }
    }
}
