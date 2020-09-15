<?php

namespace App\Service;

class BitlyService
{
    private $env;

    public function __construct($env)
    {
        $this->env = $env;
    }

    public function create_url(string $link, string $title = '')
    {
        $apiv4 = 'https://api-ssl.bitly.com/v4/bitlinks';
        $genericAccessToken = '11bbc353066e23d1c0733634ae12cc250b2b62c6';

        $data = [
            'long_url' => $link,
            'title' => $title,
            'tags' => [
                'lacommande',
                $this->env,
            ],
        ];
        $payload = json_encode($data);

        $header = [
            'Authorization: Bearer '.$genericAccessToken,
            'Content-Type: application/json',
            'Content-Length: '.strlen($payload),
        ];

        $ch = curl_init($apiv4);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);

        return json_decode($result, true);
    }

    public function update_url(string $id, string $link, string $title = '')
    {
        $apiv4 = 'https://api-ssl.bitly.com/v4/bitlinks/'.$id;
        $genericAccessToken = '11bbc353066e23d1c0733634ae12cc250b2b62c6';

        $data = [
            'long_url' => $link,
            'title' => $title,
        ];
        $payload = json_encode($data);

        $header = [
            'Authorization: Bearer '.$genericAccessToken,
            'Content-Type: application/json',
            'Content-Length: '.strlen($payload),
        ];

        $ch = curl_init($apiv4);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);

        return json_decode($result, true);
    }
}
