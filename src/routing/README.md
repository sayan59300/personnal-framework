# README #

### Contient les fichiers nécessaires au routing ###

### Ajouter une route ###
```PHP
<?php

    namespace Itval\src\routing;
    
    /* routes */
    return [
        "/chemin" => "controller@action",
    ];
```
### Ajouter un paramètre ###
```PHP
<?php

    namespace Itval\src\routing;
    
    /* parameters */
    return [
        "nom" => "regex"
    ];
```