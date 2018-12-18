<?php
/**
 * @description :
 * @package : PhpStorm.
 * @Author : quent
 * @date: 20/04/2018
 * @time: 17:18
 */

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testTask()
    {
        $task = new Task();
        $title = "Test Title";
        $task->setTitle($title);
        $this->assertEquals($title, $task->getTitle());

        $content = 'Test Content';
        $task->setContent($content);
        $this->assertEquals($content, $task->getContent());

        $this->assertTrue($task->getCreatedAt() instanceof \DateTime);
        $this->assertTrue(is_bool($task->isDone()));
    }
}