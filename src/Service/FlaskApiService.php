<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

class FlaskApiService
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function predictROI(string $description, string $type, float $amount): array
    {
        $url = 'http://127.0.0.1:5000/predict-roi';

        $payload = [
            'description' => $description,
            'type' => $type,
            'amount' => $amount,
        ];

        try {
            $response = $this->httpClient->request('POST', $url, [
                'json' => $payload,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            // Decode JSON response
            $data = json_decode($response->getContent(), true);

            // Return only the 'predictions' field
            return $data;
        } catch (TransportExceptionInterface | ClientExceptionInterface | ServerExceptionInterface $e) {
            return ['error' => 'Failed to fetch ROI predictions', 'message' => $e->getMessage()];
        }
    }
}
