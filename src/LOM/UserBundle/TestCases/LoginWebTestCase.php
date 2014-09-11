<?php

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
