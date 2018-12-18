<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testList()
    {
        $client = static::createClient();

        $client->request('GET', '/users');
        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertNotContains('Liste des utilisateurs', $client->getResponse()->getContent());
    }

    public function testCreate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/users/create');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Créer un utilisateur', $client->getResponse()->getContent());

        $form = $crawler->selectButton('Ajouter')->form();

        // set some values
        $form['user[username]'] = 'Lucas';
        $form['user[password][first]'] = 'WrongPassword';
        $form['user[password][second]'] = 'WrongPassword2';
        $form['user[email]'] = 'WrongPassword2';

        $this->assertEquals(0,
            $crawler->filter("span.glyphicon-exclamation-sign")->count()
        );

        // submit the form
        $crawler = $client->submit($form);

        $this->assertEquals(2,
            $crawler->filter("span.glyphicon-exclamation-sign")->count()
        );
        $this->assertContains(
            'Les deux mots de passe doivent correspondre.',
            $client->getResponse()->getContent()
        );
    }

    public function testEditUser()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();

        $form['_username'] = 'test';
        $form['_password'] = 'test';

        $client->submit($form);
        $client->followRedirect();

        //Find newly created task
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findBy(array('username' => 'test'))
        ;
        $this->assertCount(1, $user);

        $uri = 'users/'. $user[0]->getId() . '/edit';

        $crawler = $client->request('GET', $uri);

        $form = $crawler->selectButton('Modifier')->form();

        $test = 'test';
        $form['user[password][first]'] = $test;
        $form['user[password][second]'] = $test;

        $client->submit($form);
        $client->followRedirect();

        $this->assertRegExp('/\/users$/', $client->getRequest()->getUri());
        $this->assertContains(
            "utilisateur a bien été modifié",
            $client->getResponse()->getContent()
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}
