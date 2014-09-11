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

namespace LOM\UserBundle\TestCases;

use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Extend FixturesWebTestCase with a login method.
 */
class LoginWebTestCase extends FixturesWebTestCase
{
    /**
     * Construct a test case
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Try to login to the application, and return the client.
     *
     * @param string $username
     * @param string $password
     *
     * @return Client
     */
    public function login($username, $password)
    {
        $client = static::createClient();
        $client->restart();
        $crawler = $client->request('GET', '/login');
        $button = $crawler->selectButton('login');
        $form = $button->form(array(
            '_username' => $username,
            '_password' => $password,
        ));
        $client->submit($form);
        $client->followRedirect();

        return $client;
    }

    /**
     * Logout of the application
     *
     * @param Client $client
     *
     * @return Client
     */
    public function logout(Client $client)
    {
        $client->request('GET', '/logout');
        $client->restart();

        return $client;
    }

}
