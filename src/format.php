<?php
require '../vendor/autoload.php';
use seshatFormat\scripts\DatabaseFormatter;
use seshatFormat\scripts\FormFormatter;

if(file_exists("config/config.ini")) {
    echo "Lancement du script de formattage des données... \n";
    $df = new DatabaseFormatter();
    $df->formatAllData();
} else {
    echo "Fichier \"config/config.ini\" inexistant. Veuillez le créer et spécifier les champs 'host', 'port', 'dbname', 'user', 'password' et 'logsDir'";
}