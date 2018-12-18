<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->assertRegExp('/\/login$/', $client->getRequest()->getUri());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Se connecter')->form();

        // set some values
        $form['_username'] = 'Lucas';
        $form['_password'] = 'WrongPassword';

        $this->assertEquals(0,
            $crawler->filter("div.alert-danger")->count()
        );

        // submit the form
        $client->submit($form);
        $crawler = $client->followRedirect();


        $this->assertEquals(1,
            $crawler->filter("div.alert-danger")->count()
        );
        $this->assertContains(
            'Invalid credentials.',
            $client->getResponse()->getContent()
        );

        $form['_username'] = 'test';
        $form['_password'] = 'test';

        $this->assertNotContains(
            'Bienvenue sur Todo List',
            $client->getResponse()->getContent()
        );
        $client->submit($form);
        $client->followRedirect();
        $this->assertContains(
            'Bienvenue sur Todo List',
            $client->getResponse()->getContent()
        );
        $this->assertRegExp('/\/$/', $client->getRequest()->getUri());

        $client->request('GET', '/logout');
        $client->followRedirect();
        $this->assertRegExp('/\/login$/', $client->getRequest()->getUri());

    }
}
