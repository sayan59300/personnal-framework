<?php

namespace Itval\core\Classes;

use GuzzleHttp\Psr7\ServerRequest;
use function Http\Response\send;

/**
 * Class App Classe principale
 *
 * @package Itval\core\Classes
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class App
{
    /**
     * Lance l'application
     *
     */
    public function run(): void
    {
        $router = new Router(ServerRequest::fromGlobals());
        $response = $router->getResponse();
        send($response);
    }
}
