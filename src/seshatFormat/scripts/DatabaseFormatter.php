<?php

namespace seshatFormat\scripts;
use seshatFormat\util\Logger;


class DatabaseFormatter
{

    private $db, $logger;

    public function __construct() {
        $this->logger = new Logger;
        $config = parse_ini_file("config/config.ini");
        if(!isset($config['host']) || !isset($config['port']) ||
            !isset($config['dbname']) || !isset($config['user']) || !isset($config['password'])) {
            $this->logger->alert("Fichier de configuration \"config/config.ini\" mal configuré.");
        } else {
            $this->db = pg_connect($config['host'] . " " . $config['port'] . " " . $config['dbname']
                . $config['user'] . " " . $config['password']);
        }
    }


}