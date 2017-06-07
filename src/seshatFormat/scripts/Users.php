<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 30/05/17
 * Time: 12:11
 */

namespace seshatFormat\scripts;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use seshatFormat\util\Logger;
use Exception;
use MongoDB\Driver\Command;
use MongoDB\BSON\Regex;

class Users
{

    private $mongo_db;

    public function getUserAdditionalData($firstname, $lastname) {
        $config = parse_ini_file("config/config.ini");
        try {
            if(empty($config['mongo_user']) && empty($config['mongo_password'])) {
                $this->mongo_db = new Manager("mongodb://" . $config['mongo_host'] . ':' . $config['mongo_port']);
            } else {
                $this->mongo_db= new Manager("mongodb://" . $config['mongo_user'] . ':' . $config['mongo_password'] . '@' . $config['mongo_host'] . ':' . $config['mongo_port']. '/' . $config['mongo_dbname']);
            }
            $regexFirst = new Regex($firstname, 'i');
            $regexLast = new Regex($lastname, 'i');
            $filter = ['INTRO.FILE CREATOR.FIRST NAME' => $regexFirst,
                'INTRO.FILE CREATOR.NAME' => $regexLast
               ];
            $cmd = new Command([
                'distinct' => 'water',
                'key' => 'INTRO.FILE CREATOR',
                'query' => $filter
            ]);
            $cursor = $this->mongo_db->executeCommand('MOBISED', $cmd);
            $scents = current($cursor->toArray())->values;
            $i = 0;
            while(isset($scents[$i])) {
                if($this->emailContain($scents[$i]->MAIL, $lastname)) {
                    break;
                } else {
                    $i++;
                }
            }
            if(isset($scents[$i])) {
                return $scents[$i]->MAIL;
            } else {
                return "";
            }
        } catch (Exception $e) {
            Logger::getInstance()->error("impossible de se connecter à la base MongoDB : " . $e->getMessage());
            exit();
        }
    }

    public function to_upper($name)
    {
        $name=ucwords($name);
        $arr=explode('-', $name);
        $name=array();
        foreach($arr as $v)
        {
            $name[]=ucfirst($v);
        }
        $name=implode('-', $name);
        return $name;
    }

    public function emailContain($email, $name) {
        if(stripos($email, $name)) return true;
        if(stripos($name, '-')) {
            $composedName = explode('-', $name);
            if(stripos($email, $composedName[0])) return true;
            if(stripos($email, $composedName[1])) return true;
        }
        return false;
    }

}