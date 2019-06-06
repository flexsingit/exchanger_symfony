<?php
/**
 * Created by PhpStorm.
 * User: himanshur
 * Date: 6/6/19
 * Time: 11:57 AM
 */

namespace App\Service;

class ExchangeRates
{

    public $apiUrl = 'https://api.exchangeratesapi.io/latest/';

    public $base = 'USD';

    /**
     * returns the array when the api returns the status code 200
     * else returns false
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchLatestExchangeRates(){
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $this->apiUrl."?base=".$this->base);
        if($res->getStatusCode() == 200){
            return json_decode($res->getBody()->getContents(), true);
        } else {
            return false;
        }
    }
}