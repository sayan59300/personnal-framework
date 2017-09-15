<?php

namespace Itval\core\DAO;

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
     * @return int
     */
    public function add(array $args): int
    {
        try {
            $table = $this->getTable(get_class($this));
            $requete = $this->pdo->query(
                'INSERT INTO ' . $table
                . ' (id,' . $args['fields'] . ') '
                . 'VALUES (NULL,' . $args['values'] . ')'
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
     * Retourne le nom de la table ciblée
     *
     * @param  string $value
     * @return string
     */
    private function getTable(string $value): string
    {
        $classe = explode('\\', $value);
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
     * Fonction d'update dans la base de données
     *
     * @param  array $args / fields = array contenant les noms des colonnes avec leurs nouvelles valeurs / conditions =
     * string contenant les conditions qui seront misent dans le WHERE
     * @return int
     */
    public function amend(array $args): int
    {
        try {
            $table = $this->getTable(get_class($this));
            $formattedArgs = $this->formatArgs($args);
            $requete = $this->pdo->query(
                'UPDATE ' . $table
                . ' SET ' . $formattedArgs['fields']
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
        if (!isset($args['conditions'])) {
            $args['conditions'] = '';
        } else {
            $args['conditions'] = ' WHERE ' . $args['conditions'];
        }
        if (!isset($args['join'])) {
            $args['join'] = '';
        } else {
            if (is_array($args['join'])) {
                $args['join'] = ' ' . implode(' ', $args['join']);
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

    /**
     * Fonction de suppression dans la base de données
     *
     * @param  array $args conditions = string contenant les conditions qui seront misent dans le WHERE
     * @return int
     */
    public function delete(array $args): int
    {
        try {
            $table = $this->getTable(get_class($this));
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
     */
    public function findLast(int $limit = 1): array
    {
        try {
            $table = $this->getTable(get_class($this));
            $requete = $this->pdo->prepare(
                'SELECT *'
                . ' FROM ' . $table
                . ' ORDER BY id DESC'
                . ' LIMIT ' . $limit
            );
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
        $className = 'Itval\src\Models\\' . ucfirst($this->getTable(get_class($this))) . 'Model';
        return new $className;
    }

    /**
     * Retourne les premiers enregistrements d'une table
     *
     * @param  int $limit nombre de résultats souhaités
     * @return array
     */
    public function findFirst(int $limit = 1): array
    {
        try {
            $table = $this->getTable(get_class($this));
            $requete = $this->pdo->prepare(
                'SELECT *'
                . ' FROM ' . $table
                . ' ORDER BY id ASC'
                . ' LIMIT ' . $limit
            );
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
     * Retourne si la valeur de la colonne est disponible (colonne UNIQUE)
     *
     * @param  string $field colonne ciblée
     * @param  string $value valeur à vérifier
     * @return bool
     */
    public function isAvailable(string $field, string $value): bool
    {
        if (is_string($value)) {
            $value = "'$value'";
        }
        if ($this->find(['conditions' => "$field = " . "$value"])) {
            return true;
        }
        return false;
    }

    /**
     * Fonction de recherche dans la base de données
     *
     * @param  array $args fields = array contenant les noms des colonnes / conditions = string contenant les conditions
     * qui seront misent dans le WHERE / join = array contenant les jointures (INNER JOIN ou LEFT JOIN)
     * @return array
     */
    public function find(array $args = []): array
    {
        try {
            $table = $this->getTable(get_class($this));
            $formattedArgs = $this->formatArgs($args);
            $requete = $this->pdo->prepare(
                'SELECT ' . ($formattedArgs['fields'])
                . ' FROM ' . $table
                . $formattedArgs['join']
                . $formattedArgs['conditions']
                . $formattedArgs['group']
                . $formattedArgs['order']
                . $formattedArgs['limit']
            );
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
}
