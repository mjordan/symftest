<?php

namespace LOM\UserBundle\Listener;

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * An event listener to automatically clear a user's password reset tokens
 * on a successful login.
 */
class LoginListener
{

    /**
     * Database access
     *
     * @var Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    private $doctrine;

    /**
     * Construct the listener. Parameters for the constructor are defined
     * in LOM\Resources\config\services.yml 
     * 
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    public function __construct(Doctrine $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Triggered on a successful login. Set the resetCode and resetExpires 
     * to null, and persist to the database.
     * 
     * @param \Symfony\Component\Security\Http\Event\InteractiveLoginEvent $event
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user) {
            $user->setResetCode(null);
            $user->setResetExpires(null);
            $this->doctrine->getEntityManager()->persist($user);
            $this->doctrine->getEntityManager()->flush();
        }
    }

}
