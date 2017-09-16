<?php

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
