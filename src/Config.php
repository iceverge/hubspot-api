<?php
// src/Config.php

namespace App;

use Dotenv\Dotenv;

class Config
{
    public static function get(): array
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        return [
            'client_id'     => $_ENV['CLIENT_ID'],
            'client_secret' => $_ENV['CLIENT_SECRET'],
            'redirect_uri'  => $_ENV['REDIRECT_URI'] . "/auth-callback",
            'token_file'    => __DIR__ . '/../' . $_ENV['TOKEN_FILE'],
            'cache_file'    => __DIR__ . '/../storage/cache/contacts.json',
        ];
    }

}
