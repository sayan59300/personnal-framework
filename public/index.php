<?php

use Itval\Controllers\AuthController;
use Itval\Controllers\BaseController;
use Slim\App;
use Slim\Container;

if (version_compare(PHP_VERSION, "7.1") === -1) {
    die("PHP 7.1 is required, your version is " . PHP_VERSION);
}

session_start();
define('BASE_URL', 'http://' . filter_input(INPUT_SERVER, 'HTTP_HOST'));
define('PUBLIC_ROOT', dirname(__FILE__));
define('ROOT', dirname(PUBLIC_ROOT));
define('DS', DIRECTORY_SEPARATOR);

/* autoloader de l'application */
require_once ROOT . DS . 'core' . DS . 'autoloader.php';

/* autoloader composer */
require_once ROOT . DS . 'vendor' . DS . 'autoload.php';

/* configuration */
autoLoader(ROOT . DS . 'src' . DS . 'config' . DS);

/* helpers */
autoLoader(ROOT . DS . 'core' . DS . 'helpers' . DS);

/* chargement des listeners */
autoLoader(ROOT . DS . 'src' . DS . 'events' . DS);

/* container */
$container = new Container();
/* surcharge de la vue 404 */
$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        ob_start();
        include ROOT . DS . 'views' . DS . 'errors' . DS . '404.phtml';
        $body = ob_get_clean();
        return $container['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write($body);
    };
};
if (VERSION === 'dev') {
    $container['settings']['displayErrorDetails'] = true;
}

/* initialisation de l'application */
$app = new App($container);

/**
 * BaseController
 */
/* index */
$app->get('/', BaseController::class . ':index');
/* contact */
$app->get('/contact', BaseController::class . ':contact');
$app->post('/contact', BaseController::class . ':sendMessage');
/* mentions légales */
$app->get('/mentions', BaseController::class . ':mentions');

if (AUTH) {
    /**
     * Authentification
     */
    /* connexion */
    $app->get('/auth', AuthController::class . ':index');
    $app->post('/auth', AuthController::class . ':connexion');
    /* enregistrement */
    $app->get('/registration', AuthController::class . ':register');
    $app->post('/registration', AuthController::class . ':registration');
    $app->get('/confirmation', AuthController::class . ':confirmation');
    /* profil */
    $app->get('/profil', AuthController::class . ':profil');
    $app->post('/profil', AuthController::class . ':updateProfil');
    /* mise à jour du mot de passe */
    $app->get('/update_password', AuthController::class . ':updatePassword');
    $app->post('/update_password', AuthController::class . ':passwordUpdate');
    /* deconnexion */
    $app->get('/logout', AuthController::class . ':deconnexion');
    $app->post('/logout', AuthController::class . ':logout');
}

/* lancement de l'application */
$app->run();
