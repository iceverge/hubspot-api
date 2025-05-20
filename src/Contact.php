<?php
namespace App;

use App\HubspotService;
use GuzzleHttp\Client;

class Contact extends HubspotService
{
    public function getCachedContacts(): array
    {
        $config = Config::get();

        $cacheFile = $config['cache_file'];
        $expire    = 60 * 10;

        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $expire) {
            return json_decode(file_get_contents($cacheFile), true);
        }

        $contacts = $this->getContacts();
        file_put_contents($cacheFile, json_encode($contacts));

        return $contacts;
    }

    private function getContacts(): array
    {
        $token = $this->getToken();
        if (! $token) {
            return ['status' => 401, 'error' => 'Token not found'];
        }

        $client   = new Client();
        $response = $client->get('https://api.hubapi.com/crm/v3/objects/contacts', [
            'headers' => [
                'Authorization' => "Bearer {$token['access_token']}",
            ],
            'query'   => [
                'properties' => '
                    firstname,
                    lastname,
                    email,
                    phone,
                    hs_v2_date_entered_customer,
                    hs_v2_date_entered_lead',
                'limit'      => 100,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function getContactProperties(): array
    {
        $token = $this->getToken();
        if (! $token) {
            return ['status' => 401, 'error' => 'Token not found'];
        }

        $client   = new Client();
        $response = $client->get('https://api.hubapi.com/crm/v3/objects/contacts/properties', [
            'headers' => [
                'Authorization' => "Bearer {$token['access_token']}",
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

}
