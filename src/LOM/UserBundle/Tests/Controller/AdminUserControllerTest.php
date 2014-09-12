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

namespace LOM\UserBundle\Tests\Controller;

use LOM\UserBundle\TestCases\LoginWebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test the actions an admin can take on a user.
 */
class AdminUserControllerTest extends LoginWebTestCase
{

    /**
     * Construct the test.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Attempt to load the user list.
     */
    public function testAdminUserList()
    {
        $client = $this->login("admin@example.com", "supersecret");
        $crawler = $client->request('GET', '/admin/user/');
        $this->assertEquals(5, $crawler->filter('td:contains("@example.com")')->count());
    }

    /**
     * Attempt to change a user's password.
     */
    public function testAdminChangePassword()
    {
        $client = $this->login("admin@example.com", "supersecret");
        $crawler = $client->request('GET', '/admin/user/5/password');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Change the password for user@example.com")')->count());

        $button = $crawler->selectButton('Change password');
        $form = $button->form(array(
            'admin_change_password[newPassword][first]' => 'yabbadabbadoo',
            'admin_change_password[newPassword][second]' => 'yabbadabbadoo',
        ));
        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('html:contains("The password has been changed.")')->count());
        $this->logout($client);

        $client = $this->login("user@example.com", "yabbadabbadoo");
        $crawler = $client->request('GET', '/user/');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("user@example.com")')->count());
    }

    public function testAdminEditUser()
    {
        $client = $this->login("admin@example.com", "supersecret");
        $crawler = $client->request('GET', '/admin/user/5/edit');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("User edit")')->count());

        $button = $crawler->selectButton('Update');
        $form = $button->form(array(
            'lom_userbundle_user[username]' => 'optimus@example.com',
            'lom_userbundle_user[fullname]' => 'Optimus Prime',
            'lom_userbundle_user[institution]' => 'Autobots',
            'lom_userbundle_user[roles]' => array(
                '5', '3'
            )
        ));
        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('html:contains("The user information has been updated.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("optimus@example.com")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Optimus Prime")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Autobots")')->count());
        $this->assertGreaterThan(0, $crawler->filter('a:contains("ROLE_USER")')->count());
        $this->assertGreaterThan(0, $crawler->filter('a:contains("ROLE_DEPOSITOR")')->count());
    }

    public function testAdminCreateUser()
    {
        $client = $this->login("admin@example.com", "supersecret");
        $crawler = $client->request('GET', '/admin/user/new');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("User creation")')->count());

        $button = $crawler->selectButton('Create');
        $form = $button->form(array(
            'lom_userbundle_user[username]' => 'megatron@example.com',
            'lom_userbundle_user[fullname]' => 'Megatron Baddy',
            'lom_userbundle_user[institution]' => 'Decepticons',
            'lom_userbundle_user[roles]' => array(
                '5', '3'
            )
        ));
        $crawler = $client->submit($form);

        $mailCollector = $client->getProfile()->getCollector("swiftmailer");
        $this->assertEquals(1, $mailCollector->getMessageCount());
        $messages = $mailCollector->getMessages();
        $message = $messages[0];
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals('Welcome to LOCKS-O-MATTIC.', $message->getSubject());

        $matches = array();
        preg_match('/password reset code is\s*([0-9a-f]*)/', $message->getBody(), $matches);

        $code = $matches[1];
        $this->assertRegExp('/^[0-9a-f]{40}$/', $code);

        $crawler = $client->followRedirect();
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('html:contains("The user account has been created.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("megatron@example.com")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Megatron Baddy")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Decepticons")')->count());
        $this->assertGreaterThan(0, $crawler->filter('a:contains("ROLE_USER")')->count());
        $this->assertGreaterThan(0, $crawler->filter('a:contains("ROLE_DEPOSITOR")')->count());

        $this->logout($client);
        $crawler = $client->request('GET', '/reset/confirm');
        $button = $crawler->selectButton('Reset password');
        $form = $button->form(array(
            'user_reset_password[username]' => 'megatron@example.com',
            'user_reset_password[resetcode]' => $code,
            'user_reset_password[password][first]' => 'pewpewpewpew',
            'user_reset_password[password][second]' => 'pewpewpewpew',
        ));
        $crawler = $client->submit($form);
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("successfully changed")')->count());

        $client->followRedirects();
        $crawler = $client->request('GET', '/login');
        $response = $client->getResponse();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Username")')->count());
        $button = $crawler->selectButton('login');
        $form = $button->form(array(
            '_username' => 'megatron@example.com',
            '_password' => 'pewpewpewpew',
        ));

        $crawler = $client->submit($form);
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("megatron@example.com")')->count());
    }

}
