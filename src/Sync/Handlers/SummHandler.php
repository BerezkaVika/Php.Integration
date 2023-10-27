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
use AmoCRM\Exceptions\AmoCRMApiException;

use AmoCRM\Client\AmoCRMApiClient;
use Sync\Api\ApiService;

class SummHandler implements RequestHandlerInterface
{
     /** @var AmoCRMApiClient AmoCRM клиент. */
     private AmoCRMApiClient $apiClient;
     private $tokenActions;
     private $userName;
 
     public function __construct()
     {
         $this->apiClient = new AmoCRMApiClient(
             $integrationId = 'd33c66b3-9e93-4384-be28-6f028b99b5f8',
             $integrationSecretKey = 'W8KivrevFQg8BEggDQ5VSwa5JkhIWwU4aAC1zD0OUhhsQZZd8Ky9wobvMpvl8K3v',
             $integrationRedirectUri = 'https://577a-83-220-239-83.ngrok-free.app/auth'
         );
         $this->tokenActions = new Auth();
     }
     //https://772c-46-138-188-188.ngrok-free.app
     //http://localhost:80/auth

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $result = $this->tokenActions->createProvider();


              //  return new JsonResponse($result);
              return new JsonResponse([
                $result
            ]);
        } catch (Throwable $e) {
            exit($e->getMessage());
        }

     
    }
}
