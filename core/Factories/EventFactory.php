<?php

namespace Itval\core\Factories;

use Evenement\EventEmitter;

/**
 * Class EventFactory
 *
 * @package Itval\core\Factories
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class EventFactory
{

    private static $_instance;

    private function __construct()
    {
    }

    /**
     * Retourne l'instance de EventEmitter
     *
     * @return EventEmitter
     */
    public static function getInstance(): EventEmitter
    {
        if (!self::$_instance) {
            self::$_instance = new EventEmitter();
        }
        return self::$_instance;
    }
}
