<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Doctrine\ORM\EntityManagerInterface;

class ApiKeyUserProvider implements UserProviderInterface
{

    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getUsernameForAuthToken($apiKey)
    {
        // Look up the username based on the token in the database, via
        // an API call, or do something entirely different
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['auth_token' => $apiKey]);
        
        if (!$user)
        {
            throw new BadCredentialsException();
        }
        
        $username = $user->getUsername();

        return $username;
    }

    public function loadUserByUsername($username)
    {
        
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        
        if (!$user)
        {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('login.error.loginerror');
        }

        return $user;
//        return new User(
//                $username, null,
//                // the roles for the user - you may choose to determine
//                // these dynamically somehow based on the user
//                array('ROLE_API')
//        );
    }

    public function loadUserByLogoutToken($logoutToken)
    {
        
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['logout_token' => $logoutToken]);

        if (!$user)
        {
            return false;
        }

        return $user;
    }
    
    public function refreshUser(UserInterface $user)
    {
        // this is used for storing authentication in the session
        // but in this example, the token is sent in each request,
        // so authentication can be stateless. Throwing this exception
        // is proper to make things stateless
        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        return User::class === $class;
    }

}
