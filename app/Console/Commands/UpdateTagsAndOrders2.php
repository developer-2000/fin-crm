<?php

namespace App\Console\Commands;

use App\Models\Order;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateTagsAndOrders2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_tags_2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update tags';

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
        $client = new GuzzleHttpClient();
        $ordersArray = Order::whereBetween('time_created', ['2018-09-01 00:00:00', '2018-12-31 17:59:59'])
            ->where('service', '!=', 'sending')
            ->where('entity', 'order')
            ->where('partner_id', 2)
            ->whereIn('partner_oid', [
                57179,
                57158,
                57068,
                57066,
                57061,
                57044,
                57031,
                56782,
                56781,
                56778,
                56730,
                56724,
                56128,
                56126,
                56122,
                56117
            ]);
        $ordersArray->chunk(50, function ( $orders ) use ( $client ) {
            $response = [];
            foreach ($orders as $order) {
                try {
                    $response = $client->request('GET', 'https://umgid.com/api/order?order_id=' . $order->partner_oid . '&hash=3168d91ceb3a0ceb66ad1fbf9df186c0');
                    if ($response) {
                        if ($response->getStatusCode() == 200) {
                            $results = json_decode($response->getBody()->getContents(), true);

                            if ($results && $results['offer_id']) {
                                $offer = \App\Models\Offer::firstOrCreate([
                                    'offer_id' => $results['offer_id'],
                                    'partner_id' => 2,
                                ], ['name' => $results['offer_name']]);
                                $order->offer_id = $offer->id;
                                $order->save();
                                $this->updateOfferProducts($offer->id, $results['offer_id']);
                                echo $order->id . "\n";
                            }
                            foreach ($results as $key => $value) {
                                if (in_array($key, [
                                        'tag_source',
                                        'tag_medium',
                                        'tag_campaign',
                                        'tag_term',
                                        'tag_content'
                                    ]) && !empty($value)) {
                                    $tag = \App\Models\Tag::firstOrCreate([
                                        'name' => $key,
                                        'partner_id' => $order->partner_id,
                                        'value' => $value
                                    ]);
                                    if ($tag) {
                                        if ($key == 'tag_source') {
                                            $order->tag_source = $tag->id;
                                        }
                                        if ($key == 'tag_medium') {
                                            $order->tag_medium = $tag->id;
                                        }
                                        if ($key == 'tag_campaign') {
                                            $order->tag_campaign = $tag->id;
                                        }
                                        if ($key == 'tag_term') {
                                            $order->tag_term = $tag->id;
                                        }
                                        if ($key == 'tag_content') {
                                            $order->tag_content = $tag->id;
                                        }
                                        $order->save();
                                        echo $order->id . "\n";
                                    }
                                }
                            }

                        }
                    }
                } catch (RequestException $exception) {
                    Storage::append('tags-exceptions.log', $order->id . ' - ' . $exception->getCode()
                    );
                }
            }
        });
    }

    public function updateOfferProducts( $id, $offerId )
    {
        $offerProducts = [
            ['3582', '751'],
            ['3287', '74'],
            ['3562', '736'],
            ['3561', '730'],
            ['3558', '729'],
            ['3557', '727'],
            ['3560', '726'],
            ['3556', '725'],
            ['3554', '724'],
            ['3547', '719'],
            ['3534', '711'],
            ['3533', '710'],
            ['3506', '708'],
            ['3486', '703'],
            ['3485', '702'],
            ['3480', '700'],
            ['3479', '699'],
            ['3477', '697'],
            ['3474', '696'],
            ['3476', '695'],
            ['3473', '692'],
            ['3467', '689'],
            ['3465', '685'],
            ['3464', '682'],
            ['3462', '681'],
            ['3450', '677'],
            ['3439', '672'],
            ['3418', '668'],
            ['3392', '664'],
            ['3244', '660'],
            ['3311', '660'],
            ['3358', '655'],
            ['3335', '654'],
            ['3097', '654'],
            ['3319', '650'],
            ['3500', '637'],
            ['3293', '637'],
            ['3256', '612'],
            ['3249', '611'],
            ['3288', '610'],
            ['3357', '606'],
            ['3483', '584'],
            ['3092', '583'],
            ['3310', '583'],
            ['3116', '572'],
            ['3451', '563'],
            ['3103', '536'],
            ['3112', '457'],
            ['3126', '432'],
            ['3298', '432'],
            ['3258', '410'],
            ['3481', '408'],
            ['3323', '407'],
            ['3228', '407'],
            ['3478', '3965'],
            ['3419', '3961'],
            ['3408', '3957'],
            ['3409', '3956'],
            ['3375', '3949'],
            ['3383', '3929'],
            ['3106', '391'],
            ['3412', '3862'],
            ['3271', '3760'],
            ['3378', '3700'],
            ['3501', '3699'],
            ['3363', '367'],
            ['3356', '366'],
            ['3114', '362'],
            ['3324', '362'],
            ['3302', '356'],
            ['3110', '356'],
            ['3502', '3543'],
            ['3355', '340'],
            ['3143', '340'],
            ['3330', '339'],
            ['3124', '339'],
            ['3102', '334'],
            ['3269', '333'],
            ['3535', '333'],
            ['3279', '322'],
            ['3248', '321'],
            ['3122', '303'],
            ['3265', '297'],
        ];

        $upd = [];
        foreach ($offerProducts as $product) {
            if ($product[1] == $offerId) {
                $upd[] = $product[0];
            }
        }

        if ($upd) {
            DB::table('offers_products')
                ->whereIn('offer_id', $upd)
                ->where('offer_id', '!=', $id)
                ->update([
                    'offer_id' => $id
                ]);
        }
    }
}

