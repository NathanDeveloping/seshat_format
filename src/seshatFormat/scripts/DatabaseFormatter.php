<?php

namespace seshatFormat\scripts;
use seshatFormat\util\Logger;
use PHPExcel;
use PHPExcel_Writer_Excel2007;
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

    /**
     * $db connexion base de données
     * @var PDO
     * $logger log (console et fichier text)
     */
    private $db, $logger;

    private $dataList;


    /**
     * DatabaseFormatter constructor.
     *
     * initialise la connexion à la DB
     */
    public function __construct() {
        $this->logger = Logger::getInstance();
        $this->db = DatabaseConnexion::getInstance()->getDB();
        $this->init();
    }

    /**
     * récupère les tables de données
     * correspondant aux différents formulaires (*_CORE)
     */
    public function getDataTables() {
        if(!isset($this->db)) {
            return;
        }
        $this->dataList = $this->db->query('SELECT table_name FROM information_schema.tables WHERE table_schema=\'public\' AND table_name LIKE \'%_CORE\';');
    }


    /**
     * Lance la procédure de formatting
     * des données des différents formulaires
     */
    public function formatAllData() {
        if(isset($dataList)) {
            foreach ($dataList as $form) {

            }
        }
    }



}