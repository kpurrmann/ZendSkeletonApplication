<?php

namespace ApplicationTest\Framework;

class DoctrineORMModuleTestCase extends TestCase
{

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $_em;
    protected $_conn;

    public function setUp()
    {
        parent::setUp();
        $this->dbSchemaDown();
        $this->dbSchemaUp();
    }

    protected function dbSchemaDown()
    {
        $this->importSchema(__DIR__ . '/../../../../../data/' . $this->getOptions()->getDatabaseSchemaDown());
    }

    protected function dbSchemaUp()
    {
        $this->importSchema(__DIR__ . '/../../../../../data/' . $this->getOptions()->getDatabaseSchemaUp());
    }

    protected function importSchema($file)
    {
        $conn = $this->getDBALConnection();
        $sqlfile = explode(';', file_get_contents($file));
        foreach ($sqlfile as $sqlStmt) {
            $sqlStmt = trim($sqlStmt);
            if (!empty($sqlStmt)) {
                $conn->executeQuery($sqlStmt);
            }
        }
    }

    protected function getEntityManager()
    {
        if (is_null($this->_em)) {
            $this->_em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }
        return $this->_em;
    }

    protected function getDBALConnection()
    {
        if (is_null($this->_conn)) {
            $this->_conn = $this->getEntityManager()->getConnection();
        }
        return $this->_conn;
    }

}