<?php
namespace App;

use GuzzleHttp\Client;

class HubspotService
{
    public function getToken(): ?array
    {
        $path = Config::get()['token_file'];
        if (! file_exists($path)) {
            return null;
        }

        return json_decode(file_get_contents($path), true);
    }

    public static function saveToken(array $token): void
    {
        $token['created_at'] = time();

        file_put_contents(Config::get()['token_file'], json_encode($token));
    }

    public static function exchangeCodeForToken(string $code): array
    {
        $config = Config::get();
        $client = new Client();

        $response = $client->post('https://api.hubapi.com/oauth/v1/token', [
            'form_params' => [
                'grant_type'    => 'authorization_code',
                'client_id'     => $config['client_id'],
                'client_secret' => $config['client_secret'],
                'redirect_uri'  => $config['redirect_uri'],
                'code'          => $code,
            ],
        ]);

        $token = json_decode($response->getBody(), true);
        self::saveToken($token);
        return $token;
    }

    public function getCompanies(): array
    {
        $token = self::getToken();
        if (! $token) {
            return ['status' => 401, 'error' => 'Token not found'];
        }

        $client   = new Client();
        $response = $client->get('https://api.hubapi.com/crm/v3/objects/companies', [
            'headers' => [
                'Authorization' => "Bearer {$token['access_token']}",
            ],
            'query'   => [
                'properties' => '
                    name,
                    domain,
                    hs_v2_date_entered_customer,
                    hs_v2_date_entered_lead',
                'limit'      => 100,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public static function getDeals(): array
    {
        $token = self::getToken();
        if (! $token) {
            return ['status' => 401, 'error' => 'Token not found'];
        }

        $client   = new Client();
        $response = $client->get('https://api.hubapi.com/crm/v3/objects/{something}', [
            'headers' => [
                'Authorization' => "Bearer {$token['access_token']}",
            ],
            'query'   => [
                'properties' => '
                    name,
                    domain,
                    hs_v2_date_entered_customer,
                    hs_v2_date_entered_lead',
                'limit'      => 100,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public static function getValidToken(): ?array
    {
        $token = self::getToken();
        if (! $token || ! isset($token['access_token'], $token['expires_in'], $token['refresh_token'], $token['created_at'])) {
            return null;
        }

        // Check if expired
        $expiresAt = $token['created_at'] + $token['expires_in'] - 60;
        if (time() < $expiresAt) {
            return $token; // Still valid
        }

        // Refresh the token
        try {
            $newToken = self::exchangeCodeForToken($token['refresh_token']);
            return $newToken;
        } catch (\Exception $e) {
            return null;
        }
    }

}
