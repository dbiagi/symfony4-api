<?php

namespace App\Security;

use App\Entity\ApiUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiAuthenticator extends AbstractGuardAuthenticator
{
    private const HEADER_TOKEN_NAME = 'X-AUTH-TOKEN';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Request $request)
    {
        return $request->headers->has(self::HEADER_TOKEN_NAME);
    }

    /**
     * {@inheritDoc}
     */
    public function getCredentials(Request $request)
    {
        return [
            'token' => $request->headers->get(self::HEADER_TOKEN_NAME)
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (empty($credentials['token'])) {
            return false;
        }

        return $this->em->getRepository(ApiUser::class)->findOneBy([
            'token' => $credentials['token']
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'error' => 'Authentication failed'
        ];

        return new JsonResponse($data, JsonResponse::HTTP_FORBIDDEN);
    }

    /**
     * {@inheritDoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $user = $token->getUser();

        if (!$user instanceof ApiUser) {
            return null;
        }

        $user->setDtLastAccess(new \DateTime());

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function supportsRememberMe()
    {
        return false;
    }
}