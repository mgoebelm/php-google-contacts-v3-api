<?php

namespace rapidweb\googlecontacts\helpers;

abstract class GoogleHelper
{
    private static function loadConfig()
    {        
        $config = new \stdClass;  
        
        $config->clientID = session('GOOGLE_HELPER_CLIENTID',env('GOOGLE_HELPER_CLIENTID',''));
        $config->clientSecret = session('GOOGLE_HELPER_CLIENTSECRET',env('GOOGLE_HELPER_CLIENTSECRET',''));
        $config->redirectUri = session('GOOGLE_HELPER_REDIRECTURI',env('GOOGLE_HELPER_REDIRECTURI',''));
        $config->developerKey = session('GOOGLE_HELPER_DEVELOPERKEY',env('GOOGLE_HELPER_DEVELOPERKEY',' '));
        $config->refreshToken = session('GOOGLE_HELPER_REFRESHTOKEN',env('GOOGLE_HELPER_REFRESHTOKEN',''));

        foreach ($config as $key => $val) {
            if(!$val){
               throw new \Exception('GOOGLE HELPER invalid: '.$key);
            }
        }
        return $config;
    }

    public static function getClient()
    {
        $config = self::loadConfig();

        $client = new \Google_Client();

        $client->setApplicationName('Rapid Web Google Contacts API');

        $client->setScopes(array(/*
        'https://apps-apis.google.com/a/feeds/groups/',
        'https://www.googleapis.com/auth/userinfo.email',
        'https://apps-apis.google.com/a/feeds/alias/',
        'https://apps-apis.google.com/a/feeds/user/',*/
        'https://www.google.com/m8/feeds/',
        /*'https://www.google.com/m8/feeds/user/',*/
        ));

        $client->setClientId($config->clientID);
        $client->setClientSecret($config->clientSecret);
        $client->setRedirectUri($config->redirectUri);
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');
        $client->setDeveloperKey($config->developerKey);

        if (isset($config->refreshToken) && $config->refreshToken) {
            $client->refreshToken($config->refreshToken);
        }

        return $client;
    }

    public static function getAuthUrl(\Google_Client $client)
    {
        return $client->createAuthUrl();
    }

    public static function authenticate(\Google_Client $client, $code)
    {
        $client->authenticate($code);
    }

    public static function getAccessToken(\Google_Client $client)
    {
        return json_decode($client->getAccessToken());
    }
}
