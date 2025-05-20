<?php
namespace App;

use App\HubspotService;
use GuzzleHttp\Client;

class Company extends HubspotService
{
    public function getCompanies(): array
    {
        $token = $this->getToken();
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
}
