<?php

namespace Itval\src\config;

define(
    'ALPHABETIC',
    '/^([a-zA-Zéèçàùêâôîûäëïöü0-9@]+(([ \-\'\.])[a-zA-Zéèçàùêâôîûäëïöü0-9@]+)*)+(([ \-\'\.])([a-zA-Zéèçàùêâôîûäëïöü0-9@]+(([ \-\'\.])[a-zA-Zéèçàùêâôîûäëïöü0-9@]+)*)+)*$/'
);
define(
    'TEXT',
    '/^([a-zA-Z0-9éèçàùêâôîûäëïöü]+[\s]{0,1}|[\\\']{0,1}|[\-]{0,1}|[\,\.]{0,1}[\s]{1}|[\s]{1}[\:\;\!\?][\s]{1})+([\.]{1}|[\s][\?]{1}|[\s][\!]{1}|[\.]{3})$/'
);
define('ALPHANUMERIC', '/^[a-zA-Z0-9]+$/');
define('NUMERIC', '/^[0-9]+$/');
define('DENOMINATION', '/^([a-zA-Zéèçàùêâôîûäëïöü]+(([ \-\'])[a-zA-Zéèçàùêâôîûäëïöü]+)*)+(([ \-\'])([a-zA-Zéèçàùêâôîûäëïöü]+(([ \-\'])[a-zA-Zéèçàùêâôîûäëïöü]+)*)+)*$/');
define('EMAIL', '/^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$/');
define('PHONE_NUMBER', '/^(01|02|03|04|05|06|07|08|09)[0-9]{8}$/');
define('USERNAME', '/^\w+$/');
define('PASSWORD', '/^([a-zA-Z0-9]+(([\-\_])[a-zA-Z0-9]+)*)+(([\-\_])([a-zA-Z0-9]+(([\-\_])[a-zA-Z0-9]+)*)+)*$/');
define('SLUG', "/^[a-z]+(?:-[a-z0-9]+)*$/");
