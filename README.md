# HubSpot PHP API

This PHP API serves as a backend integration for HubSpot using OAuth2 authentication and fetches contacts from the HubSpot CRM. It supports token storage, refreshing, and mock data handling for development.

## Features

- OAuth2 authentication with HubSpot
- Token storage and refresh handling
- Contacts fetching via HubSpot API
- Simple routing using PHP `switch` logic
- Mock contact data for testing
- Frontend-friendly JSON responses

---

## Prerequisites

- PHP >= 7.4
- Composer
- cURL enabled in PHP
- A valid HubSpot App with OAuth credentials
- Donwload the cert at https://curl.se/ca/cacert.pem
- Open your PHP installation directory.
- Locate and open your php.ini file (you can find its location by running php --ini in your terminal).
- Search for this line (might be commented out):
  ```bash
  ;curl.cainfo =
  ```
  and update it to:
  sample path in windows
  
  ```bash
  curl.cainfo = "C:\php\extras\ssl\cacert.pem" 
  openssl.cafile = "C:\php\extras\ssl\cacert.pem" 
  ```
---

## Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/hubspot-api.git
   cd hubspot-api
   composer install

## Setup env
  ```bash
  CLIENT_ID=your_hubspot_client_id
  CLIENT_SECRET=your_hubspot_client_secret
  REDIRECT_URI=http://localhost:8000/oauth/callback
  TOKEN_FILE=storage/token.json

## Run server
  php -S localhost:8000
