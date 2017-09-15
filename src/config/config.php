<?php

namespace Itval\src\config;

/* BDD */
define('DB_HOST', '');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASS', '');

/* site */
define('WEBMASTER_MAIL', '');
define('SITE_CONTACT_MAIL', );
define('SITE_NAME', '');
define('SLOGAN', '');
define('TITLE', '');
define('VERSION', 'dev');

/* SwiftMailer */
// serveur smtp
define('HOST_MAIL', '');
define('HOST_MAIL_PORT', 25);
define('HOST_MAIL_USERNAME', '');
define('HOST_MAIL_PASSWORD', '');
define('HOST_MAIL_SECURITY', '');

/* site bundles activation */
define('AUTH', true); /* active ou désactive le système d'authentification du site */

/* routes */
define('ASSETS', BASE_URL . DS . 'public' . DS . 'resources');
define('ASSETS_CSS', ASSETS . DS . 'css');
define('ASSETS_JS', ASSETS . DS . 'js');
define('IMG', ASSETS . DS . 'images');

/* regex */
define(
    'ALPHABETIC',
    '/^([a-zA-Zéèçàùêâôîûäëïöü]+(([ \-\'])[a-zA-Zéèçàùêâôîûäëïöü]+)*)+(([ \-\'])([a-zA-Zéèçàùêâôîûäëïöü]+(([ \-\'])
    [a-zA-Zéèçàùêâôîûäëïöü]+)*)+)*$/'
);
define('ALPHANUMERIC', '/^[a-zA-Z0-9]+$/');
define('NUMERIC', '/^[0-9]+$/');
define('EMAIL', '/^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$/');
define('PHONE_NUMBER', '/^(01|02|03|04|05|06|07|08|09)[0-9]{8}$/');
define('USERNAME', '/^\w+$/');
define('PASSWORD', '/^([a-zA-Z0-9]+(([\-\_])[a-zA-Z0-9]+)*)+(([\-\_])([a-zA-Z0-9]+(([\-\_])[a-zA-Z0-9]+)*)+)*$/');
