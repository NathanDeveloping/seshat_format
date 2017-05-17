<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 15/05/17
 * Time: 09:19
 */

namespace seshatFormat\scripts;

/**
 * Class Station
 *  permet la récupération des différents
 *  champs lié à la station d'un formulaire (sampling point)
 *
 * (saisie de la station manuelle, saisie d'une station déjà connue, etc...)
 *
 * @package seshatFormat\scripts
 */
class Station
{

    private $nomFormulaire, $db, $uri;

    private $nbField, $fields;

    private $sampling_point_fullname, $sampling_point_abbreviation, $sampling_point_longitude, $sampling_point_latitude, $sampling_point_altitude, $sampling_point_description, $sampling_point_coordonate_system;

    /**
     * Station constructor.
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

    public function init() {
        if(!isset($this->db)) {
            return;
        }
        $tableCore = strtoupper($this->nomFormulaire) . "_CORE";
        $this->fields = $this->db->query(strtr('SELECT "SAMPLING_POINT", "SAMPLING_POINT_GROUP_SAMPLING_POINT_FULLNAME", "SAMPLING_POINT_GROUP_SAMPLING_POINT_ABBREVIATION",
         "SAMPLING_POINT_GROUP_SAMPLING_POINT_DESCRIPTION", "GPS_GROUP_SAMPLING_POINT_GPS_ALT", "GPS_GROUP_SAMPLING_POINT_GPS_LAT", "GPS_GROUP_SAMPLING_POINT_GPS_LAT2", "GPS_GROUP_SAMPLING_POINT_GPS_LNG2",
         "GPS_GROUP_SAMPLING_POINT_GPS_LNG", "NEW_SAMPLING_POINT", "SAMPLING_POINT_GROUP_MANUAL_GPS" FROM "{tableCore}" WHERE "_URI" = \'{URI}\'', [
            "{tableCore}" => $tableCore,
            "{URI}" => $this->uri
        ]))->fetchAll();
        $this->nbField = 1;
        //$this->echoAll();
        $this->sortData();
    }

    public function getAdditionalData() {

    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function echoAll() {
        echo $this->fields[0]["SAMPLING_POINT"] . "\n";
        echo $this->fields[0]["SAMPLING_POINT_GROUP_SAMPLING_POINT_FULLNAME"] . "\n";
        echo $this->fields[0]["SAMPLING_POINT_GROUP_SAMPLING_POINT_ABBREVIATION"] . "\n";
        echo $this->fields[0]['SAMPLING_POINT_GROUP_SAMPLING_POINT_DESCRIPTION'] . "\n";
        echo $this->fields[0]['GPS_GROUP_SAMPLING_POINT_GPS_ALT'] . "\n";
        echo $this->fields[0]['GPS_GROUP_SAMPLING_POINT_GPS_LAT'] . "\n";
        echo $this->fields[0]['GPS_GROUP_SAMPLING_POINT_GPS_LAT2'] . "\n";
        echo $this->fields[0]['GPS_GROUP_SAMPLING_POINT_GPS_LNG2'] . "\n";
        echo $this->fields[0]['GPS_GROUP_SAMPLING_POINT_GPS_LNG'] . "\n";
        echo $this->fields[0]['NEW_SAMPLING_POINT'] . "\n";
        echo $this->fields[0]['SAMPLING_POINT_GROUP_MANUAL_GPS'] . "\n";
    }

    public function sortData() {
        if(isset($this->fields)) {
            $this->sampling_point_coordonate_system= "Lambert 93";
            if($this->fields[0]['NEW_SAMPLING_POINT'] == 'yes') {
                $this->sampling_point_fullname = $this->fields[0]["SAMPLING_POINT_GROUP_SAMPLING_POINT_FULLNAME"];
                $this->sampling_point_abbreviation = $this->fields[0]["SAMPLING_POINT_GROUP_SAMPLING_POINT_ABBREVIATION"];
                $this->sampling_point_description = $this->fields[0]['SAMPLING_POINT_GROUP_SAMPLING_POINT_DESCRIPTION'];
                if($this->fields[0]['SAMPLING_POINT_GROUP_MANUAL_GPS'] == 'yes') {
                    $this->sampling_point_latitude= $this->fields[0]['GPS_GROUP_SAMPLING_POINT_GPS_LAT2'];
                    $this->sampling_point_longitude = $this->fields[0]['GPS_GROUP_SAMPLING_POINT_GPS_LNG2'];
                    $this->sampling_point_altitude= $this->fields[0]['GPS_GROUP_SAMPLING_POINT_GPS_ALT2'];
                } else {
                    $this->sampling_point_latitude = $this->fields[0]['GPS_GROUP_SAMPLING_POINT_GPS_LAT'];
                    $this->sampling_point_longitude = $this->fields[0]['GPS_GROUP_SAMPLING_POINT_GPS_LNG'];
                    $this->sampling_point_altitude= $this->fields[0]['GPS_GROUP_SAMPLING_POINT_GPS_ALT'];
                }
            } else {
                //TODO:rechercher base mongo
            }
        }
    }
}