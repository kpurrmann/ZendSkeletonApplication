<?php

namespace ApplicationTest\Entity;

class UserTest extends \ApplicationTest\Framework\TestCase
{

    /**
     *
     * @var \Application\Entity\User
     */
    protected $_entity;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->_entity = new \Application\Entity\User();
    }

    /**
     * @covers \Application\Entity\User::getEmail
     * @covers \Application\Entity\User::setEmail
     */
    public function testCanGetSetEmail()
    {
        $email = 'meine@email.de';
        $this->assertEmpty($this->_entity->getEmail());
        $this->_entity->setEmail($email);
        $this->assertEquals($email, $this->_entity->getEmail());
    }

    /**
     * @covers \Application\Entity\User::getId
     * @covers \Application\Entity\User::setId
     */
    public function testCanGetSetId()
    {
        $id = 1;
        $this->assertEmpty($this->_entity->getId());
        $this->_entity->setId($id);
        $this->assertEquals($id, $this->_entity->getId());
    }

}
