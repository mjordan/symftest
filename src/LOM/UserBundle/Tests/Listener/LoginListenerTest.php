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

namespace LOM\UserBundle\Tests\Listener;

use LOM\UserBundle\TestCases\LoginWebTestCase;
use LOM\UserBundle\Entity\User;

/**
 * Test the login listener - it should clear password reset codes after a 
 * successful login.
 */
class LoginListenerTest extends LoginWebTestCase
{

    public function testResetCodeAfterLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/reset');
        $button = $crawler->selectButton('Reset');
        $form = $button->form(array(
            'username' => 'user@example.com'
        ));
        $crawler = $client->submit($form);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("instructions have been sent")')->count());

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LOMUserBundle:User')->find(5);
        $this->assertNotNull($entity->getResetCode());
        $this->assertNotNull($entity->getResetExpires());
        
        $client = $this->login('user@example.com', 'supersecret');
        $crawler = $client->request('GET', '/user/');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Authentication details")')->count());
        
        // fetch a new copy of the user.
        $em->refresh($entity);
        $this->assertNull($entity->getResetCode());
        $this->assertNull($entity->getResetExpires());
    }

}
