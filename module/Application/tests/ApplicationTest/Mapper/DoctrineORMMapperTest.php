<?php

namespace ApplicationTest\Mapper;

class DoctrineORMMapperTest extends \ApplicationTest\Framework\DoctrineORMModuleTestCase
{

    /**
     *
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $_repo;

    /**
     * 
     */
    public function setUp()
    {
        parent::setUp();
        $this->_repo = $this->_em->getRepository('Application\Entity\User');
    }

    /**
     *
     */
    public function testPersist()
    {
        $user = new \Application\Entity\User();
        $user->setId(1);
        $user->setEmail('meine@email.de');
        $this->_em->persist($user);
        $this->_em->flush();
        $result = $this->_repo->find(1);
        $this->assertEquals($user, $result);
    }

}
