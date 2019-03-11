<?php

namespace Itval\core\DAO;

use Itval\core\DAO\Exception\PaginatedException;
use Itval\core\DAO\Exception\QueryException;
use Itval\src\Services\PaginationAdapter;
use Pagerfanta\Pagerfanta;
use Slim\Http\Request;

/**
 * Class Tables couche d'abstraction de la base de données
 *
 * @package Itval\core\DAO
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class Tables implements \ArrayAccess
{

    /**
     * Tableau contenant le résultat d'une requête sous forme de tableau de models
     *
     * @var array
     */
    private $models = [];

    /**
     * Connexion à la base de données
     *
     * @var \PDO
     */
    private $pdo;

    public function __construct(\PDO $pdo = null)
    {
        if ($pdo) {
            $this->pdo = $pdo;
        } else {
            $this->pdo = Database::getPdo();
        }
    }

    /**
     * Fonction insert dans la base de données
     *
     * @param  array $args fields = string contenant les noms des colonnes séparés par des virgules / values = string
     * contenant les valeurs a donner aux colonnes séparées par des virgules
     * @param array $values
     * @return int
     */
    public function add(array $args, array $values = []): int
    {
        $fields = implode(',', $args['fields']);
        $names = implode(',', $this->getValuesNames($args['fields']));
        $values = array_merge(['id' => null], $values);
        try {
            $table = $this->getTable();
            $requete = $this->pdo->prepare(
                'INSERT INTO ' . $table
                . ' (id,' . $fields . ') '
                . 'VALUES (' . $names . ');'
            );
            $requete->execute($values);
            if ($requete->rowCount() != 0) {
                return 1;
            } else {
                return 0;
            }
        } catch (\PDOException $e) {
            $this->catchReturn($e);
        }
    }

    /**
     * Fonction d'update dans la base de données
     *
     * @param  array $args / fields = array contenant les noms des colonnes avec leurs nouvelles valeurs / conditions =
     * string contenant les conditions qui seront misent dans le WHERE
     * @param array $values
     * @return int
     */
    public function amend(array $args, array $values = []): int
    {
        try {
            $table = $this->getTable();
            $formattedArgs = $this->formatArgs($args);
            $requete = $this->pdo->prepare(
                'UPDATE ' . $table
                . ' SET ' . $formattedArgs['fields']
                . $formattedArgs['conditions']
            );
            $requete->execute($values);
            if ($requete->rowCount() != 0) {
                return 1;
            } else {
                return 0;
            }
        } catch (\PDOException $e) {
            $this->catchReturn($e);
        }
    }

    /**
     * Fonction de suppression dans la base de données
     *
     * @param  array $args conditions = string contenant les conditions qui seront misent dans le WHERE
     * @return int
     */
    public function delete(array $args): int
    {
        try {
            $table = $this->getTable();
            $formattedArgs = $this->formatArgs($args);
            $requete = $this->pdo->query(
                'DELETE'
                . ' FROM ' . $table
                . $formattedArgs['conditions']
            );
            if ($requete->rowCount() != 0) {
                return 1;
            } else {
                return 0;
            }
        } catch (\PDOException $e) {
            $this->catchReturn($e);
        }
    }

    /**
     * Retourne les derniers enregistrements d'une table
     *
     * @param  int $limit nombre de résultats souhaités
     * @return array
     * @throws QueryException
     */
    public function findLast(int $limit = 1): array
    {
        try {
            $requete = $this->pdo->query($this->getQuery('last', [], $limit));
            $requete->execute([]);
            if ($requete->rowCount() != 0) {
                $this->setModelsValues($requete->fetchAll(\PDO::FETCH_OBJ));
                return $this->models;
            } else {
                return [];
            }
        } catch (\PDOException $e) {
            $this->catchReturn($e);
        }
    }

    /**
     * Retourne les premiers enregistrements d'une table
     *
     * @param  int $limit nombre de résultats souhaités
     * @return array
     * @throws QueryException
     */
    public function findFirst(int $limit = 1): array
    {
        try {
            $requete = $this->pdo->query($this->getQuery('last', [], $limit));
            $requete->execute([]);
            if ($requete->rowCount() != 0) {
                $this->setModelsValues($requete->fetchAll(\PDO::FETCH_OBJ));
                return $this->models;
            } else {
                return [];
            }
        } catch (\PDOException $e) {
            $this->catchReturn($e);
        }
    }

    /**
     * Fonction de recherche dans la base de données
     *
     * @param  array $args fields = array contenant les noms des colonnes / conditions = string contenant les conditions
     * qui seront misent dans le WHERE / join = array contenant les jointures (INNER JOIN ou LEFT JOIN)
     * @param array $values
     * @return array
     * @throws QueryException
     */
    public function find(array $args = [], array $values = []): array
    {
        try {
            $requete = $this->pdo->prepare($this->getQuery('select', $args));
            $requete->execute($values);
            if ($requete->rowCount() != 0) {
                $this->setModelsValues($requete->fetchAll(\PDO::FETCH_OBJ));
                return $this->models;
            } else {
                return [];
            }
        } catch (\PDOException $e) {
            $this->catchReturn($e);
        }
    }

    /**
     * Fonction permettant de récupérer des données paginées
     *
     * @param Request $request
     * @param array $args
     * @param int $perPage
     * @return null|Pagerfanta
     * @throws PaginatedException
     */
    public function findPaginated(Request $request, array $args = [], int $perPage = 10): ?Pagerfanta
    {
        $args['formattedFields'] = '*';
        if (isset($args['fields'])) {
            if (is_array($args['fields'])) {
                $args['formattedFields'] = implode(',', $args['fields']);
            }
        }
        if (!isset($args['tables'])) {
            $args['tables'] = $this->getTable();
        } else {
            $args['tables'] = $this->getTable() . ',' . implode(',', $args['tables']);
        }
        if (!isset($args['join'])) {
            $args['join'] = '';
        }
        if (!isset($args['conditions'])) {
            $args['conditions'] = '';
        }
        if (!isset($args['group'])) {
            $args['group'] = '';
        }
        if (!isset($args['order'])) {
            $args['order'] = '';
        }
        $requete = 'SELECT ' . $args['formattedFields']
            . ' FROM ' . $args['tables']
            . $args['join']
            . $args['conditions']
            . $args['group']
            . $args['order'];
        $countRequete = 'SELECT COUNT(id)'
            . ' FROM ' . $args['tables']
            . $args['join']
            . $args['conditions'];
        $adapter = new PaginationAdapter(
            $this->pdo,
            $requete,
            $countRequete
        );
        $params = $request->getQueryParams();
        $page = $params['p'] ?? 1;
        try {
            return (new Pagerfanta($adapter))
                    ->setMaxPerPage($perPage)
                    ->setCurrentPage($page);
        } catch (PaginatedException $e) {
            if (VERSION === 'dev') {
                throw $e;
            }
            return null;
        }
    }

    /**
     * Retourne si la valeur de la colonne est disponible (colonne UNIQUE)
     *
     * @param  string $field colonne ciblée
     * @param  string $value valeur à vérifier
     * @return bool
     * @throws QueryException
     */
    public function isAvailable(string $field, string $value): bool
    {
        if ($this->find(['conditions' => "$field = :$field"], [":$field" => $value])) {
            return false;
        }
        return true;
    }

    /**
     * Vérifie si la clé désirée est présente au sein de l'objet
     *
     * @param  mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->$offset);
    }

    /**
     * Récupère la valeur de la clé désirée
     *
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Ajoute la valeur souhaitée à la clé en argument
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->$offset = $value;
    }

    /**
     * Supprime la valeur de la clé en argument
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        $this->$offset = null;
    }

    /**
     * retourne le \PDOStatement avec la requête demandée ou une chaîne de caractère vide le cas échéant
     *
     * @param string $type
     * @param array $args
     * @param int|null $limit
     * @return string
     * @throws QueryException
     */
    public function getQuery(string $type, array $args = [], int $limit = null): string
    {
        if ($type === 'select') {
            $formattedArgs = $this->formatArgs($args);
            $requete = 'SELECT ' . ($formattedArgs['fields'])
                . ' FROM ' . $formattedArgs['tables']
                . $formattedArgs['join']
                . $formattedArgs['conditions']
                . $formattedArgs['group']
                . $formattedArgs['order']
                . $formattedArgs['limit'];
            return $requete;
        }
        if ($type === 'last' || $type === 'first') {
            $order = ($type === 'last') ? ' ORDER BY id DESC' : ' ORDER BY id ASC';
            $requete = 'SELECT *'
                . ' FROM ' . $this->getTable()
                . $order
                . ' LIMIT ' . $limit;
            return $requete;
        }
        throw new QueryException();
    }

    /**
     * Ajoute les résultats au tableau de l'instance
     *
     * @param array $resultats
     */
    private function setModelsValues(array $resultats): void
    {
        foreach ($resultats as $resultat) {
            $model = $this->getModel();
            foreach ($resultat as $key => $value) {
                $model->$key = $value;
            }
            array_push($this->models, $model);
        }
    }

    /**
     * Retourne le chemin complet vers le model demandée
     *
     * @return \Itval\src\Models\
     */
    private function getModel()
    {
        $className = 'Itval\src\Models\\' . ucfirst($this->getTable()) . 'Model';
        return new $className;
    }

    /**
     * Retourne le tableau des arguments passés à la requête
     *
     * @param array $fields
     * @return array
     */
    private function getValuesNames(array $fields)
    {
        $names = [":id"];
        foreach ($fields as $key) {
            array_push($names, ":$key");
        }
        return $names;
    }

    /**
     * Retourne le nom de la table ciblée
     *
     * @return string
     */
    private function getTable(): string
    {
        $classe = explode('\\', get_class($this));
        return strtolower(trim(end($classe), 'Model'));
    }

    /**
     * Affiche l'erreur capturée après un echec de la classe PDO selon la version du site
     *
     * @param \PDOException $e
     */
    private function catchReturn(\PDOException $e): void
    {
        if (VERSION === 'dev') {
            die('Problème lors du traitement des données ' . $e->getMessage());
        }
        error('Erreur lors du traitement des données');
        redirect();
    }

    /**
     * Fonction qui formatte le tableau des arguments
     *
     * @param  array $args
     * @return array
     */
    private function formatArgs(array $args): array
    {
        if (!isset($args['fields'])) {
            $args['fields'] = '*';
        } else {
            if (is_array($args['fields'])) {
                $args['fields'] = implode(',', $args['fields']);
            }
        }
        if (!isset($args['tables'])) {
            $args['tables'] = $this->getTable();
        } else {
            $args['tables'] = $this->getTable() . ',' . implode(',', $args['tables']);
        }
        if (!isset($args['conditions'])) {
            $args['conditions'] = '';
        } else {
            $args['conditions'] = ' WHERE ' . $args['conditions'];
        }
        if (!isset($args['join'])) {
            $args['join'] = '';
        } else {
            if (is_array($args['join'])) {
                $args['join'] = ' ' . implode(',', $args['join']);
            }
        }
        if (!isset($args['group'])) {
            $args['group'] = '';
        } else {
            $args['group'] = ' GROUP BY ' . $args['group'];
        }
        if (!isset($args['order'])) {
            $args['order'] = '';
        } else {
            $args['order'] = ' ORDER BY ' . $args['order'];
        }
        if (!isset($args['limit'])) {
            $args['limit'] = '';
        } else {
            $args['limit'] = ' LIMIT ' . $args['limit'];
        }
        return $args;
    }
}
