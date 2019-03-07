<?php

namespace App\Console\Commands;

use App\Models\Order;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Support\Facades\Storage;

class UpdateTagsAndOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_tags';

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
        $ordersArray = Order::whereBetween('time_created', ['2018-09-01 00:00:00', '2018-12-27 17:59:59'])
            ->where('service', '!=', 'sending')
            ->where('entity', 'order')
            ->where('partner_id', 3);

        $ordersArray->chunk(10000, function ($orders) use ($client) {
            $response = [];
            foreach ($orders as $order) {
                try {
                    $response = $client->request('GET', 'https://univer-mag.com/cron/orders/dev/tags.php?hash=3168d91ceb3a0ceb66ad1fbf9df186c0&id=' . $order->partner_oid);
                    if ($response) {
                        if ($response->getStatusCode() == 200) {
                            $results = json_decode($response->getBody()->getContents(), true);

                            if ($results && $results['order_id']) {
                                foreach ($results as $key => $value) {
                                    if (in_array($key, [
                                            'tag_source',
                                            'tag_medium',
                                            'tag_campaign',
                                            'tag_term',
                                            'tag_content'
                                        ]) && !empty($value)) {
                                        $tag = \App\Models\Tag::firstOrCreate([
                                            'name'       => $key,
                                            'partner_id' => $order->partner_id,
                                            'value'      => $value
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
                    }

                } catch (RequestException $exception) {
                    Storage::append('tags-exceptions.log', $order->id . ' - ' . $exception->getCode()
                    );
                }
            }
        });
    }
}

