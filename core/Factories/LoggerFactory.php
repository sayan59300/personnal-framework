<?php

namespace Itval\core\Factories;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

/**
 * Class LoggerFactory
 *
 * @package Itval\core\Factories
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class LoggerFactory
{

    private function __construct()
    {
    }

    /**
     * Retourne l'instance du Monolog\Logger avec sa configuration
     *
     * @param  string $file
     * @return Logger
     */
    public static function getInstance($file = 'app'): Logger
    {
        $logger = new Logger($file . '_logs');
        $logger->pushHandler(new StreamHandler(ROOT . "/logs/$file.log", Logger::DEBUG));
        $logger->pushHandler(new FirePHPHandler());

        return $logger;
    }
}
