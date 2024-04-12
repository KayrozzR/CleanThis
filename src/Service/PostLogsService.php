<?php

namespace App\Service;

use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PostLogsService
{
    public function __construct(private HttpClientInterface $httpClient)
    {
        
    }

    public function postLogs(array $logData): array
    {
        $requestData = [
            'EventTime' => $logData['EventTime'],
            'LoggerName' => $logData['LoggerName'],
            'User' => $logData['User'],
            'Message' => $logData['Message'],
            'Level' => $logData['Level'],
            'Data' => $logData['Data'],
        ];


        $requestJson = json_encode($requestData, JSON_THROW_ON_ERROR);

        //  dd($requestJson);

        $response = $this->httpClient->request('POST', 'http://localhost:5000/', [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => $requestJson,
        ]);

        
        if (201 !== $response->getStatusCode()) {
            throw new Exception('Response status code is different than expected.');
        }

        // ... other checks

        $responseJson = $response->getContent();
        $responseData = json_decode($responseJson, true, 512, JSON_THROW_ON_ERROR);

        return $responseData;
    }

}