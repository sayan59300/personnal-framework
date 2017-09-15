<?php

namespace Itval\core\Classes;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Router Classe qui s'occupe de faire le routing de l'application
 *
 * @package Itval\core
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class Router
{

    /**
     * Instance de la classe Request
     *
     * @var ServerRequestInterface
     */
    private $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
        Route::parse($this->request);
    }

    /**
     * Retourne la réponse après traitement par le controlleur
     *
     * @return Response
     */
    public function getResponse(): Response
    {
        $controller = $this->loadController();
        $action = $this->request->action ?? '';
        if (!$controller) {
            return error404();
        } else {
            if (method_exists($controller, $action)) {
                return call_user_func_array([$controller, $action], [$this->request->params] ?? []);
            } else {
                return error404();
            }
        }
    }

    /**
     * Retourne une instance du controller demandé ou false si celui ci n'éxiste pas
     *
     * @return boolean ou une instance du controller demandé
     */
    private function loadController()
    {
        $controller = $this->request->controller ?? 'none';
        if (file_exists(ROOT . DS . 'controllers' . DS . ucfirst($controller) . 'Controller' . '.php')) {
            $controllerName = ucfirst($controller) . 'Controller';
            include ROOT . DS . 'controllers' . DS . $controllerName . '.php';
            $controller = new $controllerName($this->request);
            return $controller;
        } else {
            return false;
        }
    }
}
