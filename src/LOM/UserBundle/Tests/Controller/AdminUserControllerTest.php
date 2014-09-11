<?php

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
