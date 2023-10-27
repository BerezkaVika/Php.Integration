<?php

namespace Sync\Api;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\OAuth2\Client\Provider\AmoCRM;
use AmoCRM\Client\AmoCRMApiClientFactory;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\LinksCollection;
use AmoCRM\Collections\NullTagsCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\CompanyModel;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\LeadModel;
use Carbon\Carbon;
use League\OAuth2\Client\Token\AccessTokenInterface;
use League\OAuth2\Client\Grant\AuthorizationCode;
use League\OAuth2\Client\Grant\RefreshToken;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;

use Throwable;

/**
 * Class ApiService.
 *
 * @package Sync\Api
 */
class Auth extends ApiService
{


        /** @var AmoCRMApiClient AmoCRM клиент. */
        private AmoCRMApiClient $apiClient;

        public $TOKEN_FILE = './tokens.json';
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
        }


    /**
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    public function getToken()
    {
        $accessToken = json_decode(file_get_contents($this->TOKEN_FILE), true);

        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            return new \League\OAuth2\Client\Token\AccessToken([
                'access_token' => $accessToken['accessToken'],
                'refresh_token' => $accessToken['refreshToken'],
                'expires' => $accessToken['expires'],
                'baseDomain' => $accessToken['baseDomain'],
            ]);
        } else {
            exit('Invalid access token ' . var_export($accessToken, true));
        }
    }

       //Создадим контакт. ВАРИАНТ 1
       public function createContact()
       {
    //    try {

    //     $contact = new ContactModel();
    //     $contact->setName('Example');
    //     printf('Start');
    //     // $this->apiClient->contacts()->addOne($contact);

    //    } catch (Throwable $e) {
    //       die($e->getMessage());
    //    }

    //     //Получим коллекцию значений полей контакта
    //     $customFields = $contact->getCustomFieldsValues();

    //    $phoneField = (new MultitextCustomFieldValuesModel())->setFieldCode('PHONE');


    //    //Установим значение поля
    //     $phoneField->setValues(
    //         (new MultitextCustomFieldValueCollection())
    //             ->add(
    //                 (new MultitextCustomFieldValueModel())
    //                     ->setEnum('WORKDD')
    //                     ->setValue('+79123')
    //             )
    //         );
    //         $contact->setName('Example');

    //     $customFields->add($phoneField);

    // Создадим контакт

//********************************
   
   // Создадим контакт. ВАРИАНТ 2
try{


$contactModel = new ContactModel();
$contactModel->setLastName("LastName");
$contactModel->setFirstName("FirstName");
$contactModel->setName("Name");
printf('1');
//$this->apiClient->contacts()->addOne($contactModel);
$this->apiClient->contacts()->updateOne($contactModel);
      
        printf('2');

    } catch (AmoCRMApiException $e) {
        exit($e->getMessage());
        die;
    }


    printf('End');
  }

  
    /**
     * Создаем провайдера
     */
    public function createProvider()
    {
        /**
         * Создаем провайдера
         */
        $provider = new AmoCRM([
            'clientId' => 'd33c66b3-9e93-4384-be28-6f028b99b5f8',
            'clientSecret' => 'W8KivrevFQg8BEggDQ5VSwa5JkhIWwU4aAC1zD0OUhhsQZZd8Ky9wobvMpvl8K3v',
            'redirectUri' => 'https://577a-83-220-239-83.ngrok-free.app/auth',
        ]);
        if (isset($_GET['referer'])) {
            $provider->setBaseDomain($_GET['referer']);
        }
        
        if (!isset($_GET['request'])) {
            if (!isset($_GET['code'])) {
                /**
                 * Просто отображаем кнопку авторизации или получаем ссылку для авторизации
                 * По-умолчанию - отображаем кнопку
                 */
                $_SESSION['oauth2state'] = bin2hex(random_bytes(16));
                if (true) {
                    echo '<div>
                        <script
                            class="amocrm_oauth"
                            charset="utf-8"
                            data-client-id="' . $provider->getClientId() . '"
                            data-title="Установить интеграцию"
                            data-compact="false"
                            data-class-name="className"
                            data-color="default"
                            data-state="' . $_SESSION['oauth2state'] . '"
                            data-error-callback="handleOauthError"
                            src="https://www.amocrm.ru/auth/button.min.js"
                        ></script>
                        </div>';
                    echo '<script>
                    handleOauthError = function(event) {
                        alert(\'ID клиента - \' + event.client_id + \' Ошибка - \' + event.error);
                    }
                    </script>';
                    die;
                } else {
                    $authorizationUrl = $provider->getAuthorizationUrl(['state' => $_SESSION['oauth2state']]);
                    header('Location: ' . $authorizationUrl);
                }
            } elseif (empty($_GET['state']) || empty($_SESSION['oauth2state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
                unset($_SESSION['oauth2state']);
                exit('Invalid state');
            }
        
            /**
             * Ловим обратный код
             */
            try {
        
                    /** @var \League\OAuth2\Client\Token\AccessToken $access_token */
                    $accessToken = $provider->getAccessToken(new AuthorizationCode(), [
                        'code' => $_GET['code'],
                    ]);
            
            
                    if (!$accessToken->hasExpired()) {
                        $this->saveToken([
                            'accessToken' => $accessToken->getToken(),
                            'refreshToken' => $accessToken->getRefreshToken(),
                            'expires' => $accessToken->getExpires(),
                            'baseDomain' => $provider->getBaseDomain(),
                        ]);
                    }
            } catch (Throwable $e) {
                die($e->getMessage());
        }
             
            
        
            /** @var \AmoCRM\OAuth2\Client\Provider\AmoCRMResourceOwner $ownerDetails */
            $ownerDetails = $provider->getResourceOwner($accessToken);
        
            printf('Hello, %s!', $ownerDetails->getName());
            $this->createContact();
            printf('Yes');

        } else {
            $accessToken =  $this->getToken();
        
            $provider->setBaseDomain($accessToken->getValues()['baseDomain']);
        
            /**
             * Проверяем активен ли токен и делаем запрос или обновляем токен
             */
            if ($accessToken->hasExpired()) {
                /**
                 * Получаем токен по рефрешу
                 */
                try {
                    $accessToken = $provider->getAccessToken(new RefreshToken(), [
                        'refresh_token' => $accessToken->getRefreshToken(),
                    ]);
        
                    $this->saveToken([
                        'accessToken' => $accessToken->getToken(),
                        'refreshToken' => $accessToken->getRefreshToken(),
                        'expires' => $accessToken->getExpires(),
                        'baseDomain' => $provider->getBaseDomain(),
                    ]);
        
        
                } catch (Throwable $e) {
                    die($e->getMessage());
                }
            }
        
            try {
                /**
                 * Делаем запрос к АПИ
                 */
                $data = $provider->getHttpClient()
                    ->request('GET', $provider->urlAccount() . 'api/v2/account', [
                        'headers' => $provider->getHeaders($accessToken)
                    ]);
        
                $parsedBody = json_decode($data->getBody()->getContents(), true);
                printf('ID аккаунта - %s, название - %s', $parsedBody['id'], $parsedBody['name']);
        
        
        
            } catch (Throwable $e) {
                die($e->getMessage());
            }
        }
        
        try{
         
           $this->apiClient->setAccessToken($accessToken)
                ->setAccountBaseDomain($this->$accessToken->getValues()['baseDomain'])
                ->onAccessTokenRefresh(
                    function (AccessTokenInterface $accessToken, string $baseDomain) {
                        $this->saveToken(
                            [
                                'accessToken' => $accessToken->getToken(),
                                'refreshToken' => $accessToken->getRefreshToken(),
                                'expires' => $accessToken->getExpires(),
                                'baseDomain' => $baseDomain,
                            ]
                        );
                    }
                );
        
            }catch (Throwable $e) {
                die($e->getMessage());
            
            }
            
        }

    }
