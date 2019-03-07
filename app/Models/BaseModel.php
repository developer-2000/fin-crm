<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as GuzzleHttpClient;

class BaseModel extends Model
{

    protected $contextOptions = [
        "ssl" => [
            "verify_peer"      => false,
            "verify_peer_name" => false
        ]
    ];
    
    /**
     * Ключ Api Elastix
     */
    protected $keyApiElastix = 'b04OVjNt6NTTSwJtyifvKXWhHUKufy3Gcs76KV5DKWMlpIW4';

    protected $keyApiElastix2 = '6nNTtFgdSjKtlBEGR0XJMSHPEzIz0uhr45aJiNrvysnRg8Kk6y';

    protected $keyApiElastix3 = '6nNTtFgdSjKtlBEGR0XJMSHPEzIz0uhr45aJiNrvysnRg8Kk6y';

    protected $keyApiElastix4 = 'b04OVjNt6NTTSwJtyifvKXWhHUKufy3Gcs76KV5DKWMlpIW4';

    /**
     * URL Api Elastix
     */
    protected $urlApiElastix = 'https://sip.badvps.com/api.php?';

    protected $urlApiElastix2 = 'https://sip.badvps.com/api2.php?';

    protected $urlApiElastix3 = 'http://pbx.badvps.com/api2.php?';

    protected $urlApiElastix4 = 'http://pbx.badvps.com/api.php?';
    
    /**
     * Ключ Api Univermag 
     */
    protected $keyApiUnivermag = '656d843afe9703fd4eb061ea95e6ab64';
    
    /**
     * URL Api Univermag
     */
    protected $urlApiUnivermag = 'https://univer-mag.com/crm/';
    
    protected function postRequestToTheUrl($data = array()) 
    {
        return stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($data),
            ],
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ]); 
    }
    
    protected function getRequestToTheUrl() {
        return stream_context_create([
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
            'http' => [
                'ignore_errors' => true,
            ],
        ]);
    }
    
    protected function apiElastixProcessing($action, $get = [], $post = [])
    {
        $data = $this->apiProcessing($get, $post);
        $client = new GuzzleHttpClient();

        $response = $client->request($get ? 'GET' : 'POST', $this->urlApiElastix4 . 'action=' . $action . '&key=' . $this->keyApiElastix . $data['stringGet'], $get ? $get : $post);
      //  $result = file_get_contents($this->urlApiElastix4 . 'action=' . $action . '&key=' . $this->keyApiElastix . $data['stringGet'], false, $data['param']);
        $result = json_decode($response->getBody()->getContents());
        return $result;
    }

    protected function apiElastixProcessing2($action, $get = false, $post = false)
    {

        $data  = $this->apiProcessing($get, $post);

        $result = file_get_contents($this->urlApiElastix3 . 'action=' . $action . '&key=' . $this->keyApiElastix2 . $data['stringGet'], false, $data['param']);

        return json_decode($result);
    }

    protected function apiElastixProcessing3($action, $get = false, $post = false)
    {
        $data = $this->apiProcessing($get, $post);
        $result = file_get_contents($this->urlApiElastix3 . 'action=' . $action . '&key=' . $this->keyApiElastix3 . $data['stringGet'], false, $data['param']);
        return json_decode($result);
    }

    protected function apiElastixProcessing4($action, $get = false, $post = false)
    {
        $data = $this->apiProcessing($get, $post);
        $result = file_get_contents($this->urlApiElastix4 . 'action=' . $action . '&key=' . $this->keyApiElastix4 . $data['stringGet'], false, $data['param']);
        return json_decode($result);
    }

    protected function apiElastixProcessing5($action, $get = false, $post = false)
    {
        $data = $this->apiProcessing($get, $post);
        $result = file_get_contents($this->urlApiElastix2 . 'action=' . $action . '&key=' . $this->keyApiElastix2 . $data['stringGet'], false, $data['param']);
        return json_decode($result);
    }
    
    protected function apiUniverMagProcessing($action, $get = false, $post = false) 
    {
        $data = $this->apiProcessing($get, $post);
        $result = file_get_contents($this->urlApiUnivermag . $action . '?hash=' . $this->keyApiUnivermag . $data['stringGet'], false, $data['param']); 
        return json_decode($result);
    }
    
    protected function postbackPrcessing($data) 
    {
        $data = json_decode($data);
        $param = $data->post ? $this->postRequestToTheUrl($data->post) : $this->getRequestToTheUrl();
        $result = file_get_contents($data->url, false, $param);     
        return json_decode($result);   
    }
    
    private function apiProcessing($get, $post) 
    {
        $data = [];
        $data['stringGet'] = '';

        if ($get) 
            foreach ($get as $gk => $gv)
                $data['stringGet'] .= '&' . $gk . '=' . $gv;
        $data['param'] = $this->getRequestToTheUrl();
        if ($post) $data['param'] = $this->postRequestToTheUrl($post);
        return $data;
    }

    /* Query MySQL */

	protected function insert($data = array(), $lastId = false) 
	{
        if (!$data) die('error query');
        return ($lastId) ? DB::table($this->table)->insertGetId($data) : DB::table($this->table)->insert($data);
	}
    
    
//    protected function updateData($data = array(), $where = false)
//    {
//        if ($data) {
//            $result = DB::table($this->table);
//            if ($where && is_array($where)) {
//                foreach ($where as $fieldWhere => $valueWhere) {
//                    $result->where($fieldWhere, $valueWhere);
//                }
//            } else die('error query');
//            return $result->update($data);
//        } die('error query');
//    }
    
}
