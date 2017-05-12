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

    /**
     * nombres de champs associés pour créer le squelette
     * du fichier Excel
     * valeurs associées lors du passage dans le rowToArray()
     */
    private $nbOperators, $nbInstitutions, $nbSamplingPoints, $nbMeasurement, $nbSampleSuffix, $nbSampleKind;

    /**
     * colonne et ligne actuelle
     * mis à jour lors du addNextLine et addNextColumn
     */
    private $currentColumn, $currentLine;

    private $currentExcelObject;


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
        $this->init();
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
            $this->init();
        }
    }

    /**
     * crée le squelette du fichier excel
     * (les différents intitulé des champs)
     *
     * @param $phpExcelObject
     *          object phpExcel précédemment crée
     */
    public function createSkeleton() {
        $this->currentExcelObject->getActiveSheet()->setTitle('INTRO');
        $this->addNextLine("TITLE");
        $this->insertEmptyLine();
        $this->addNextLine("DATA DESCRIPTION");
        $this->addNextLine("KEYWORD");
        $this->addNextLine("FILE CREATOR");
        for($i = 0; $i < $this->nbOperators; $i++) {
            $this->addNextLine("NAME");
            $this->addNextLine("FIRST NAME");
            $this->addNextLine("MAIL");
        }
        $this->addNextLine("CREATION DATE");
        $this->addNextLine("LANGUAGE");
        $this->addNextLine("PROJECT NAME");
        for($i = 0; $i < $this->nbInstitutions; $i++) {
            $this->addNextLine("INSTITUTION");
        }
        $this->addNextLine("SCIENTIFIC FIELD");
        $this->insertEmptyLine();
        $this->insertEmptyLine();
        $this->insertEmptyLine();
        $this->addNextColumn("SAMPLING POINTS");
        $this->addNextColumn("COORDONATE SYSTEM");
        $this->addNextColumn("ABBREVIATION");
        $this->addNextColumn("LONGITUDE");
        $this->addNextColumn("LATITUDE");
        $this->addNextColumn("ELEVATION (m)");
        $this->addNextColumn("DESCRIPTION");
        $this->resetColumn();
        for($i = 0; $i < $this->nbSamplingPoints; $i++) {
            $this->addNextLine("SAMPLING_POINT");
        }
        $this->insertEmptyLine();
        $this->insertEmptyLine();
        $this->addNextLine("SAMPLING DATE");
        $this->insertEmptyLine();
        $this->insertEmptyLine();
        $this->insertEmptyLine();
        $this->insertEmptyLine();
        for($i = 0; $i < $this->nbSampleKind; $i++) {
            $this->addNextLine("SAMPLE KIND");
        }
        $this->insertEmptyLine();
        $this->addNextColumn("");
        $this->addNextColumn("ABBREVIATION");
        $this->resetColumn();
        for($i = 0; $i < $this->nbSampleSuffix; $i++) {
            $this->addNextLine("SAMPLE SUFFIX");
        }
        $this->insertEmptyLine();
        $this->insertEmptyLine();
        $this->addNextColumn("NATURE OF MEASUREMENT");
        $this->addNextColumn("ABBREVIATION");
        $this->addNextColumn("UNIT");
        $this->resetColumn();
        for($i = 0; $i < $this->nbMeasurement; $i++) {
            $this->addNextLine("MEASUREMENT");
        }
        $this->insertEmptyLine();
        $this->addNextLine("METHODOLOGY"); $this->addNextColumn("sampling method"); $this->resetColumn();
        $this->addNextLine("METHODOLOGY"); $this->addNextColumn("sample conditionning"); $this->resetColumn();
        $this->addNextLine("METHODOLOGY"); $this->addNextColumn("analysis or measurement method(s)"); $this->resetColumn();
        $this->addNextLine("METHODOLOGY"); $this->addNextColumn("field campaign report"); $this->resetColumn();
        $this->addNextLine("METHODOLOGY"); $this->addNextColumn("sample storage"); $this->resetColumn();
        $this->addNextLine("METHODOLOGY"); $this->addNextColumn("comments"); $this->resetColumn();
        $objWriter = new PHPExcel_Writer_Excel2007($this->currentExcelObject);
        $objWriter->save("test.xlsx");
    }

    /**
     * @param $label
     */
    public function addNextLine($label) {
        $this->currentExcelObject->getActiveSheet()->SetCellValue($this->currentColumn . $this->currentLine, $label);
        $this->currentLine++;
    }

    public function addNextColumn($label) {
        $this->currentExcelObject->getActiveSheet()->SetCellValue($this->currentColumn . $this->currentLine, $label);
        $this->currentColumn++;
    }

    public function resetColumn() {
        $this->currentColumn = 'A';
    }

    public function insertEmptyLine() {
        $this->resetColumn();
        $this->addNextLine(null);
    }

    /**
     * initialise les variables nb champs
     * lors du passage au traitement d'un
     * nouveau formulaire
     */
    public function init() {
        $this->nbInstitutions = 0;
        $this->nbMeasurements = 0;
        $this->nbOperators = 0;
        $this->nbSamplingPoints = 0;
        $this->currentColumn = 'A';
        $this->currentLine = 1;
        $this->currentExcelObject = new PHPExcel();
    }


}