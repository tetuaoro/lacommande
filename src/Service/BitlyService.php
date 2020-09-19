<?php

namespace App\Service;

class BitlyService
{
    protected $env;
    protected $token;

    public function __construct($env, $token)
    {
        $this->env = $env;
        $this->token = $token;
    }

    public function bitThis(string $link, string $title = '', string $id = null)
    {
        $data = [
            'long_url' => $link,
            'title' => $title,
            'tags' => [
                'lacommande',
                'ariifood',
                $this->env,
            ],
        ];
        $payload = json_encode($data);

        $header = [
            'Authorization: Bearer '.$this->token,
            'Content-Type: application/json',
            'Content-Length: '.strlen($payload),
        ];

        $ch = curl_init($id ? 'https://api-ssl.bitly.com/v4/bitlinks/'.$id : 'https://api-ssl.bitly.com/v4/bitlinks');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $id ? 'PATCH' : 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);

        return json_decode($result, true);
    }
}
