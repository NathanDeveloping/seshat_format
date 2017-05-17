<?php
namespace  seshatFormat\util;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{

    private static $instance;

    private function __construct(){}

    public static function getInstance() {
        if(!isset($instance)) {
            $instance = new Logger();
        }
        return $instance;
    }

    public function log($level, $message, array $context = [])
    {
        echo strtr($message, $context);
        $config = parse_ini_file("config/config.ini");
        $stream = fopen($_SERVER['DOCUMENT_ROOT'] . $config['logsDir'] . date("m-d-y")  , 'a+');
        fwrite($this->stream, strtr($message, $context));
        fclose($stream);
    }

}