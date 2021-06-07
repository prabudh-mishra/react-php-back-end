<?php


namespace App\EventListener;


use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuthenticationListener
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $data = $event->getData();

        $details['id'] = $user->getId();
        $details['firstName'] = $user->getFirstName();
        $details['lastName'] = $user->getLastName();
        $details['email'] = $user->getEmail();

        $event->setData([
            'code' => $event->getResponse()->getStatusCode(),
            'message' => 'Login successfully',
            'user' => $details,
            'token' => $data['token'],
        ]);
    }

    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
        $response = new JWTAuthenticationFailureResponse('Invalid Username or Password', Response::HTTP_UNAUTHORIZED);
        $event->setResponse($response);
    }
}
