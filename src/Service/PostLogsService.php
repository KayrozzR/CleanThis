<?php

namespace App\Service;

use DateTime;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Exception;

use function Symfony\Component\Clock\now;

class PostLogsService
{

    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }
    
    public function postConnexionInfos(string $loggerName, string $message, string $level, array $data, String $userEmail): array
    {

        $eventTime = (new DateTime('now'))->format('Y-m-d\TH:i:s.uP');

        $infos = [
            'loggerName' => $loggerName,
            'user' => $userEmail,
            'message' => $message,
            'level' => $level,
            'data' => $data,
            'eventTime' => $eventTime
        ];

        $tableau = json_encode($infos, JSON_THROW_ON_ERROR);

        // dd($tableau);

       $response = $this->httpClient->request('POST', "http://localhost:3000/product", [

            'headers' => [
                'Content-Type'=> 'application/json',
                'Accept' => 'application/json'
                
            ],

            'body' => $tableau,
        
        ]);

        if (201 !== $response->getStatusCode()) {
            throw new Exception('Response status code is different than expected.');
        }

        $responseJson = $response->getContent();
        $responseData = json_decode($responseJson, true, 512, JSON_THROW_ON_ERROR);

        return $responseData ;
    }
}