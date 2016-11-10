<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class AuthControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $app = new \AppKernel('test', false);
        $app->boot();

        self::assertTrue($app->handle(Request::create('/login'))->isSuccessful());
        self::assertTrue($app->handle(Request::create('/register/'))->isSuccessful());
    }

    public function testRegistration()
    {
        $this->registration(true, 'Test user', 'test@example.com', '28942984239', 'Pa$$w0rd');
        $this->registration(false, 'Test user', 'test@example', '28942984239', 'Pa$$w0rd');
    }

    protected function registration(bool $success, string $name, string $email, string $phone, string $password)
    {
        $client = static::createClient();

        $buttonCrawlerNode = $client->request('GET', '/register/')->selectButton('submit');
        $form = $buttonCrawlerNode->form();

        $client->submit($form, [
            'fos_user_registration_form[realname]' => $name,
            'fos_user_registration_form[email]' => $email,
            'fos_user_registration_form[phone]' => $phone,
            'fos_user_registration_form[plainPassword]' => $password,
        ]);

        $user = $client->getContainer()->get('doctrine.orm.entity_manager')->getConnection()->executeQuery('SELECT id FROM user WHERE email = :email', [
            'email' => $email,
        ])->fetch();

        if ($success) {
            static::assertNotEmpty($user);
        } else {
            static::assertEmpty($user);
        }
    }
}
