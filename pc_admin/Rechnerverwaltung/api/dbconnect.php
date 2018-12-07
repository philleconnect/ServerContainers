<?php
    if (file_exists('config/config.txt')) {
        $config = unserialize(file_get_contents('config/config.txt'));
    } else {
        $config = unserialize(file_get_contents('../config/config.txt'));
    }
    $database = mysqli_connect($config['database']['url'], $config['database']['user'], $config['database']['password'], $config['database']['name'])
    or die ("Datenbankfehler. Wir bitten dies zu entschuldigen.");
    mysqli_set_charset($database, 'utf8');
?>