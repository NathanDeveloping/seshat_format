<?php
require '../vendor/autoload.php';
use seshatFormat\scripts\DatabaseFormatter;
use seshatFormat\scripts\FormFormatter;

if(file_exists("config/config.ini")) {
    $config = parse_ini_file("config/config.ini");
    if(!isset($config['host']) ||
        !isset($config['port']) ||
        !isset($config['dbname']) ||
        !isset($config['user']) ||
        !isset($config['password']) ||
        !isset($config['logsDir']) ||
        !isset($config['destinationFolder'])) {
        echo "Fichier \"config/config.ini\" inexistant. Veuillez le créer et spécifier les champs 'host', 'port', 'dbname', 'user', 'password', 'logsDir' et 'destinationFolder'";
    } else {
        echo "Lancement du script de formattage des données... \n";
        $df = new DatabaseFormatter();
        $df->formatAllData();
    }
} else {
    echo "Fichier \"config/config.ini\" inexistant. Veuillez le créer et spécifier les champs 'host', 'port', 'dbname', 'user', 'password', 'logsDir' et 'destinationFolder'";
}