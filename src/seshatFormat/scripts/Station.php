<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 15/05/17
 * Time: 09:19
 */

namespace seshatFormat\scripts;
use seshatFormat\util\DatabaseConnexion;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use Exception;
use seshatFormat\util\Logger;
use MongoDB\Driver\ReadPreference;

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

    private $nomFormulaire, $db, $mongo_db, $uri;

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
        ]));
        if(!$this->fields) {
            $this->fields = $this->db->query(strtr('SELECT "SAMPLING_POINT_GROUP_SAMPLING_POINT_FULLNAME", "SAMPLING_POINT_GROUP_SAMPLING_POINT_ABBREVIATION",
                "SAMPLING_POINT_GROUP_SAMPLING_POINT_DESCRIPTION", "GPS_GROUP_SAMPLING_POINT_GPS_ALT", "GPS_GROUP_SAMPLING_POINT_GPS_LAT", "GPS_GROUP_SAMPLING_POINT_GPS_LAT2", "GPS_GROUP_SAMPLING_POINT_GPS_LNG2",
                "GPS_GROUP_SAMPLING_POINT_GPS_LNG", "SAMPLING_POINT_GROUP_MANUAL_GPS" FROM "{tableCore}" WHERE "_URI" = \'{URI}\'', [
                "{tableCore}" => $tableCore,
                "{URI}" => $this->uri
            ]))->fetchAll();
            $this->fields[0]['NEW_SAMPLING_POINT'] = "yes";
            $this->nbField = 1;
            //$this->echoAll();
            $this->sortData();
        }
    }

    public function getAdditionalData($station) {
        $config = parse_ini_file("config/config.ini");
        try {
            if(empty($config['mongo_user']) && empty($config['mongo_password'])) {
                $this->mongo_db = new Manager("mongodb://" . $config['mongo_host'] . ':' . $config['mongo_port']);
            } else {
                $this->mongo_db= new Manager("mongodb://" . $config['mongo_user'] . ':' . $config['mongo_password'] . '@' . $config['mongo_host'] . ':' . $config['mongo_port']. '/' . $config['mongo_dbname']);
            }
            $filter = ['INTRO.STATION.ABBREVIATION' => $station];
            $options = [
                'projection' => [
                    '_id' => 0,
                    'INTRO.STATION' => 1
                ],
            ];
            $query = new Query($filter, $options);
            $cursor = $this->mongo_db->executeQuery('MOBISED.water', $query);
            foreach($cursor as $row) {
                if($row->INTRO->STATION[0]->ABBREVIATION == $station) {
                    return $row->INTRO->STATION[0];
                    break;
                }
            }
        } catch (Exception $e) {
            Logger::getInstance()->error("impossible de se connecter à la base MongoDB : " . $e->getMessage());
            exit();
        }
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
                $station = $this->fields[0]["SAMPLING_POINT"];
                $datas = $this->getAdditionalData($station);
                $this->sampling_point_fullname = $datas->NAME;
                $this->sampling_point_description = $datas->DESCRIPTION;
                $this->sampling_point_latitude = $datas->LATITUDE;
                $this->sampling_point_longitude = $datas->LONGITUDE;
                $this->sampling_point_altitude = $datas->ELEVATION;
                $this->sampling_point_abbreviation = $station;
            }
        }
    }
}