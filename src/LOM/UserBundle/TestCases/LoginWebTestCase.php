<?php

namespace LOM\UserBundle\TestCases;

use Symfony\Bundle\FrameworkBundle\Client;

class LoginWebTestCase extends FixturesWebTestCase {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function login($username, $password) {
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
    
    public function logout(Client $client) {
        $client->request('GET', '/logout');
        $client->restart();
        return $client;
    }
    
}