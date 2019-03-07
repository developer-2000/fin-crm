<?php

namespace App\Models;

use App\Models\Api\Posts\Viettel;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client as GuzzleHttpClient;

class VietnamWard extends Model
{
    protected $fillable = ['ward_id', 'ward_name', 'district_id', 'district_value', 'district_name',
                           'province_id', 'province_name'];

    public static function loadWards()
    {
        $client = new GuzzleHttpClient();

        $responseWithWards = $client->request('GET', Viettel::API . '/api/setting/listallwards');
        $wards = json_decode($responseWithWards->getBody()->getContents(), true);
        $responseWithDistrics = $client->request('GET', Viettel::API . '/api/setting/listalldistrict');
        $districts = json_decode($responseWithDistrics->getBody()->getContents(), true);
        foreach ($districts as $district) {
            $districtsArray[$district['DISTRICT_ID']] = $district;
        }
        $responseWithProvinces = $client->request('GET', Viettel::API . '/api/setting/listallprovince');
        $provinces = json_decode($responseWithProvinces->getBody()->getContents(), true);

        foreach ($provinces as $province) {
            $provincesArray[$province['PROVINCE_ID']] = $province;
        }

        if ($wards) {
            foreach ($wards as $ward) {

                VietnamWard::updateOrCreate(
                    [
                        'ward_id'   => $ward['WARDS_ID'],
                        'ward_name' => $ward['WARDS_NAME']
                    ],
                    [
                        'district_id'    => $ward['DISTRICT_ID'],
                        'district_name'  => !empty($districtsArray[$ward['DISTRICT_ID']]['DISTRICT_NAME']) ? $districtsArray[$ward['DISTRICT_ID']]['DISTRICT_NAME'] : NULL,
                        'district_value' => !empty($districtsArray[$ward['DISTRICT_ID']]['DISTRICT_VALUE']) ? $districtsArray[$ward['DISTRICT_ID']]['DISTRICT_VALUE'] : NULL,
                        'province_id'    => !empty($districtsArray[$ward['DISTRICT_ID']]['PROVINCE_ID']) ? $districtsArray[$ward['DISTRICT_ID']]['PROVINCE_ID'] : NULL,
                        'province_name'  => !empty($provincesArray[$districtsArray[$ward['DISTRICT_ID']]['PROVINCE_ID']]['PROVINCE_NAME'])
                            ? $provincesArray[$districtsArray[$ward['DISTRICT_ID']]['PROVINCE_ID']]['PROVINCE_NAME'] : NULL,
                        'created_at'     => now(),
                        'updated_at'     => now()
                    ]
                );
            }
        }
    }
}
