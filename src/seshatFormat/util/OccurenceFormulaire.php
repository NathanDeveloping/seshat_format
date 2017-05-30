<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 30/05/17
 * Time: 10:42
 */

namespace seshatFormat\util;


/**
 * Class OccurenceFormulaire
 * Classe gestion des doublons
 * stock le nombre d'occurence d'un formulaire de nom donné
 *
 * singleton pour généraliser l'objet et le rendre unique
 *
 * @package seshatFormat\util
 */
class OccurenceFormulaire
{

    private static $instance;
    private $nbOccurences;

    private function __construct()
    {
        $this->nbOccurences = array();
    }

    public static function getInstance() {
        if(!isset(OccurenceFormulaire::$instance)) {
            OccurenceFormulaire::$instance = new OccurenceFormulaire();
        }
        return OccurenceFormulaire::$instance;
    }

    /**
     * Permet l'ajout
     * @param $nomFormulaire
     */
    public function addOccurrence($nomFormulaire) {
        $this->nbOccurences[$nomFormulaire]++;
    }

    /**
     * recupère le nombre d'occurence
     * d'un formulaire donné
     * @param $nomFormulaire
     * @return mixed
     */
    public function getOccurrences($nomFormulaire) {
        if(!isset($this->nbOccurences[$nomFormulaire])) {
            $this->nbOccurences[$nomFormulaire] = 0;
        }
        return $this->nbOccurences[$nomFormulaire];
    }

}