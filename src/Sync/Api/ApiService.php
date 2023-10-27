<?php

namespace Sync\Api;


use AmoCRM\Models\ContactModel;
use Throwable;
use AmoCRM\Client\AmoCRMApiClient;
use League\OAuth2\Client\Token\AccessToken;

/**
 * Class ApiService.
 *
 * @package Sync\Api
 */
class ApiService
{
    /** @var string Файл хранения токенов. */
    public $TOKEN_FILE = './tokens.json';

    /** @var AmoCRMApiClient AmoCRM клиент. */
    private AmoCRMApiClient $apiClient;

    //public  ContactModel $contact = "fff";
    

   


    /**
     * ApiService constructor.
     */
    public function __construct()
    {
        $this->apiClient = new AmoCRMApiClient(
            $integrationId = 'd33c66b3-9e93-4384-be28-6f028b99b5f8',
            $integrationSecretKey = 'W8KivrevFQg8BEggDQ5VSwa5JkhIWwU4aAC1zD0OUhhsQZZd8Ky9wobvMpvl8K3v',
            $integrationRedirectUri = 'https://577a-83-220-239-83.ngrok-free.app/auth'
        );
        //$this->contact = new ContactModel();
    }

    /**
     * Сохранение токена авторизации.
     *
     * @param int $serviceId Системный идентификатор аккаунта.
     * @param array $token Токен доступа Api.
     * @return void
     */
    public function saveToken($accessToken)
    {
        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            $data = [
                'accessToken' => $accessToken['accessToken'],
                'expires' => $accessToken['expires'],
                'refreshToken' => $accessToken['refreshToken'],
                'baseDomain' => $accessToken['baseDomain'],
            ];

            file_put_contents($this->TOKEN_FILE, json_encode($data));
        } else {
            exit('Invalid access token ' . var_export($accessToken, true));
        }
    }

    

 
    }