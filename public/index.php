<?php
// public/index.php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config;
use App\Contact;
use App\HubspotService;
use Dotenv\Dotenv;

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$contact = new Contact();

$hobspotService = new HubspotService();

switch ($uri) {
    case '/':
        echo json_encode(['status' => 'HubSpot API is running']);
        break;

    case '/auth':
        // Check if the token is already saved
        $token = $hobspotService->getValidToken();
        if ($token) {
            echo json_encode(['status' => 'connected']);
            break;
        }

        // If not, redirect to HubSpot authorization page
        $conf  = Config::get();
        $scope = 'crm.objects.contacts.read';
        $url   = "https://app.hubspot.com/oauth/authorize?client_id={$conf['client_id']}&redirect_uri={$conf['redirect_uri']}&scope={$scope}";
        header("Location: $url");
        break;

    case '/auth-callback':
        if (isset($_GET['code'])) {
            HubspotService::exchangeCodeForToken($_GET['code']);
            header("Location: {$_ENV['REDIRECT_SUCCESS_URI']}?auth=success");
        } else {
            echo json_encode(['error' => 'Missing code']);
        }
        break;

    case '/contacts':
        $contacts = $contact->getCachedContacts();
        echo json_encode($contacts);
        break;

    case '/contacts/properties':
        echo json_encode($contact->getContactProperties());
        break;

    case '/valid-token':
        $token = $hobspotService->getValidToken();
        if ($token) {
            echo json_encode(['isValid' => true]);
        } else {
            echo json_encode(['isValid' => false]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
        break;
}
