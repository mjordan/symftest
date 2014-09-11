<?php

namespace LOM\UserBundle\Tests\Controller;

use LOM\UserBundle\TestCases\FixturesWebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test the security features of the app.
 */
class SecurityControllerTest extends FixturesWebTestCase
{

    /**
     * Attempt to access the user page without logging in.
     */
    public function testAnonUserAccess()
    {
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

    /**
     * Attempt to access the admin page without logging in.
     */
    public function testAnonAdminAccess()
    {
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

    /**
     * Attempt to login.
     */
    public function testUserLogin()
    {
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

    /**
     * Attempt to reset a password.
     */
    public function testLostPassword()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/reset');
        $button = $crawler->selectButton('Reset');
        $form = $button->form(array(
            'username' => 'user@example.com'
        ));
        $crawler = $client->submit($form);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("instructions have been sent")')->count());

        $mailCollector = $client->getProfile()->getCollector("swiftmailer");
        $this->assertEquals(1, $mailCollector->getMessageCount());
        $message = $mailCollector->getMessages()[0];
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals('LOCKSS-O-MATIC Password Reset', $message->getSubject());

        $matches = array();
        preg_match('/password reset code is ([0-9a-f]*)/', $message->getBody(), $matches);

        $code = $matches[1];
        $this->assertRegExp('/^[0-9a-f]{40}$/', $code);

        $crawler = $client->request('GET', '/reset/confirm');
        $button = $crawler->selectButton('Reset password');
        $form = $button->form(array(
            'user_reset_password[username]' => 'user@example.com',
            'user_reset_password[resetcode]' => $code,
            'user_reset_password[password][first]' => 'jibberjabber',
            'user_reset_password[password][second]' => 'jibberjabber',
        ));
        $crawler = $client->submit($form);
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("successfully changed")')->count());

        $crawler = $client->request('GET', '/login');
        $response = $client->getResponse();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Username")')->count());
        $button = $crawler->selectButton('login');
        $form = $button->form(array(
            '_username' => 'user@example.com',
            '_password' => 'jibberjabber',
        ));

        $crawler = $client->submit($form);
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("user@example.com")')->count());
    }

}
