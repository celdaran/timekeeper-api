<?php
// src/Security/ApiKeyAuthenticator.php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    // No longer a hardcoded constant, injected via constructor
    private array $apiKeys;

    /**
     * The constructor receives the API keys from services.yaml.
     * Symfony's DI container automatically passes the values defined there.
     *
     * @param string $clientApiKeysString Comma-separated client API keys
     * @param string $adminApiKeysString Comma-separated admin API keys
     */
    public function __construct(string $clientApiKeysString, string $adminApiKeysString)
    {
        // Parse the comma-separated strings into an associative array for easy lookup
        // This structure maps the key to the username it represents.
        $this->apiKeys = [];
        foreach (explode(',', $clientApiKeysString) as $key) {
            $key = trim($key);
            if (!empty($key)) {
                $this->apiKeys[$key] = 'api_client';
            }
        }
        foreach (explode(',', $adminApiKeysString) as $key) {
            $key = trim($key);
            if (!empty($key)) {
                $this->apiKeys[$key] = 'api_admin';
            }
        }
    }

    /**
     * Called on every request to decide if this authenticator should be used.
     * Return `false` to let other authenticators try.
     */
    public function supports(Request $request): ?bool
    {
        return $request->headers->has('X-API-KEY') && str_starts_with($request->getPathInfo(), '/api/');
    }

    public function authenticate(Request $request): Passport
    {
        $apiKey = $request->headers->get('X-API-KEY');

        if (null === $apiKey) {
            throw new CustomUserMessageAuthenticationException('No API token provided.');
        }

        // Look up the API key using the injected data
        if (!isset($this->apiKeys[$apiKey])) {
            throw new CustomUserMessageAuthenticationException('Invalid API token.');
        }

        $username = $this->apiKeys[$apiKey];
        return new SelfValidatingPassport(new UserBadge($username));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}