<?php

namespace App\Repositories\Integrations;

use App\Models\Api\Ninjaxpress\NinjaxpressKey;
use GuzzleHttp\Client as GuzzleHttpClient;

class NinjaxpressRepository
{
    public static function generateAccessToken( $data )
    {
        $key = NinjaxpressKey::findOrFail($data->key_id);
        $target = $key->target;
        if ($target) {
            $client = new GuzzleHttpClient();

            $response = $client->request('POST', 'https://api.ninjavan.co/ID/2.0/oauth/access_token', [
           // $response = $client->request('POST','https://api-sandbox.ninjavan.co/SG/2.0/oauth/access_token', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    "client_id" => (string)$key->client_id,
                    "client_secret" => (string)$key->client_secret,
                    "grant_type" => 'client_credentials',
                ]
            ]);

            $responseContent = json_decode($response->getBody()->getContents());
            $statusCode = json_decode($response->getStatusCode());
            if ($statusCode == 200) {
                $key->access_token = $responseContent->access_token;
                $key->expires = $responseContent->expires;
                $key->token_type = $responseContent->token_type;
                $key->expires_in = $responseContent->expires_in;
                if ($key->save()) {
                    $html = view('integrations.ninjaxpress.edit-key-table', ['keyData' => $key])->render();
                    return ['success' => true, 'html' => $html];
                }
            } else {
                return ['success' => false, 'errors' => $statusCode . ' ' . $responseContent];
            }
        }
    }

    public static function generateHmac( $data )
    {

        $key = NinjaxpressKey::findOrFail($data->key_id);
        if ($key->client_secret) {
            $data = file_get_contents('php://input');
            $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $key->client_secret, true));
            if ($calculated_hmac) {
                $key->hmac = $calculated_hmac;
                if ($key->save()) {
                   // $html = view('integrations.ninjaxpress.edit-key-table', ['keyData' => $key])->render();
                    return ['success' => true];
                }
            } else {
                return ['success' => false, 'errors' => 'Внутрення ошибка сервера!'];
            }
        }
    }
}