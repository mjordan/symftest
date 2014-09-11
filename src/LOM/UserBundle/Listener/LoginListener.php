<?php
/*
 * Copyright (C) 2014 mjoyce
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

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
            $this->doctrine->getManager()->persist($user);
            $this->doctrine->getManager()->flush();
        }
    }

}
