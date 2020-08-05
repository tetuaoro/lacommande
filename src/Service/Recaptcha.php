<?php

namespace App\Service;

class Recaptcha
{
    private $key;
    private $secret;

    public function __construct($recapkey, $recapsecret)
    {
        $this->key = $recapkey;
        $this->secret = $recapsecret;
    }

    public function captchaverify($response)
    {
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'secret' => $this->secret,
            'response' => $response,
        ]);
        $response_ = curl_exec($ch);
        curl_close($ch);

        return json_decode($response_);
    }
}
