<?php

$params = require_once 'migrations-db.php';

$db = \Doctrine\DBAL\DriverManager::getConnection($params);

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($db),
    'dialog' => new \Symfony\Component\Console\Helper\DialogHelper(),
));
