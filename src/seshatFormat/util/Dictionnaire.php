<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 15/05/17
 * Time: 16:48
 */

namespace seshatFormat\util;


class Dictionnaire
{

    private static $instance;

    private $table;

    private function __construct() {
        $this->init();
    }

    public static function getInstance() {
        if(!isset($instance)) {
            $instance = new Dictionnaire();
        }
        return $instance;
    }

    public function init() {
        $this->table = array(
          "RW" => "RAW WATER",
            "RAW WATER" => "W",
            "manual_sampling" => "manual sampling",
            "FC" => "field centrifuge",
            "field centrifuge" => "FC",
            "FW" => "FILTERED WATER",
            "FILTERED WATER" => "FW"
        );
    }

    public function getTraduction($mot) {
        return $this->table[$mot];
    }
}