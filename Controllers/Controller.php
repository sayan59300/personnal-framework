<?php

namespace Itval\Controllers;

use Itval\core\Classes\Session;
use Itval\core\Factories\EventFactory;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile;

/**
 * Class Controller Controlleur principal
 *
 * @author Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class Controller
{

    /**
     * Instance de EventEmitter
     *
     * @var \Evenement\EventEmitter
     */
    public $emitter;

    /**
     * Valeurs de $_POST
     *
     * @var array|null|object
     */
    private $_post;

    /**
     * Valeurs de $_GET
     *
     * @var array
     */
    private $_query;

    /**
     * Valeurs de $_COOKIE
     *
     * @var array
     */
    private $_cookie;

    /**
     * Valeurs de $_FILE
     *
     * @var UploadedFile[]
     */
    private $_file;

    /**
     * Request
     *
     * @var Request
     */
    private $request;

    /**
     * Variables passées à l'instance
     *
     * @var array
     */
    private $vars = [];

    /**
     * Container de dépendances
     *
     * @var Container
     */
    protected $container;

    /**
     * Controller constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        extract($this->vars);
        date_default_timezone_set("Europe/Berlin");
        $this->container = $container;
        $this->emitter = EventFactory::getInstance();
        $this->_post = $container->request->getParsedBody();
        $this->_query = $container->request->getQueryParams();
        $this->_cookie = $container->request->getCookieParams();
        $this->_file = $container->request->getUploadedFiles();
    }

    /**
     * Retourne le tableau $_POST
     *
     * @return array|null|object
     */
    public function getPost()
    {
        return $this->_post;
    }

    /**
     * Retourne le tableau $_GET
     *
     * @return array
     */
    public function getQuery(): array
    {
        return $this->_query;
    }

    /**
     * Retourne le tableau $_COOKIE
     *
     * @return array
     */
    public function getCookie(): array
    {
        return $this->_cookie;
    }

    /**
     * Retourne le tableau $_FILE
     *
     * @return UploadedFile[]
     */
    public function getFile(): array
    {
        return $this->_file;
    }

    /**
     * Retourne la vue demandée
     *
     * @param  string $viewName
     * @return Response
     */
    protected function render(string $viewName): Response
    {
        $response = new Response();
        extract($this->vars);
        $viewDir = $this->getViewDir();
        $view = ROOT . DS . 'views' . DS . $viewDir . DS . $viewName . '.phtml';
        if (file_exists(ROOT . DS . 'views' . DS . $viewDir . DS . $viewName . '.phtml')) {
            ob_start();
            include $view;
            $contentForBody = ob_get_clean();
            ob_start();
            include ROOT . DS . 'views' . DS . 'templates' . DS . 'base.phtml';
            $body = ob_get_clean();
            $response->withStatus(200);
            $response->getBody()->write($body);
            $this->resetValidationSession();
            return $response;
        }
        return error404($response);
    }

    /**
     * Initialise un token aléatoire et indique son heure de création dans 2 variables de session
     * Attribut la valeur du token dans une variable de même nom qui est utilisée dans la vue
     * Retourne le token au besoin pour une autre utilisation
     *
     * @return string
     */
    protected function setToken(): string
    {
        $token = generateToken();
        $this->set('token', $token);
        return $token;
    }

    /**
     * Ajoute une entrée dans le tableau $vars
     *
     * @param string $key
     * @param mixed $value
     */
    protected function set(string $key, $value = null): void
    {
        if (is_array($key)) {
            $this->vars += $key;
        } else {
            $this->vars[$key] = $value;
        }
    }

    /**
     * Retourne la liste des erreurs dans une chaine de caractères
     *
     * @param  array $values
     * @return string
     */
    protected function formattedErrors(array $values): string
    {
        $erreurs = '';
        foreach ($values as $value) {
            $erreurs .= $value . '<br/>';
        }
        return $erreurs;
    }

    /**
     * Génère les variables de session pour le formulaire d'inscription
     *
     * @param array $values
     */
    protected function setValuesSession(array $values)
    {
        foreach ($values as $key => $value) {
            Session::set($key, $value);
        }
    }

    /**
     * Unset les variables de session du formulaire d'inscription
     *
     * @param array $args
     */
    protected function resetValuesSession(array $args)
    {
        $keys = array_keys($args);
        foreach ($keys as $key) {
            Session::delete($key);
        }
    }

    /**
     * Efface les messages de validation stockés en session
     */
    private function resetValidationSession()
    {
        foreach ($_SESSION as $key => $value) {
            if (strstr($key, 'validator_error_')) {
                Session::delete($key);
            }
        }
    }

    /**
     * Retourne le nom du dossier des vues en fonction du controlleur appelé
     *
     * @return string
     */
    private function getViewDir()
    {
        $controller = get_called_class();
        $explodeArray = explode('\\', $controller);
        $controllerName = end($explodeArray);
        $dirnameExplode = explode('Controller', $controllerName);
        return $dir = strtolower($dirnameExplode[0]);
    }
}
