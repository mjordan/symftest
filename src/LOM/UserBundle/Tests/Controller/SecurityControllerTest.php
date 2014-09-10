<?php

namespace LOM\UserBundle\Tests\Controller;

use LOM\UserBundle\TestCases\FixturesWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends FixturesWebTestCase {

    public function testAnonUserAccess() {
        $client = static::createClient();
        $client->restart();

        $crawler = $client->request('GET', '/user');
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertStringEndsWith('/login', $response->headers->get('location'));
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Redirecting")')->count());

        $crawler = $client->followRedirect();
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Login")')->count());
    }

    public function testAnonAdminAccess() {
        $client = static::createClient();
        $client->restart();

        $crawler = $client->request('GET', '/admin');
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertStringEndsWith('/login', $response->headers->get('location'));
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Redirecting")')->count());

        $crawler = $client->followRedirect();
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Login")')->count());
    }

    public function testUserLogin() {
        $client = static::createClient();
        $client->restart();

        $crawler = $client->request('GET', '/login');
        $response = $client->getResponse();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Username")')->count());
        $button = $crawler->selectButton('login');
        $form = $button->form(array(
            '_username' => 'user@example.com',
            '_password' => 'supersecret',
        ));

        $crawler = $client->submit($form);
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertStringEndsWith('/user/', $response->headers->get('location'));
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Redirecting")')->count());

        $crawler = $client->followRedirect();
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("user@example.com")')->count());

        $link = $crawler->selectLink("Logout")->link();
        $this->assertStringEndsWith('/logout', $link->getUri());
        $crawler = $client->click($link);
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertStringEndsWith('/', $response->headers->get('location'));
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Redirecting")')->count());

        $crawler = $client->request('GET', '/admin');
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertStringEndsWith('/login', $response->headers->get('location'));
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Redirecting")')->count());

        $crawler = $client->request('GET', '/user');
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertStringEndsWith('/login', $response->headers->get('location'));
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Redirecting")')->count());
    }

}
