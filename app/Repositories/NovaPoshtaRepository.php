<?php

namespace App\Repositories;

use App\Models\Api\Posts\Novaposdddhta;
use GuzzleHttp as GuzzleHttpClient;

class NovaPoshtaRepository
{
    public static function getData()
    {
        $client = new GuzzleHttpClient();
        $response = $client->request('POST', 'https://api.novaposhta.ua/v2.0/json/', [
            'json' => [

                "modelName"        => "Counterparty",
                "calledMethod"     => "getCounterparties",
                "methodProperties" => [
                    "CounterpartyProperty" => "Sender",
                    "Page"                 => "1"],
                "apiKey"           => Novaposdddhta::getKey()
            ]]);

        $getCounterparties = json_decode($response->getBody()->getContents());
    }
}