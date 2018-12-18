<?php
/**
 * @description :
 * @package : PhpStorm.
 * @Author : quent
 * @date: 20/04/2018
 * @time: 17:18
 */

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;

class UserTest extends TestCase
{
    public function testUser()
    {
        $user = new User();
        $username = "Test";
        $user->setUsername($username);
        $this->assertEquals($username, $user->getUsername());

        $content = 'Test Content';
        $user->setPassword($content);
        $this->assertEquals($content, $user->getPassword());

        $bool = true;
        $user->setIsAdmin($bool);
        $this->assertEquals($bool, $user->isAdmin());

        $this->assertTrue($user->getTasks() instanceof ArrayCollection);
        $this->assertTrue(in_array('ROLE_USER', $user->getRoles()));
    }
}