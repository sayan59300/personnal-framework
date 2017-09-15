<?php

namespace Itval\core\Classes;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Route Parseur d'url
 *
 * @package Itval\core\Classes
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class Route
{

    /**
     * Parseur d'url
     *
     * @param ServerRequestInterface $request
     */
    public static function parse(ServerRequestInterface $request)
    {
        $uri = $request->getUri()->getPath();
        if ($uri !== "/") {
            $urlParts = explode('?', $uri);
            $url = rtrim($urlParts[0], '/');
        } else {
            $url = "/";
        }
        if (isset($urlParts)) {
            $parameter = explode("/", $urlParts[0]);
        }
        if (isset($parameter[2])) {
            foreach (PARAMETERS as $key => $value) {
                if (preg_match($value, $parameter[2])) {
                    $url = "/$parameter[1]/:$key";
                }
            }
        }
        if (array_key_exists($url, ROUTES)) {
            $route = explode('@', ROUTES[$url]);
            $request->controller = $route[0];
            $request->action = $route[1];
            $trimUrl = trim($uri, '/');
            $params = explode('/', $trimUrl);
            if (isset($params[1]) && !strstr($params[1], '?p=')) {
                $request->params = $params[1] ?? null;
                $request->additionalParams = array_slice($params, 2);
            } else {
                $request->params = null;
                $request->additionalParams = [];
            }
        }
    }
}
