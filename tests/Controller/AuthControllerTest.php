<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class AuthControllerTest extends WebTestCase
{
    public function testRegistration(): void
    {
        $this->registration(true, 'Test user', 'test@example.com', '28942984239', 'Pa$$w0rd');
        $this->registration(false, 'Test user', 'test@example', '28942984239', 'Pa$$w0rd');
    }

    protected function registration(bool $success, string $name, string $email, string $phone, string $password): void
    {
        $client = static::createClient();

        $buttonCrawlerNode = $client->request('GET', '/registration')->selectButton('submit');
        $form = $buttonCrawlerNode->form();

        $client->submit($form, [
            'form[realname]' => $name,
            'form[email]' => $email,
            'form[phone]' => $phone,
            'form[password]' => $password,
        ]);

        $user = $client->getContainer()->get('doctrine')->getConnection()->executeQuery('SELECT id FROM users WHERE username = :username', [
            'username' => $email,
        ])->fetch();

        if ($success) {
            static::assertNotEmpty($user);
        } else {
            static::assertEmpty($user);
        }
    }
}
