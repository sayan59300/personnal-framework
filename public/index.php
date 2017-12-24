<?php

use Itval\Controllers\AuthController;
use Itval\Controllers\BaseController;
use Slim\App;
use Slim\Container;
use Slim\Http\Response;
use Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware;

if (version_compare(PHP_VERSION, "7.1") === -1) {
    die("PHP 7.1 is required, your version is " . PHP_VERSION);
}

session_start();
define('SSL', false);
if (SSL) {
    define('BASE_URL', 'https://' . filter_input(INPUT_SERVER, 'HTTP_HOST'));
} else {
    define('BASE_URL', 'http://' . filter_input(INPUT_SERVER, 'HTTP_HOST'));
}
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
    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    return function ($request, $response) use ($container) {
        ob_start();
        include ROOT . DS . 'views' . DS . 'errors' . DS . '404.phtml';
        $body = ob_get_clean();
        /** @var Response $response */
        $response = $container['response'];
        return $response
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write($body);
    };
};
if (VERSION === 'dev') {
    $settings = $container['settings'];
    // Enable whoops
    $settings['debug'] = true;
    // Support click to open editor
    $settings['whoops.editor'] = 'sublime';
    // Display call stack in orignal slim error when debug is off
    $settings['displayErrorDetails'] = true;
}

/* initialisation de l'application */
$app = new App($container);

/* middleware pour whoops */
$app->add(new WhoopsMiddleware);

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
    $app->get('/update-password', AuthController::class . ':updatePassword');
    $app->post('/update-password', AuthController::class . ':passwordUpdate');
    $app->get('/reset-password', AuthController::class . ':vueResetPassword');
    $app->post('/reset-password', AuthController::class . ':sendResetPasswordEmail');
    $app->get('/reset-password-confirmation', AuthController::class. ':resetPasswordConfirmation');
    $app->post('/reset-password-confirmation', AuthController::class. ':resetPasswordConfirmation');
    /* deconnexion */
    $app->get('/logout', AuthController::class . ':deconnexion');
    $app->post('/logout', AuthController::class . ':logout');
}

/* lancement de l'application */
$app->run();
