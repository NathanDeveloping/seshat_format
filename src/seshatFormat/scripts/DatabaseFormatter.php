<?php

namespace seshatFormat\scripts;
use seshatFormat\util\Logger;
use Exception;
use PDO;

/**
 * Class DatabaseFormatter
 * @package seshatFormat\scripts
 *
 * permet la conversion de la DB en fichiers Excel
 * afin de permettre la modification ou l'ajout de
 * données plus facilement
 *
 */
class DatabaseFormatter
{

    private $db, $logger;

    /**
     * DatabaseFormatter constructor.
     *
     * initialise la connexion à la DB
     */
    public function __construct() {
        $this->logger = new Logger;
        $config = parse_ini_file("config/config.ini");
        if(!isset($config['host']) || !isset($config['port']) ||
            !isset($config['dbname']) || !isset($config['user']) || !isset($config['password'])) {
            $this->logger->alert("Fichier de configuration \"config/config.ini\" incorrect ou inexistant.");
        } else {
            try {
                $this->db = new PDO(strtr("pgsql:host={host};port={port};dbname={dbname};user={user};password={password}", [
                    "{host}" => $config['host'],
                    "{port}" => $config['port'],
                    "{dbname}" => $config['dbname'],
                    "{user}" => $config['user'],
                    "{password}" => $config['password']
                ]));
            } catch (Exception $e) {
                $this->logger->alert($e->getMessage());
            }
        }
    }

    /**
     * récupère les tables de données
     * correspondant aux différents formulaires (*_CORE)
     */
    public function getDataTables() {
        if(!isset($db)) {
            return;
        }
        return $db->query('SELECT table_name FROM information_schema.tables WHERE table_schema=\'public\' AND table_name LIKE \'%_CORE\';');
    }

    /**
     * récupère les données d'un formulaire en particulier
     *
     * @param $nomFormulaire
     *          nom du formulaire duquel extraire les données
     */
    public function getDataFields($nomFormulaire) {
        if(!isset($db)) {
            return;
        }
        return $db->query(strtr('SELECT * FROM {tableName} ;', [
            "{tableName}" => strtoupper($nomFormulaire) . "_CORE"
        ]));
    }

    /**
     * formattage d'une entrée d'une table de donnée
     * pour faciliter sa conversion en fichier tabulaire
     *
     * @param $row
     *          champ d'une table de donnée
     */
    public function rowToArray($row) {
        if(isset($row)) {

        }
    }


}