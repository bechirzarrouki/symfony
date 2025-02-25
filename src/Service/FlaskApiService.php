<?php

namespace App\Service;

use Google\Service\Docs\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\HttpClientInterface;
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
