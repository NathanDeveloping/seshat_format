<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 15/05/17
 * Time: 10:06
 */

namespace seshatFormat\scripts;
use seshatFormat\util\Logger;
use Exception;
use PDO;

class DatabaseConnexion
{

    private static $instance;
    private $db;

    private function __construct()
    {
        $config = parse_ini_file("config/config.ini");
        if(!isset($config['host']) || !isset($config['port']) ||
            !isset($config['dbname']) || !isset($config['user']) || !isset($config['password'])) {
            Logger::getInstance()->alert("Fichier de configuration \"config/config.ini\" incorrect ou inexistant.");
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
                Logger::getInstance()->alert($e->getMessage());
            }
        }
    }

    public static function getInstance() {
        if(!isset($instance)) {
            $instance = new DatabaseConnexion();
        }
        return $instance;
    }

    public function getDB() {
        return $this->db;
    }

}