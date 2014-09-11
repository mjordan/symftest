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

}
