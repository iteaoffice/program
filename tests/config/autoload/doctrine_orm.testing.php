<?php

return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params'      => array(
                    'host'          => (getenv('DEBRANOVA_HOST') === 'travis' ? '127.0.0.1' : 'localhost'),
                    'port'          => '3306',
                    'user'          => (getenv('DEBRANOVA_HOST') === 'travis' ? 'root' : 'phpci'),
                    'password'      => (getenv('DEBRANOVA_HOST') === 'travis' ? '' : 'AB4VF'),
                    'dbname'        => (getenv('DEBRANOVA_HOST') === 'travis' ? 'myapp_test' : 'test'),
                    'driverOptions' => array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
                    )
                )
            )
        ),
    ),
);
