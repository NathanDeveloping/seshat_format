<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 15/05/17
 * Time: 10:39
 */

namespace seshatFormat\scripts;

use PHPExcel;
use PDO;
use seshatFormat\util\Dictionnaire;
use seshatFormat\util\Logger;
use PHPExcel_Writer_Excel2007;
use PHPExcel_Style_Fill;

/**
 * Class SingleFormFormatter
 * permet le formattage d'un formulaire
 * bien spécifique et unique
 *
 * @package seshatFormat\scripts
 */
class SingleFormFormatter
{

    /**
     * nom du formulaire vierge
     * et nom du formulaire itéré
     */
    private $nomForm, $instanceName;

    /**
     * nombres de champs associés pour créer le squelette
     * du fichier Excel
     * valeurs associées lors du passage dans le rowToArray()
     */
    private $operators, $nbInstitutions, $nbSamplingPoints, $nbMeasurements, $nbSampleSuffix, $nbSampleKind;

    /**
     * colonne et ligne actuelle
     * mis à jour lors du addNextLine et addNextColumn
     */
    private $currentColumn, $currentLine;

    /**
     * fichier excel sur lequel on travail
     * URI du formulaire sur lequel on travail
     */
    private $currentExcelObject, $URI, $db;

    /**
     * variable de stockage des retours de la requête
     * @var
     */
    private $formData;

    /**
     * SingleFormFormatter constructor.
     * @param $nomForm
     *          nom du formulaire vierge
     * @param $uri
     *          uuid dans la base
     * @param $instanceName
     *          nom spécifique du formulaire
     */
    public function __construct($nomForm, $uri, $instanceName)
    {
        $this->nomForm = $nomForm;
        $this->URI = $uri;
        $this->instanceName = $instanceName;
        $this->db = DatabaseConnexion::getInstance()->getDB();
        $this->init();
    }

    /**
     * récupère les données d'un formulaire en particulier
     *
     * @param $nomFormulaire
     *          nom du formulaire duquel extraire les données
     */
    public function getDataFields() {
        if(!isset($this->db)) {
            return;
        }
        $this->formData= $this->db->query(strtr("SELECT * FROM \"{tableName}\" WHERE \"_URI\" = '{uri}' ;", [
            "{tableName}" => strtoupper($this->nomForm) . "_CORE",
            "{uri}" => $this->URI
        ]))->fetch();
    }

    /**
     * formattage d'un formulaire particulier
     * en tableau pour faciliter sa conversion
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
     * crée le fichier excel associé
     *
     * @param $phpExcelObject
     *          object phpExcel précédemment crée
     */
    public function format() {
        var_dump($this->formData);
        echo "nb fields : " . $this->operators->nbField;
        if($this->formData) {
            /**
             * données
             */
            $date = date("Y-m-d", strtotime($this->formData['DATE']));
            $scientificField = $this->formData['INTRODUCTION_GROUP_SCIENTIFIC_FIELD'];
            $row = $this->operators->fields;
            $res = $this->splitNames($row[0]["OPERATOR"]);
            $comment = $this->formData['METHODOLOGY'];
            $sampleKind = $this->formData['SAMPLE_KIND'];
            /**
             * début mise en page
             */
            $this->currentExcelObject->getActiveSheet()->setTitle('INTRO');
            $this->addNextLine("TITLE");
            $this->insertEmptyLine();
            $this->addNextLine("DATA DESCRIPTION");
            $this->addNextLine("KEYWORD");
            $this->addNextLine("FILE CREATOR");
            $this->addNextColumn($res[0] . " " . $res[1]);
            $this->resetColumn();
            $this->addNextLine("NAME");
            $this->addNextColumn($res[1]);
            $this->resetColumn();
            $this->addNextLine("FIRST NAME");
            $this->addNextColumn($res[0]);
            $this->resetColumn();
            $this->addNextLine("MAIL");
            for ($i = 1; $i < $this->operators->nbField; $i++) {
                $res = $this->splitNames($row[$i]["OPERATOR"]);
                $this->addNextLine("NAME");
                $this->addNextColumn($res[1]);
                $this->resetColumn();
                $this->addNextLine("FIRST NAME");
                $this->addNextColumn($res[0]);
                $this->resetColumn();
                $this->addNextLine("MAIL");
            }
            $this->addNextLine("CREATION DATE");
            $this->addNextColumn($date);
            $this->resetColumn();
            $this->addNextLine("LANGUAGE");
            $this->addNextColumn("francais");
            $this->resetColumn();
            $this->addNextLine("PROJECT NAME");
            for ($i = 0; $i < $this->nbInstitutions; $i++) {
                $this->addNextLine("INSTITUTION");
            }
            $this->addNextLine("SCIENTIFIC FIELD");
            $this->addNextColumn($scientificField);
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
            $this->cellColor("A" . $this->getCurrentLine() . ":" . $this->getCurrentCell());
            $this->resetColumn();
            for ($i = 0; $i < $this->nbSamplingPoints; $i++) {
                $this->addNextLine("SAMPLING_POINT");
                $this->cellColor($this->getCurrentCell());
                $this->resetColumn();
            }
            $this->insertEmptyLine();
            $this->cellColor($this->getCurrentCell());
            $this->insertEmptyLine();
            $this->cellColor($this->getCurrentCell());
            $this->addNextLine("SAMPLING DATE");
            $this->cellColor($this->getCurrentCell());
            $this->addNextColumn($date);
            $this->insertEmptyLine();
            $this->insertEmptyLine();
            $this->insertEmptyLine();
            $this->insertEmptyLine();
            $this->addNextColumn(null);
            $this->addNextColumn("ABBREVIATION");
            $this->cellColor($this->getCurrentCell());
            $this->resetColumn();
            for ($i = 0; $i < $this->nbSampleKind; $i++) {
                $this->addNextLine("SAMPLE KIND");
                $this->cellColor($this->getCurrentCell());
                $this->addNextColumn(Dictionnaire::getInstance()->getTraduction($sampleKind));
                $this->addNextColumn(Dictionnaire::getInstance()->getTraduction(Dictionnaire::getInstance()->getTraduction($sampleKind)));
                $this->resetColumn();
            }
            $this->insertEmptyLine();
            $this->cellColor($this->getCurrentCell());
            $this->insertEmptyLine();
            $this->cellColor($this->getCurrentCell());
            $this->insertEmptyLine();
            $this->cellColor($this->getCurrentCell());
            $this->addNextColumn("");
            $this->addNextColumn("ABBREVIATION");
            $this->cellColor($this->getCurrentCell());
            $this->resetColumn();
            for ($i = 0; $i < $this->nbSampleSuffix; $i++) {
                $this->addNextLine("SAMPLE SUFFIX");
                $this->cellColor($this->getCurrentCell());
            }
            $this->insertEmptyLine();
            $this->insertEmptyLine();
            $this->addNextColumn("NATURE OF MEASUREMENT");
            $this->addNextColumn("ABBREVIATION");
            $this->addNextColumn("UNIT");
            $this->cellColor("B" . $this->getCurrentLine() . ":" . $this->getCurrentCell());
            $this->resetColumn();
            for ($i = 0; $i < $this->nbMeasurements; $i++) {
                $this->addNextLine("MEASUREMENT");
                $this->cellColor($this->getCurrentCell());
            }
            $this->insertEmptyLine();
            $this->addNextLine("METHODOLOGY");
            $this->cellColor($this->getCurrentCell());
            $this->addNextColumn("sampling method");
            $this->resetColumn();
            $this->addNextLine("METHODOLOGY");
            $this->cellColor($this->getCurrentCell());
            $this->addNextColumn("sample conditionning");
            $this->resetColumn();
            $this->addNextLine("METHODOLOGY");
            $this->cellColor($this->getCurrentCell());
            $this->addNextColumn("analysis or measurement method(s)");
            $this->resetColumn();
            $this->addNextLine("METHODOLOGY");
            $this->cellColor($this->getCurrentCell());
            $this->addNextColumn("field campaign report");
            $this->resetColumn();
            $this->addNextLine("METHODOLOGY");
            $this->cellColor($this->getCurrentCell());
            $this->addNextColumn("sample storage");
            $this->resetColumn();
            $this->addNextLine("METHODOLOGY");
            $this->cellColor($this->getCurrentCell());
            $this->addNextColumn("comments");
            $this->addNextColumn($comment);
            $this->resetColumn();
            $objWriter = new PHPExcel_Writer_Excel2007($this->currentExcelObject);
            $objWriter->save($this->instanceName . ".xlsx");
        } else {
            Logger::getInstance()->alert("SingleFormFormatter : données introuvables.");
        }
    }

    /**
     * complète avec du texte la case de colonne actuelle
     * et de ligne suivante
     * @param $label
     *          contenu de la case
     */
    public function addNextLine($label) {
        $this->currentLine++;
        $this->currentExcelObject->getActiveSheet()->SetCellValue($this->currentColumn . $this->currentLine, $label);
    }

    /**
     * complète avec du texte la case de colonne suivante
     * et de ligne actuelle
     * @param $label
     */
    public function addNextColumn($label) {
        $this->currentColumn++;
        $this->currentExcelObject->getActiveSheet()->SetCellValue($this->currentColumn . $this->currentLine, $label);
    }

    /**
     * eq. retour chariot (CR)
     */
    public function resetColumn() {
        $this->currentColumn = 'A';
    }

    /**
     * insertion d'une ligne vide
     */
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
        $this->getDataFields();
        $this->operators = new Operator($this->nomForm, $this->URI);
        $this->nbInstitutions = 3;
        $this->nbMeasurements = 4;
        $this->nbSampleSuffix = 2;
        $this->nbSampleKind=2;
        $this->nbSamplingPoints = 4;
        $this->currentColumn = 'A';
        $this->currentLine = 0;
        $this->currentExcelObject = new PHPExcel();
        $this->currentExcelObject ->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $this->currentExcelObject ->getActiveSheet()->getColumnDimension('B')->setWidth(45);
        $this->currentExcelObject ->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->currentExcelObject ->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->currentExcelObject ->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $this->currentExcelObject ->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->currentExcelObject ->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $this->currentExcelObject ->getActiveSheet()->getColumnDimension('H')->setWidth(45);
    }

    /**
     * change la couleur du/des cellules spécifiées
     * par la couleur bleu de base (#4D4DA1)
     * (ex : A1 ou A1:E1)
     * @param $cells
     *      la ou les cellules concernées
     */
    private function cellColor($cells){
        $this->currentExcelObject->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => "4d4da1"
            )
        ));
        $this->currentExcelObject->getActiveSheet()->getStyle($cells)->getFont()->getColor()->setRGB('fffff');
    }

    /**
     * retourne les coordonnées de la case actuelle
     * @return string
     *          coordonnées cellule
     */
    public function getCurrentCell() {
        return $this->currentColumn . $this->currentLine;
    }

    /**
     * retourne la ligne de la case actuelle
     * @return mixed
     */
    public function getCurrentLine() {
        return $this->currentLine;
    }

    /**
     * Permet de séparer nom et prénoms
     * à partir d'un string de ce type : (pierre_benoit) devient Pierre(indice 0) Benoit(indice 1)
     *
     * @param $underscoredName
     * @return array
     *
     */
    public function splitNames($underscoredName) {
        $res = explode("_", $underscoredName);
        $res[0] = ucfirst($res[0]);
        $res[1] = ucfirst($res[1]);
        return $res;
    }

}