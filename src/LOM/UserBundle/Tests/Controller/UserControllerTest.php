<?php

namespace LOM\UserBundle\Tests\Controller;

use LOM\UserBundle\TestCases\LoginWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends LoginWebTestCase {

    public function __construct() {
        parent::__construct();
    }

    public function testUserHome() {
        $client = $this->login("user@example.com", "supersecret");
        $crawler = $client->request('GET', '/user/');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Authentication details")')->count());
        $this->logout($client);
    }

    public function testUserEdit() {
        $client = $this->login("user@example.com", "supersecret");
        $crawler = $client->request('GET', '/user/edit');        
        $button = $crawler->selectButton('Update');
        
        $form = $button->form(array(
            'lom_userbundle_user[username]' => 'optimus@example.com',
            'lom_userbundle_user[fullname]' => 'Optimus the great',
            'lom_userbundle_user[institution]' => 'Autobots',
        ));
        
        $client->submit($form);
        
        $crawler = $client->request('GET', '/user/');
        
        $this->assertGreaterThan(0, $crawler->filter('html:contains("optimus@example.com")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Optimus the great")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Autobots")')->count());
    }

    public function testUserPassword() {
        $this->setUp();
        $client = $this->login("user@example.com", "supersecret");
        $client->followRedirects();
        
        $crawler = $client->request('GET', '/user/password');
        $button = $crawler->selectButton('Change password');

        $form = $button->form(array(
            'user_change_password[oldPassword]' => 'badpassword',
            'user_change_password[newPassword][first]' => 'newpassword',
            'user_change_password[newPassword][second]' => 'newpassword',
        ));

        $crawler = $client->submit($form);
        $response = $client->getResponse();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Wrong value for your current password.")')->count());

        $button = $crawler->selectButton('Change password');

        $form = $button->form(array(
            'user_change_password[oldPassword]' => 'supersecret',
            'user_change_password[newPassword][first]' => 'newpassword',
            'user_change_password[newPassword][second]' => 'newpassword',
        ));

        $crawler = $client->submit($form);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Your password has been changed.")')->count());

        $this->logout($client);
        $client = $this->login('user@example.com', 'newpassword');
        $crawler = $client->request('GET', '/user/');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Authentication details")')->count());
        $this->logout($client);
    }

}
