<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $table = 'countries';
    protected $fillable = ['use'];
    private $exchangeRatesApi = 'https://openexchangerates.org/api/latest.json?app_id=0d4ee0efa54c4adfaf17867e1c9d0037';

    private static $allCountries = false;
    private static $langCountries = false;

    // используемые страны на нужном языке ['code' => 'name']
    // например, для селекта
    public static function langCountries() {
        if (!static::$allCountries) {
            static::$allCountries = self::where(['use' => 1])
                ->orderBy('sequence')
                ->pluck('name', 'code')
                ->toArray();
        }
        if (!static::$langCountries) {
            static::$langCountries = [];
            foreach (static::$allCountries as $code => $name) {
                static::$langCountries[$code] = /*'<img class="country-flag" src="/img/flags/' . mb_strtoupper($code) . '.png" /> ' . */
                    trans('countries.' . $name);
            }
        }
        return static::$langCountries;
    }

    /**
     * @return HasMany
     */
    public function scriptDetails()
    {
        return $this->hasMany(ScriptDetail::class, 'geo', 'code');
    }

    /**
     * @return HasMany
     */
    public function offers()
    {
        return $this->hasMany(Offer::class, 'geo', 'code');
    }

    /**
     * @return HasMany
     */
    public function coldCallFiles()
    {
        return $this->hasMany(ColdCallFile::class, 'geo', 'code');
    }

    /**
     * @return HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'geo', 'code');
    }


  /**
     * @return HasMany
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * @return HasMany
     */
    public function financeTransactions()
    {
        return $this->hasMany(Transaction::class, 'geo', 'code');
    }


    /**
     * Получаем все старны
     * @return object
     */
    function getAllCounties()
    {
        return DB::table($this->table)->where('use',1)->get();
    }

    function getAllCountryArray()
    {
        return collect(
            DB::table($this->table)
                ->select('id', 'name', 'code', 'currency')
                ->where('use', 1)
                ->get()
        )->keyBy('code');
    }

    /* Проверяем существует ли государство с таким Id */
    function existCountryId($id)
    {
        return (bool)DB::table($this->table)
            ->where('id', $id)
            ->where('use', 1)
            ->value('id');
    }

    /* Получаем название страны */
    function getCountryNameById($id)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('use', 1)
            ->value('name');
    }

    /*public function searchCountryByWord($term)
    {
        $countries = DB::table('countries')
            ->select('id', 'code', 'name')
            ->where('use', '=', 1)
            ->where('name', 'LIKE', '%' . $term . '%')->get();
        return $countries;
    }*/

    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateExchangeRates()
    {
        $client = new GuzzleHttpClient();
        $response = $client->request('GET', $this->exchangeRatesApi);
        if ($response) {
            $rates = json_decode($response->getBody()->getContents(), true)['rates'];
            foreach ($rates as $key => $rate) {
                Country::where('currency', $key)->update(['exchange_rate' => $rate]);
            }
        }

        return;
    }
}
