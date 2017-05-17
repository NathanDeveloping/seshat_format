<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 15/05/17
 * Time: 09:19
 */

namespace seshatFormat\scripts;

use seshatFormat\util\DatabaseConnexion;

/**
 * Class Data
 *  permet la récupération des différents
 *  champs de données du formulaire
 *
 * @package seshatFormat\scripts
 */
class Data
{

    private $nomFormulaire, $db, $uri;

    private $nbField, $fields, $nbFieldValue;

    /**
     * Data constructor.
     * @param $nomForm
     * @param $uri
     */
    public function __construct($nomForm, $uri)
    {
        $this->db = DatabaseConnexion::getInstance()->getDB();
        $this->nomFormulaire = $nomForm;
        $this->uri = $uri;
        $this->fields = array();
        $this->init();
    }


    /**
     * récupère les champs de données
     */
    public function init() {
        if(!isset($this->db)) {
            return;
        }
        $tableCore = strtoupper($this->nomFormulaire) . "_CORE";
        $dataColumns = $this->db->query(strtr('SELECT column_name FROM information_schema.columns WHERE table_name = \'{tableCore}\' AND column_name LIKE \'DATA_DATA%\'', [
            "{tableCore}" => $tableCore,
        ]))->fetchAll();
        $columnList = "";
        $numItems = count($dataColumns);
        $i = 0;
        foreach($dataColumns as $val) {
            if(++$i === $numItems) {
                $columnList.= "\"" . $val[0]. "\"";
            } else {
                $columnList.= "\"" . $val[0] . "\", ";
            }
        }
        $data = $this->db->query(strtr('SELECT {columnList} FROM "{tableCore}" WHERE "_URI" = \'{URI}\'', [
            "{tableCore}" => $tableCore,
            "{URI}" => $this->uri,
            "{columnList}" => $columnList
        ]))->fetchAll();
        foreach ($dataColumns as $val) {
            if(isset($data[0][$val[0]])) {
                if(strpos($val[0], 'UNIT') === false) {
//                    echo "colonne : " . $val[0] . "\n";
//                    echo "nature : " . $this->clearName($val[0]) . "\n";
//                    echo "unit : " . $data[0][$val[0] . "_UNIT"] . "\n";
//                    echo "value :" . $data[0][$val[0]] . "\n";
                    $this->fields[] = array(
                        "NATURE" => $this->clearName($val[0]),
                        "UNIT" => $data[0][$val[0] . "_UNIT"],
                        "VALUE" => $data[0][$val[0]],
                    );
                }
            }
        }
        $this->nbField = count($this->fields);
        $this->nbFieldValue = 1;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function clearName($name) {
        $matches = array();
        //preg_match('/DATA_DATA_([A-Z1-9_]+)_UNIT/', $name, $matches);
        preg_match("/(?<=DATA_DATA_)(.*?)(?=_UNIT|$)/", $name, $matches);
        return $matches[0];
    }
}