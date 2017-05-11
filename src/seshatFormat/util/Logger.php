<?php
namespace  seshatFormat\util;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{


    public function log($level, $message, array $context = [])
    {
        echo strtr($message, $context);
        $config = parse_ini_file("config/config.ini");
        $stream = fopen($config['logsDir'] . date("m-d-y")  , 'a');
        fwrite($this->stream, strtr($message, $context));
        fclose($stream);
    }

}