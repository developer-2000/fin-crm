<?php

namespace App\Http\Controllers;  

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Order;

class CountryController extends BaseController
{
    /**
     * поиск видимых стран по селекту
     */
    public static function findByWord(Request $request, Country $countryModel)
    {
        $term = trim($request->q);

        if (empty($term)) {
            return \Response::json([]);
        }

        /*
        $countries = $countryModel->searchCountryByWord($term);
        $formatted_countries = [];
        foreach ($countries as $country) {
            $formatted_countries[] = ['id' => $country->code, 'text' => $country->name];
        }
        return \Response::json($formatted_countries);
        */

        $countries = Country::langCountries();
        $return = [];
        foreach ($countries as $code => $name) {
            if (stripos($name, $term) !== false) {
                $return[] = ['id' => $code, 'text' => $name];
            }
        }
        return \Response::json($return);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function all(Request $request) {

        //(new Country)->updateExchangeRates();

        $countries = Country::orderBy('sequence')->get();
        return view('countries.all', ['countries' => $countries]);
    }

    /**
     * @param Request $request
     */
    public function useChange(Request $request) {
        if (!$request->ajax()) {
            abort(403);
        }
        $code = $request->get('code', '');
        $country = Country::where('code', $code)->first();
        if($country) {
            $country->use = !$country->use;
            $country->save();
        }
    }

    /**
     * @param Request $request
     */
    // перетаскивание страны изменяет её порядок в очереди
    public function replace(Request $request) {
        if (!$request->ajax()) {
            abort(403);
        }
        $cur_code = $request->post('current', '');
        $prev_code = $request->post('prev', '');
        $countries = Country::whereIn('code', [$cur_code, $prev_code])->get()->keyBy('code');
        if ($countries && isset($countries[$cur_code])) {
            $cur_sequence = $countries[$cur_code]->sequence;
            if (isset($countries[$prev_code])) {
                $prev_sequence = $countries[$prev_code]->sequence;
                if ($prev_sequence > $cur_sequence) {
                    Country::where('sequence', '>', $cur_sequence)
                        ->where('sequence', '<=', $prev_sequence)
                        ->decrement('sequence');
                    Country::where('code', $cur_code)->update(['sequence' => $prev_sequence]);
                } elseif ($prev_sequence < $cur_sequence) {
                    Country::where('sequence', '>', $prev_sequence)
                        ->where('sequence', '<', $cur_sequence)
                        ->increment('sequence');
                    Country::where('code', $cur_code)->update(['sequence' => $prev_sequence + 1]);
                }
            } else {
                Country::where('sequence', '>=', 1)
                    ->where('sequence', '<=', $cur_sequence - 1)
                    ->increment('sequence');
                Country::where('code', $cur_code)->update(['sequence' => 1]);
            }
        }
    }

}
