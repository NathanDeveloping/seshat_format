<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 15/05/17
 * Time: 09:50
 */

namespace seshatFormat\scripts;

use seshatFormat\util\DatabaseConnexion;
use seshatFormat\util\Logger;

/**
 * Class FormFormatter
 * permet le formattage des formulaires
 * du même nom
 * @package seshatFormat\scripts
 */
class FormFormatter
{

    private $nomForm, $formList;

    private $db;


    public function __construct($nomForm) {
        $this->nomForm = $nomForm;
        $this->db = DatabaseConnexion::getInstance()->getDB();
    }

    /**
     * récupère les différents formulaires du même nom
     * stockés dans la BDD
     */
    public function getDistinctForms() {
        if(!isset($this->db)) {
            return;
        }
        try {
            $this->formList = $this->db->query(strtr("SELECT DISTINCT \"_URI\", \"META_INSTANCE_NAME\" FROM \"{tableName}\"", [
                "{tableName}" => strtoupper($this->nomForm) . "_CORE"
            ]));
        } catch (\Exception $e) {
            Logger::getInstance()->alert("PDOException : " . $e->getMessage());
        }

    }

    /**
     * lance le formattage sur tous les formulaires
     * provenant du même formulaire vierge
     */
    public function formatAll() {
        $this->getDistinctForms();
        var_dump($this->formList);
        if($this->formList) {
            foreach ($this->formList as $formRow) {
                $formFormatter = new SingleFormFormatter($this->nomForm, $formRow['_URI'], $formRow['META_INSTANCE_NAME']);
                $formFormatter->format();
            }
        } else {
            Logger::getInstance()->alert("FormFormatter : erreur de requête (formatAll())\n");
        }
    }

}