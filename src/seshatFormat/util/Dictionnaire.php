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
            "FILTERED WATER" => "FW",
            "SEDIMENT" => "SED_S",
            "FT" => "glass fiber filtered",
            "glass fiber filtered" => "FT",
            "LC" => "lab centrifuge",
            "lab centrifuge" => "LC",
            "FCOUT_FT" => "output waters from the field centrifuge, filtered with glass fiber filters",
            "output waters from the field centrifuge, filtered with glass fiber filters" => "FCOUT_FT",
            "fw22" => "FW22: filtered waters with syringe filter at 0.22 µm cutting edge",
            "fw45" => "FW45:  filtered waters with syringe filter at 0.45 µm cutting edge",
            "ufw22" => "UFW22: filtered waters with ultra-filtration at 0.22 µm cutting edge",
            "ufw5k" => "UFW5K: filtered waters with ultra-filtration at 5000 Da cutting edge",
            "fcout_fw22" => "FCOUT_FW22: field centrifuge output water filtered with syringe filter at 0.22 µm cutting edge"
        );
    }

    public function getTraduction($mot) {
        return $this->table[$mot];
    }
}