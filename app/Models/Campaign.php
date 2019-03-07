<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Models\OffersTarget;
use \App\Models\User;

class Campaign extends BaseModel
{
    protected $table = 'company_elastix';

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'campaign_id');
    }

    public function coldCallFile()
    {
        return $this->hasMany(ColdCallFile::class, 'campaign_id');
    }


    /**
     * Получаем компанию на прозвон
     * @param int $source Источник
     * @param int $country Страна
     * @param int $owner Владелец
     * @param int $offer Оффер
     * @return int
     */
    function getElastixCompanyByFilter($source, $country, $owner, $offer)
    {
        $result = DB::table($this->table)->select('id', 'source', 'country', 'owner', 'offer')
            ->orderBy('position')
            ->get();
        foreach ($result as $r) {
            $success = [];
            if ($r->source) {
                $r->source = json_decode($r->source, true);
                foreach ($r->source as $rs) {
                    if ($source == $rs[0] && $rs[1] == 1) {
                        $success[] = 1;
                        continue;
                    } elseif ($source == $rs[0] && $rs[1] == 0) {
                        continue;
                    }
                }
            } else {
                $success[] = 1;
            }
            if ($r->country) {
                $r->country = json_decode($r->country, true);
                foreach ($r->country as $rc) {
                    if ($country == $rc[0] && $rc[1] == 1) {
                        $success[] = 1;
                    } elseif ($country == $rc[0] && $rc[1] == 0) {
                        continue;
                    }
                }
            } else {
                $success[] = 1;
            }
            if ($r->owner) {
                $r->owner = json_decode($r->owner, true);
                foreach ($r->owner as $row) {
                    if ($owner == $row[0] && $row[1] == 1) {
                        $success[] = 1;
                    } elseif ($owner == $row[0] && $row[1] == 0) {
                        continue;
                    }
                }
            } else {
                $success[] = 1;
            }
            if ($r->offer) {
                $r->offer = json_decode($r->offer, true);
                foreach ($r->offer as $rof) {
                    if ($offer == $rof[0] && $rof[1] == 1) {
                        $success[] = 1;
                    } elseif ($offer == $rof[0] && $rof[1] == 0) {
                        continue;
                    }
                }
            } else {
                $success[] = 1;
            }
            if (count($success) == 4) {
                return $r->id;
            }
        }
        return 0;
    }

    function getNameCompanyElastix()
    {
        return collect(
            DB::table($this->table)->select('id', 'name', 'position', 'company_id')
                ->orderBy('position', 'asc')
                ->get()
        )->keyBy('id');
    }

    /* Получаем все компании */
    function getAllCompanyElastix()
    {
        return DB::table($this->table)->get();
    }

    function getAllCompanyElastixForColdCalls()
    {
        return DB::table('company_elastix')
            ->where('company_id', '!=', NULL)
            ->paginate(20);
    }

    function getAllCompanyElastixForColdCallsByUserId($companyId)
    {
       $companies = DB::table('company_elastix')
            ->where('company_id', $companyId)
            ->paginate(20);
        return $companies;
    }

    function existCompanyElastix($id)
    {
        return (bool)DB::table($this->table)->where('id', $id)
            ->value('id');
    }

    function updatePositionCompany($data)
    {
        if ($data['function'] == 1 && $data['position'] > 1) {
            $newPosition = $data['position'] - 1;
            DB::table($this->table)->where('position', $newPosition)
                ->update([
                    'position' => $data['position']
                ]);
            DB::table($this->table)->where('id', $data['id'])
                ->update([
                    'position' => $newPosition
                ]);
            return true;
        } elseif ($data['function'] == 0) {
            $newPosition = $data['position'] + 1;
            DB::table($this->table)->where('position', $newPosition)
                ->update([
                    'position' => $data['position']
                ]);
            DB::table($this->table)->where('id', $data['id'])
                ->update([
                    'position' => $newPosition
                ]);
            return true;
        }
        return false;
    }

    function addToBase($data)
    {
        $result = $this->createData($data);
        if (count($result['error']) == 0) {
            DB::table($this->table)->insert($result['data']);
        }
        return $result;
    }

    function createData($data, $id = 0)
    {
        $message = [];
        $errorsEX = [];
        $arrayCallTime = [];
        $arrayCountry = [];
        $arrayOffer = [];
        $arraySource = [];
        $position = DB::table($this->table)->max('position');
        if ($id == 0) {
            $nameFromDB = DB::table($this->table)->where('name', $data['name'])->get();
        } else {
            $nameFromDB = DB::table($this->table)->where('name', $data['name'])
                ->where('id', '!=', $id)
                ->get();
        }

        if (count($nameFromDB) == null) {
            if (strlen($data['name']) < 3) {
                $message[] = 'Слишком короткое название';
                $errorsEX[] = 'name';
            }
            if (strlen($data['name']) > 50) {
                $message[] = 'Слишком большое название';
                $errorsEX[] = 'name';
            }
        } else {
            $message[] = 'Такое название уже существет';
            $errorsEX[] = 'name';
        }
        if ($data['status'] === 'true') {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }
        if ($data['learning'] === 'true') {
            $data['learning'] = 1;
        } else {
            $data['learning'] = 0;
        }

        if ($data['min_call_count']) {
            if (!(int)$data['min_call_count']) {
                $message[] = 'Поле должно быть числовым';
                $errorsEX[] = 'min_call_count';
            }
        }

        foreach ($data['callTime'] as $key => $value) {
            if ($value > 0) {
                $arrayCallTime[$key + 1] = $value;
            } else {
                $errorsEX[] = $key + $value;
            }
        }
        $arrayData = [
            'name' => $data['name'],
            'call_count' => count($data['callTime']),
            'company_id' => isset($data['company_id']) ? intval($data['company_id']) : 0,
            'cron_status' => $data['status'],
            'call_time' => json_encode($arrayCallTime),
            'position' => $position + 1,
            'learning' => $data['learning'],
            'min_call_count'    => $data['min_call_count']
        ];
        if (!$id) {
            $data['position'] = $position + 1;
        }
        if (isset($data['country'])) {
            foreach ($data['country'] as $value1) {
                $countryFromDB = DB::table('countries')->where('id', $value1[0])->get();
                if ($countryFromDB != null) {
                    $arrayCountry[] = [
                        $value1[0],
                        $value1[1],
                    ];
                } else {
                    $errorsEX[] = $value1;
                }
            }
            $arrayData['country'] = json_encode($arrayCountry);
        } else {
            $arrayData['country'] = '';
        }
        if (isset($data['company_id'])) {
            $arrayData['company_id'] = intval($data['company_id']);
        }
        if (isset($data['offer'])) {
            foreach ($data['offer'] as $value2) {
                $offerFromDB = DB::table('offers')->where('id', $value2[0])->get();
                if ($offerFromDB != null) {
                    $arrayOffer[] = [
                        $value2[0],
                        $value2[1]
                    ];
                } else {
                    $errorsEX[] = $value2;
                }
            }
            $arrayData['offer'] = json_encode($arrayOffer);
        } else {
            $arrayData['offer'] = '';
        }
        if (isset($data['source'])) {
            foreach ($data['source'] as $value3) {
                $sourceFromDB = DB::table('projects')->where('id', $value3[0])->get();
                if ($sourceFromDB != null) {
                    $arraySource[] = [
                        $value3[0],
                        $value3[1]
                    ];
                } else {
                    $errorsEX[] = $value3;
                }
            }
            $arrayData['source'] = json_encode($arraySource);
        } else {
            $arrayData['source'] = '';
        }
        if (!$errorsEX) {
            if (!$id) {
                $acaq = $this->apiElastixProcessing2('addCompanyAndQueues', [
                    'name' => $data['name']
                ]);
                if ($acaq->status == 200) {
                    $arrayData['id'] = $acaq->id;
                } else {
                    $errorsEX[] = $acaq->message;
                }
            }
        }
        return [
            'error' => $errorsEX,
            'message' => $message,
            'data' => $arrayData,
        ];
    }

    function companyElatixUpdate($id)
    {
        return DB::table($this->table)->where('id', $id)->first();
    }

    function companyElastixUpdateAjax($data, $id)
    {
        $result = $this->createData($data, $id);
        if (count($result['error']) == 0) {
            DB::table($this->table)->where('id', $id)->update($result['data']);
        }
        return $result;

    }
}
