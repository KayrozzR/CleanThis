<?php

namespace App\Service;

use DateTime;
use DateTimeImmutable;

class JWTService{

    //We generate the JWT
    /**
     * generation of the jwt token
     *
     * @param array $header
     * @param array $payload
     * @param string $secret
     * @param integer $validity
     * @return string
     */
public function generate(array $header, array $payload, string $secret, int $validity = 10800): string{

    if($validity > 0){
        $now = new DateTimeImmutable();
        $exp = $now->getTimestamp() + $validity;

        $payload['iat'] = $now->getTimestamp();
        $payload['exp'] = $exp;
    }

    //We encode in base 64
    $base64Header = base64_encode(json_encode($header));
    $base64Payload = base64_encode(json_encode($payload));

    //we clean the encode values (we clean the +,/ and = )
    $base64Header = str_replace(['+','/','='], ['-','_',''], $base64Header);
    $base64Payload = str_replace(['+','/','='], ['-','_',''], $base64Payload);

    //we generate the signature
    $secret= base64_encode($secret);

    $signature = hash_hmac('sha256',$base64Header. '.'.$base64Payload, $secret, true);
    $base64Signature = base64_encode($signature);
    $base64Signature = str_replace(['+','/','='], ['-','_',''], $base64Signature);

    //We create the token
    $jwt= $base64Header. '.'.$base64Payload. '.'. $base64Signature;

    return $jwt;

    }

    //We verify that the token is valid in terms of form
    public function isValid(string $token):bool
    {
        return preg_match(
            '/^[a-zA-z0-9\-\_\=]+\.[a-zA-z0-9\-\_\=]+\.[a-zA-z0-9\-\_\=]+$/',
            $token
        ) ===1;
    }

    //we get the payload
    public function getPayload( string $token): array
    {
        // we separate the token
        $array =explode ('.',$token);

        //we decode the payload
        $payload =json_decode(base64_decode($array[1]), true);

        return $payload;
    }

    //we get the header
    public function getHeader( string $token): array
    {
        // we separate the token
        $array =explode ('.',$token);

        //we decode the payload
        $header =json_decode(base64_decode($array[0]), true);

        return $header;
    }
    //we verifye if the token is still valid
    public function isExpired(string $token): bool{
        $payload = $this->getPayload($token);
        $now = new DateTimeImmutable();
        return $payload['exp']< $now->getTimestamp();
    }

    //we verify the signature of the token 
    public function check(string $token, string $secret)
    {
        //we get the header and the payload
        $header= $this->getHeader($token);
        $payload= $this->getPayload($token);

        //We verify if the signature is ok by generating a new token
        $verifToken = $this->generate($header, $payload, $secret, 0);
        return $token === $verifToken;
    }
}

