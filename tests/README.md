# README #

### Contient les tests ###

### Format ###
```PHP
<?php

    use PHPUnit\Framework\TestCase;
    
    require_once 'components.php';
    
    class NameTest extends TestCase
    {
        
    }
```
### Exemple pour le modèle UsersModel ###
Ces 2 méthodes doivent être présentes pour chaque modèle à tester, le table sera créée pour les tests puis supprimée à la fin de ceux ci.
```PHP
<?php

    use Itval\src\Models\UsersModel;
    use PHPUnit\Framework\TestCase;

    require_once 'components.php';

    class UsersModelTest extends TestCase
    {
        
        /**
         * @var PDO
         */
        private static $pdo;
        
        public static function tearDownAfterClass()
        {
            self::$pdo->exec('TRUNCATE TABLE users');
            self::$pdo->exec('DROP TABLE users');
        }
    
        public function setUp()
        {
            self::$pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=testDatabase;charset=utf8',
                DB_USER,
                DB_PASS,
                [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']
            );
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->exec(
                "CREATE TABLE IF NOT EXISTS users 
                          (
                            id  INT(11) NOT NULL AUTO_INCREMENT,
                            nom  VARCHAR(255) NOT NULL,
                            prenom  VARCHAR(255) NOT NULL,
                            email  VARCHAR(255) NOT NULL,
                            username  VARCHAR(255) NOT NULL,
                            password  VARCHAR(255) NOT NULL,
                            confirmation_token  VARCHAR(255) NOT NULL,
                            confirmed  INT(11) NOT NULL,
                            registered_at  DATE NOT NULL,
                            PRIMARY KEY ( id ),
                            UNIQUE KEY  username  ( username )
                          )  
                          ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;"
            );
        }
    }
```