<?php

namespace Sync\Api;

use AmoCRM\Client\AmoCRMApiClient;
use League\OAuth2\Client\Token\AccessToken;
use AmoCRM\Models\CompanyModel;
use AmoCRM\Models\ContactModel;
use Throwable;

/**
 * Class ApiService.
 *
 * @package Sync\Api
 */
class Contacts extends ApiService
{
    /** @var AmoCRMApiClient AmoCRM клиент. */
    private AmoCRMApiClient $apiClient;

    private  ContactModel $contact;

    /**
     * ApiService constructor.
     */
    public function __construct()
    {
        $this->apiClient = new AmoCRMApiClient(
            $integrationId = 'd33c66b3-9e93-4384-be28-6f028b99b5f8',
            $integrationSecretKey = 'W8KivrevFQg8BEggDQ5VSwa5JkhIWwU4aAC1zD0OUhhsQZZd8Ky9wobvMpvl8K3v',
            $integrationRedirectUri = 'http://localhost:80/auth'
        );
    }

     /**
     * Получение токена досутпа для аккаунта.
     *
     * @param AccessToken $queryParams Входные GET параметры.
     * @return array Имя авторизованного аккаунта.
     */
     //Создадим контакт
     public function createContact()
     {

     $this->contact->setName('Example');
     
     
     try {
 
         $contactModel = $this->apiClient->contacts()->addOne($this->contact);
 
     } catch (Throwable $e) {
        die($e->getMessage());
     }
}
}
