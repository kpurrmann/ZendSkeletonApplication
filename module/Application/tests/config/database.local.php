<?php

return array(
   'application-test' => array(
      'database_schema_up' => 'schema_up.sql',
      'database_schema_down' => 'schema_down.sql',
   ),
   'doctrine' => array(
      'connection' => array(
         'orm_default' => array(
            'driverClass' => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
            'params' => array(
               'memory' => true,
            )
         )
      ),
   ),
);