<?php
// src/Service/GmailOAuth2TokenProvider.php
namespace App\Service;

use Google\Client as GoogleClient;

class GmailOAuth2TokenProvider
{
    private GoogleClient $client;

    public function __construct(
        string $clientId = '601891516572-4a3td1gq34hv28vro7qjoht0ipc3901a', 
        string $clientSecret = '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', 
        string $refreshToken = '1//04pSxP2phhoGaCgYIARAAGAQSNgF-L9IrFwOs4RBT7HG9SqMO4d2teskNXHNHCmEEu8scCFJLarAAqwIZme8RjczTQl9dTatJfA'
    ) {
        $this->client = new GoogleClient();
        $this->client->setClientId($clientId);
        $this->client->setClientSecret($clientSecret);
        $this->client->setAccessType('offline');
        $this->client->setApprovalPrompt('force');
        $this->client->refreshToken($refreshToken);
    }
    

    public function getAccessToken(): string
    {
        // Refresh the token if needed and return the current access token
        $token = $this->client->getAccessToken();
        // The token array contains an 'access_token' key
        return $token['access_token'];
    }
}
