<?php
namespace Integrations;

class CynicoIntegration {

    private $client=NULL;
    private $secret_key = "";
    private $api_version = "1.0";

    public function __construct($client) {
        $this->client = new \GuzzleHttp\Client($client);
    }

    public function getHealth()
    {
        $res = $this->client->request('GET', 'https://api-dev.cyni.co/health');
        return json_decode($res->getBody());
    }

    public function getEasyPayStatus()
    {
        $response = [
            "name"=> "EasyPay API",
            "version"=> "v".$this->api_version,
            "status" => 0,
        ];

        $detail = $this->getHealth()->detail;
        if($detail=="ok")
            $response['status'] = 1;

        return $response;
    }

    public function sendSMS($parameters)
    {
        $body = [
            "sender" => '+40'.$parameters['recipient'],
            "operator" => "Digi",
            "recipient" => '+40'.$parameters['recipient'],
            "content" => 'EasyPay API Prefix: '.$parameters['prefix'].'. License Plate: '.$parameters['license_plate']
        ];

        $headers = [
            'Authorization' => $this->secret_key,
            'Content-Type'        => 'application/json'
        ];

        $res = $this->client->request('POST', 'https://api-dev.cyni.co/sms', [
            'headers' => $headers,
            'body' => json_encode($body)
        ]);

        return json_decode($res->getBody());
    }
}