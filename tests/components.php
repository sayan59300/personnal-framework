<?php

define('BASE_URL', 'http://' . filter_input(INPUT_SERVER, 'HTTP_HOST'));
define('PUBLIC_ROOT', dirname(__FILE__));
define('ROOT', dirname(PUBLIC_ROOT));
define('DS', DIRECTORY_SEPARATOR);

/* configuration */
require_once ROOT . DS . 'src' . DS . 'config' . DS . 'config.php';
define('ROUTES', require_once ROOT . DS . 'src' . DS . 'routing' . DS . 'routing.php');
define('PARAMETERS', require_once ROOT . DS . 'src' . DS . 'routing' . DS . 'parameters.php');

/* helpers */
require_once ROOT . DS . 'core' . DS . 'autoloader.php';
autoLoader(ROOT . DS . 'core' . DS . 'helpers' . DS);

/* chargement des listeners */
require_once ROOT . DS . 'src' . DS . 'events' . DS . 'listeners.php';

/* classes autoload */
require_once ROOT . DS . 'vendor' . DS . 'autoload.php';
