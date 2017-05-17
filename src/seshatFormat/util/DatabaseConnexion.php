<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 15/05/17
 * Time: 10:06
 */

namespace seshatFormat\util;
use seshatFormat\util\Logger;
use Exception;
use PDO;

/**
 * Class DatabaseConnexion
 * Singleton de connexion à la base
 * de données
 * @package seshatFormat\scripts
 */
class DatabaseConnexion
{

    private static $instance;
    private $db;

    /**
     * DatabaseConnexion constructor.
     */
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

    /**
     * Singleton getter
     * @return DatabaseConnexion
     */
    public static function getInstance() {
        if(!isset($instance)) {
            $instance = new DatabaseConnexion();
        }
        return $instance;
    }

    /**
     * Database connexion getter
     * @return PDO
     */
    public function getDB() {
        return $this->db;
    }

}