<?php
require '../vendor/autoload.php';
use seshatFormat\scripts\DatabaseFormatter;

$df = new DatabaseFormatter();
$df->createSkeleton();