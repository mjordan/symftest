<?php

namespace LOM\UserBundle\Listener;

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener {
    
    private $doctrine;
    
    public function __construct(Doctrine $doctrine) {
        $this->doctrine = $doctrine;
    }
    
    public function onInteractiveLogin(InteractiveLoginEvent $event) {
        $user = $event->getAuthenticationToken()->getUser();
        
        if($user) {
            $user->setResetCode(null);
            $user->setResetExpires(null);
            $this->doctrine->getEntityManager()->persist($user);
            $this->doctrine->getEntityManager()->flush();
        }        
    }
    
}