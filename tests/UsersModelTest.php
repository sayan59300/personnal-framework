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

    public function testAdd()
    {
        $model = new UsersModel(self::$pdo);
        $res = $this->insertUsers($model);
        $this->assertEquals(1, $res);
    }

    private function insertUsers(UsersModel $model, int $number = null)
    {
        if (!$number) {
            $model->nom = 'FakeNom' . uniqid();
            $model->prenom = 'FakePrenom' . uniqid();
            $model->email = 'fake@fake.fr';
            $model->username = 'FakeUsername' . uniqid();
            $model->password = encrypted(random_token(20));
            $model->confirmed = 0;
            $model->setConfirmationToken()->setRegisteredAt();
            return $model->save();
        }
        for ($i = 0; $i < $number; $i++) {
            $model->nom = 'FakeNom' . uniqid();
            $model->prenom = 'FakePrenom' . uniqid();
            $model->email = 'fake@fake.fr';
            $model->username = 'FakeUsername' . uniqid();
            $model->password = encrypted(random_token(20));
            $model->confirmed = 0;
            $model->setConfirmationToken()->setRegisteredAt();
            $model->save();
        }
    }

    public function testFindLastWithoutNumber()
    {
        $model = new UsersModel(self::$pdo);
        $res = $model->findLast();
        $this->assertCount(1, $res);
    }

    public function testFindLastWithNumber()
    {
        $model = new UsersModel(self::$pdo);
        $this->insertUsers($model, 20);
        $res = $model->findLast(10);
        $this->assertCount(10, $res);
    }

    public function testFindFirstWithoutNumber()
    {
        $model = new UsersModel(self::$pdo);
        $res = $model->findFirst();
        $this->assertCount(1, $res);
    }

    public function testFindFirstWithNumber()
    {
        $model = new UsersModel(self::$pdo);
        $this->insertUsers($model, 20);
        $res = $model->findFirst(10);
        $this->assertCount(10, $res);
    }

    public function testFindWithFalseConditions()
    {
        $model = new UsersModel(self::$pdo);
        $res = $model->find(['conditions' => 'id = 1000000']);
        $this->assertCount(0, $res);
    }

    public function testFindWithCorrectConditions()
    {
        $model = new UsersModel(self::$pdo);
        $model->nom = 'Washington';
        $model->prenom = 'Georges';
        $model->email = 'georges.washington@usa.fr';
        $model->username = 'gwashington';
        $model->password = encrypted('gwashington');
        $model->confirmed = 0;
        $model->setConfirmationToken()->setRegisteredAt();
        $model->save();
        $res = $model->find(['conditions' => "username = 'gwashington'"]);
        $this->assertEquals('gwashington', current($res)->username);
    }

    public function testUpdate()
    {
        $model = new UsersModel(self::$pdo);
        /** @var UsersModel $user */
        $user = current($model->find(['conditions' => "username = 'gwashington'"]));
        $user->prenom = 'John';
        $user->email = 'john.washington@usa.fr';
        $user->update();
        $this->assertEquals('John', $user->prenom);
        $this->assertEquals('john.washington@usa.fr', $user->email);
    }

    public function testDelete()
    {
        $model = new UsersModel(self::$pdo);
        /** @var UsersModel $user */
        $user = current($model->find(['conditions' => "username = 'gwashington'"]));
        $res = $model->delete(['conditions' => "id = $user->id"]);
        $this->assertEquals(1, $res);
    }
}
