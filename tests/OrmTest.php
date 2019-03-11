<?php

use Itval\core\DAO\Exception\QueryException;
use Itval\src\Models\TestsModel;
use PHPUnit\Framework\TestCase;

require_once 'components.php';

class OrmTest extends TestCase
{

    /**
     * @var TestsModel
     */
    private $model;

    /**
     * @var PDO
     */
    private $pdo;

    public function setUp()
    {
        $this->pdo = new PDO(
            'sqlite::memory:',
            null,
            null
        );
        $this->model = new TestsModel($this->pdo);
    }

    /**
     * SELECT
     * @throws QueryException
     */
    public function testQueryWithWrongType()
    {
        $this->expectException(QueryException::class);
        $this->model->getQuery('mauvais');
    }

    /**
     * @throws QueryException
     */
    public function testSelectQueryWithoutFields()
    {
        $requete = $this->model->getQuery('select');
        $this->assertEquals('SELECT * FROM tests', $requete);
    }

    /**
     * @throws QueryException
     */
    public function testSelectQueryWithJoin()
    {
        $requete = $this->model->getQuery(
            'select',
            [
                'join' => ['INNER JOIN users']
            ]
        );
        $requete2 = $this->model->getQuery(
            'select',
            [
                'join' => ['INNER JOIN users', 'LEFT JOIN coms']
            ]
        );
        $this->assertEquals('SELECT * FROM tests INNER JOIN users', $requete);
        $this->assertEquals('SELECT * FROM tests INNER JOIN users,LEFT JOIN coms', $requete2);
    }

    /**
     * @throws QueryException
     */
    public function testSelectQueryWithFields()
    {
        $requete = $this->model->getQuery('select', ['fields' => ['id', 'nom', 'description', 'contenu']]);
        $this->assertEquals('SELECT id,nom,description,contenu FROM tests', $requete);
    }

    /**
     * @throws QueryException
     */
    public function testSelectQueryWithFieldsConditions()
    {
        $requete = $this->model->getQuery(
            'select',
            [
                'fields' => ['id', 'nom', 'description', 'contenu'],
                'conditions' => 'id = :id'
            ]
        );
        $this->assertEquals('SELECT id,nom,description,contenu FROM tests WHERE id = :id', $requete);
    }

    /**
     * @throws QueryException
     */
    public function testSelectQueryWithFieldsConditionsGroupBy()
    {
        $requete = $this->model->getQuery(
            'select',
            [
                'fields' => ['id', 'nom', 'description', 'contenu'],
                'conditions' => 'id = :id',
                'group' => 'nom'
            ]
        );
        $this->assertEquals('SELECT id,nom,description,contenu FROM tests WHERE id = :id GROUP BY nom', $requete);
    }

    /**
     * @throws QueryException
     */
    public function testSelectQueryWithFieldsConditionsGroupByOrderBy()
    {
        $requete = $this->model->getQuery(
            'select',
            [
                'fields' => ['id', 'nom', 'description', 'contenu'],
                'conditions' => 'id = :id',
                'group' => 'nom',
                'order' => 'nom DESC'
            ]
        );
        $this->assertEquals('SELECT id,nom,description,contenu FROM tests WHERE id = :id GROUP BY nom ORDER BY nom DESC', $requete);
    }

    /**
     * @throws QueryException
     */
    public function testSelectQueryWithFieldsConditionsGroupByOrderByLimit()
    {
        $requete = $this->model->getQuery(
            'select',
            [
                'fields' => ['id', 'nom', 'description', 'contenu'],
                'conditions' => 'id = :id',
                'group' => 'nom',
                'order' => 'nom DESC',
                'limit' => 3
            ]
        );
        $requete2 = $this->model->getQuery(
            'select',
            [
                'fields' => ['id', 'nom', 'description', 'contenu'],
                'conditions' => 'id = :id',
                'group' => 'nom',
                'order' => 'nom DESC',
                'limit' => '0,3'
            ]
        );
        $this->assertEquals('SELECT id,nom,description,contenu FROM tests WHERE id = :id GROUP BY nom ORDER BY nom DESC LIMIT 3', $requete);
        $this->assertEquals('SELECT id,nom,description,contenu FROM tests WHERE id = :id GROUP BY nom ORDER BY nom DESC LIMIT 0,3', $requete2);
    }

    /**
     * @throws QueryException
     */
    public function testSelectQueryWithFieldsConditionsGroupByOrderByLimitJoin()
    {
        $requete = $this->model->getQuery(
            'select',
            [
                'fields' => ['tests.id', 'tests.nom', 'tests.description', 'tests.contenu'],
                'conditions' => 'tests.id = :id',
                'group' => 'tests.nom',
                'order' => 'tests.nom DESC',
                'limit' => 3,
                'join' => ['INNER JOIN users']
            ]
        );
        $requete2 = $this->model->getQuery(
            'select',
            [
                'fields' => ['tests.id', 'tests.nom', 'tests.description', 'tests.contenu'],
                'conditions' => 'tests.id = :id AND users.id = :user_id AND coms.nom = :nom',
                'group' => 'users.nom',
                'order' => 'users.nom DESC',
                'limit' => 3,
                'join' => ['INNER JOIN users', 'LEFT JOIN coms']
            ]
        );
        $this->assertEquals('SELECT tests.id,tests.nom,tests.description,tests.contenu FROM tests INNER JOIN users WHERE tests.id = :id GROUP BY tests.nom ORDER BY tests.nom DESC LIMIT 3', $requete);
        $this->assertEquals('SELECT tests.id,tests.nom,tests.description,tests.contenu FROM tests INNER JOIN users,LEFT JOIN coms WHERE tests.id = :id AND users.id = :user_id AND coms.nom = :nom GROUP BY users.nom ORDER BY users.nom DESC LIMIT 3', $requete2);
    }

    /**
     * @throws QueryException
     */
    public function testSelectQueryWithFieldsGroupBy()
    {
        $requete = $this->model->getQuery(
            'select',
            [
                'fields' => ['id', 'nom', 'description', 'contenu'],
                'group' => 'nom',
            ]
        );
        $this->assertEquals('SELECT id,nom,description,contenu FROM tests GROUP BY nom', $requete);
    }

    /**
     * @throws QueryException
     */
    public function testSelectQueryWithFieldsOrderBy()
    {
        $requete = $this->model->getQuery(
            'select',
            [
                'fields' => ['id', 'nom', 'description', 'contenu'],
                'order' => 'nom DESC'
            ]
        );
        $this->assertEquals('SELECT id,nom,description,contenu FROM tests ORDER BY nom DESC', $requete);
    }

    /**
     * @throws QueryException
     */
    public function testSelectQueryWithFieldsLimit()
    {
        $requete = $this->model->getQuery(
            'select',
            [
                'fields' => ['id', 'nom', 'description', 'contenu'],
                'limit' => 3
            ]
        );
        $this->assertEquals('SELECT id,nom,description,contenu FROM tests LIMIT 3', $requete);
    }

    /**
     * LAST
     * @throws QueryException
     */
    public function testLastQuery()
    {
        $requete = $this->model->getQuery('last', [], 1);
        $this->assertEquals('SELECT * FROM tests ORDER BY id DESC LIMIT 1', $requete);
    }

    /**
     * @throws QueryException
     */
    public function testLastQueryWithLimit()
    {
        $requete = $this->model->getQuery('last', [], 3);
        $this->assertEquals('SELECT * FROM tests ORDER BY id DESC LIMIT 3', $requete);
    }

    /**
     * FIRST
     */
    public function tesFirstQuery()
    {
        try {
            $requete = $this->model->getQuery('first', [], 1);
        } catch (QueryException $e) {
        }
        $this->assertEquals('SELECT * FROM tests ORDER BY id ASC LIMIT 1', $requete);
    }

    /**
     * @throws QueryException
     */
    public function tesFirstQueryWithLimit()
    {
        $requete = $this->model->getQuery('first', [], 3);
        $this->assertEquals('SELECT * FROM tests ORDER BY id ASC LIMIT 3', $requete);
    }
}
