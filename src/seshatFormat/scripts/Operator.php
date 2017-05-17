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
 * Class Operator
 *  permet la récupération des différents
 *  champs "OPERATOR" d'un formulaire.
 *
 * @package seshatFormat\scripts
 */
class Operator
{

    private $nomFormulaire, $db, $uri;

    private $nbField, $fields;

    /**
     * Operator constructor.
     * @param $nomForm
     * @param $uri
     */
    public function __construct($nomForm, $uri)
    {
        $this->db = DatabaseConnexion::getInstance()->getDB();
        $this->nomFormulaire = $nomForm;
        $this->uri = $uri;
        $this->init();
    }

    /**
     * récupère les champs FILE_CREATOR et OPERATOR
     * d'un formulaire ainsi que leur nombre
     */
    public function init() {
        if(!isset($this->db)) {
            return;
        }
        $tableCore = strtoupper($this->nomFormulaire) . "_CORE";
        $table = strtoupper($this->nomFormulaire) . "_OPERATOR_REPEAT";
        $this->fields = $this->db->query(strtr('SELECT "OPERATOR" FROM "{tableRepeat}" WHERE "_PARENT_AURI" = \'{URI}\' UNION (SELECT "CREATOR_OPERATOR_GROUP_FILE_CREATOR" FROM "{tableCore}" WHERE "_URI" = \'{URI}\')', [
            "{tableRepeat}" => $table,
            "{tableCore}" => $tableCore,
            "{URI}" => $this->uri
        ]))->fetchAll();
        $this->nbField = count($this->fields);
    }

    public function getAdditionalData() {

    }

    public function __get($name)
    {
        return $this->$name;
    }
}