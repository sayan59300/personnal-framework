# README #

### Contient les Models du framework (représentation des tables de la base de données) ###

Les variables des Models doivent correspondre en tous points aux clés des tables de la base de données et être publiques

### Format ###
```PHP
<?php

    namespace Itval\src\Models;
    
    use Itval\core\DAO\Tables;
    
    /**
     * Class NameModel
     *
     * @package Itval\src\Models
     * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
     */
    class NameModel extends Tables
    {
        
    }
```