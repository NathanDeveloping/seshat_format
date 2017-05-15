<?php
require '../vendor/autoload.php';
use seshatFormat\scripts\DatabaseFormatter;
use seshatFormat\scripts\FormFormatter;

$df = new FormFormatter("WATER_FC_RW");
$df->formatAll();