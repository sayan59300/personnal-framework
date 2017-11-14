<?php

use Itval\core\Classes\App;

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

/* routing */
define('ROUTES', require_once ROOT . DS . 'src' . DS . 'routing' . DS . 'routing.php');
define('PARAMETERS', require_once ROOT . DS . 'src' . DS . 'routing' . DS . 'parameters.php');

/* helpers */
autoLoader(ROOT . DS . 'core' . DS . 'helpers' . DS);

/* chargement des listeners */
autoLoader(ROOT . DS . 'src' . DS . 'events' . DS);

/* affichage des erreurs php en mode developpement */
if (VERSION === 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}

$app = new App();
$app->run();
