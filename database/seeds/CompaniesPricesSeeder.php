<?php

use Illuminate\Database\Seeder;

class CompaniesPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = DB::table('companies')->get();
        if ($companies) {
            foreach ($companies as $company) {
                $prices = json_decode($company->prices, true);
                $update = [];
                $newPrices = [];
                $newPrices['global'] = $prices;
                $newPrices['global']['type'] = $company->type;
                $update['prices'] = json_encode($newPrices);
                if ($company->billing_type) {
                    $billing = json_decode($company->billing, true);
                    $newBilling = [];
                    $newBilling['global']['type'] = $company->billing_type;
                    if ($company->billing_type == 'lead') {
                        $newBilling['global']['approve'] = $billing['billing_approve'];
                        $newBilling['global']['up_sell'] = $billing['billing_up_sell'];
                        $newBilling['global']['up_sell_2'] = $billing['billing_up_sell_2'];
                        $newBilling['global']['cross_sell'] = $billing['billing_cross_sell'];
                    } elseif ($company->billing_type == 'hour') {
                        $newBilling['global']['in_system'] = $billing['billing_in_system'];
                        $newBilling['global']['in_talk'] = $billing['billing_in_talk'];
                    }
                    $update['billing'] = json_encode($newBilling);
                }
                DB::table('companies')->where('id', $company->id)->update($update);
            }
        }
    }
}
