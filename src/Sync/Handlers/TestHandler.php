<?php

declare(strict_types=1);

namespace Sync\Handlers;
session_start();
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Sync\Api\Auth;
use Throwable;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\CompanyModel;

use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Collections\LinksCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\AmoAPI;
use AmoCRM\Client\AmoCRMApiClient;
use Sync\Api\ApiService;

class TestHandler implements RequestHandlerInterface
{
     /** @var AmoCRMApiClient AmoCRM клиент. */
     private AmoCRMApiClient $apiClient; 
     
     public function __construct()
     {
         $this->apiClient = new AmoCRMApiClient(
             $integrationId = 'd33c66b3-9e93-4384-be28-6f028b99b5f8',
             $integrationSecretKey = 'W8KivrevFQg8BEggDQ5VSwa5JkhIWwU4aAC1zD0OUhhsQZZd8Ky9wobvMpvl8K3v',
             $integrationRedirectUri = 'https://8c42-46-138-188-188.ngrok-free.app/auth'
         );
     }


    
    

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
    

            $clientId = "d33c66b3-9e93-4384-be28-6f028b99b5f8";
            $clientSecret = "W8KivrevFQg8BEggDQ5VSwa5JkhIWwU4aAC1zD0OUhhsQZZd8Ky9wobvMpvl8K3v";
            $redirectUri = "https://8c42-46-138-188-188.ngrok-free.app/auth";
    
            $apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);
    
            if (isset($_GET['referer'])) {
                $apiClient->setAccountBaseDomain($_GET['referer']);
            }
    
            if (!isset($_GET['code'])) {
                $state = bin2hex(random_bytes(16));
                $_SESSION['oauth2state'] = $state;
                if (isset($_GET['button'])) {
                    echo $apiClient->getOAuthClient()->getOAuthButton(
                        [
                            'title' => 'Установить интеграцию',
                            'compact' => true,
                            'class_name' => 'className',
                            'color' => 'default',
                            'error_callback' => 'handleOauthError',
                            'state' => $state,
                        ]
                    );
                } else {
                    $authorizationUrl = $apiClient->getOAuthClient()->getAuthorizeUrl([
                        'state' => $state,
                        'mode' => 'post_message',
                    ]);
                    header('Location: ' . $authorizationUrl);
                }
                die;
            } elseif (
                empty($_GET['state'])
                || empty($_SESSION['oauth2state'])
                || ($_GET['state'] !== $_SESSION['oauth2state'])
            ) {
                unset($_SESSION['oauth2state']);
                exit('Invalid state');
            }
            /**
             * Ловим обратный код
             */
            try {
                $accessToken = $apiClient->getOAuthClient()->getAccessTokenByCode($_GET['code']);
    
                if (!$accessToken->hasExpired()) {
                    $tokenData = [
                        'accessToken' => $accessToken->getToken(),
                        'refreshToken' => $accessToken->getRefreshToken(),
                        'expires' => $accessToken->getExpires(),
                        'baseDomain' => $apiClient->getAccountBaseDomain(),
                    ];
                }
            } catch (Throwable $e) {
                die((string)$e);
            }
            
            // создаем контакт
            $ownerDetails= $apiClient->getOAuthClient()->getResourceOwner($accessToken);
           $ownerName = $ownerDetails->getName();
            $contactModel = new ContactModel();
            $contactModel->setFirstName("FirstName");   
            $contactModel->setLastName("LastName");
            $contactModel->setName("Name");

            $apiClient->setAccessToken($accessToken);

            $t= $apiClient->contacts()->addone($contactModel);


   

            //Получим контакт по ID
            try {
                $baseDomain = $apiClient->getAccountBaseDomain();
                $this->apiClient->setAccessToken($accessToken);
                $this
                    ->apiClient
                    ->getOAuthClient()
                    ->setBaseDomain((string)$baseDomain);
                    $arrContacts = $this->apiClient->contacts()->get()->toArray();
                    $lastContactId = $arrContacts[count($arrContacts)-1]['id'];                          
                
                $contact = $apiClient->contacts()->getOne($lastContactId);                              
             } catch (Throwable $e) {
                 die((string)$e);
             }

            //  // создаем сделку и привязываем к ней контакт
             $leadsService = $apiClient->leads();

             $lead = new LeadModel();
             $lead->setName('Название сделки')
                 ->setPrice(54321);
             
           $leadsCollection = new LeadsCollection();
           $leadsCollection->add($lead);


            try {
                $leadsCollection = $leadsService->add($leadsCollection);
            } catch (AmoCRMApiException $e) {
                printError($e);
                die;
            }

              $links = new LinksCollection();
              $links->add($contact);
            try {
                $apiClient->leads()->link($lead, $links);
            } catch (Throwable $e) {
                die((string)$e);
            }

    
            return new JsonResponse(
                $ownerName
            );
        } catch (Throwable $e) {
            exit($e);
        }

     
    }
}
