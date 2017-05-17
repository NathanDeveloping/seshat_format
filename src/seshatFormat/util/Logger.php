<?php
namespace  seshatFormat\util;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{

    private static $instance;

    private $config;

    private function __construct(){
        $this->config = parse_ini_file("config/config.ini");
        if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $this->config['logsDir'])) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . $this->config['logsDir']);
        }
    }

    public static function getInstance() {
        if(!isset($instance)) {
            $instance = new Logger();
        }
        return $instance;
    }

    public function log($level, $message, array $context = [])
    {
        echo strtr($message, $context);

        $stream = fopen($_SERVER['DOCUMENT_ROOT'] . $this->config['logsDir'] . date("m-d-y")  , 'a+');
        fwrite($stream, strtr($message, $context));
        fclose($stream);
    }

}