<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
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

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();

        $form['_username'] = 'test';
        $form['_password'] = 'test';

        $client->submit($form);
        $client->followRedirect();

        $client->request('GET', '/tasks');

        $this->assertRegExp('/\/tasks$/', $client->getRequest()->getUri());
    }

    public function testCreateAndEdit()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();

        $form['_username'] = 'test';
        $form['_password'] = 'test';

        $client->submit($form);
        $client->followRedirect();

        $crawler = $client->request('GET', '/tasks/create');

        //Create task
        $this->assertRegExp('/\/tasks\/create$/', $client->getRequest()->getUri());

        $form = $crawler->selectButton('Ajouter')->form();

        $form['task[title]'] = 'test title';
        $form['task[content]'] = 'test content';

        $client->submit($form);
        $client->followRedirect();

        $this->assertRegExp('/\/tasks$/', $client->getRequest()->getUri());

        $this->assertContains(
            'La tâche a été bien été ajoutée.',
            $client->getResponse()->getContent()
        );

        //Find newly created task
        $task = $this->entityManager
            ->getRepository(Task::class)
            ->findBy(array('title' => 'test title'))
        ;
        $this->assertCount(1, $task);

        //EDIT it !
        $uri = '/tasks/'. $task[0]->getId() .'/edit';

        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //$crawler = $client->followRedirect();

        $form = $crawler->selectButton('Modifier')->form();

        $form['task[content]'] = 'test content edited';

        $client->submit($form);
        $client->followRedirect();


        $this->assertRegExp('/\/tasks$/', $client->getRequest()->getUri());
        $this->assertContains(
            'La tâche a bien été modifiée.',
            $client->getResponse()->getContent()
        );

        //TOGGLE It !
        $uri = '/tasks/'. $task[0]->getId() .'/toggle';
        $client->request('GET', $uri);
        $client->followRedirect();
        $this->assertRegExp('/\/tasks$/', $client->getRequest()->getUri());
        $this->assertContains(
            'La tâche ' . $task[0]->getTitle() . ' a bien été marquée comme faite.',
            $client->getResponse()->getContent()
        );

        //DELETE IT !
        $uri = '/tasks/'. $task[0]->getId() .'/delete';
        $client->request('GET', $uri);
        $client->followRedirect();
        $this->assertRegExp('/\/tasks$/', $client->getRequest()->getUri());
        $this->assertContains(
            'La tâche a bien été supprimée.',
            $client->getResponse()->getContent()
        );

        //Check that it does not exist anymore
        $task = $this->entityManager
            ->getRepository(Task::class)
            ->findBy(array('title' => 'test title'))
        ;
        $this->assertCount(0, $task);
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
