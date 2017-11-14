<?php

namespace Itval\src\Services;

use Pagerfanta\Adapter\AdapterInterface;

class PaginationAdapter implements AdapterInterface
{

    /**
     * Instance de PDO pour la connexion à la base de données
     *
     * @var \PDO
     */
    private $pdo;

    /**
     * Requête récupérant les résultats en base de données
     *
     * @var string
     */
    private $query;

    /**
     * Requête qui compte le nombre total de résultats
     *
     * @var string
     */
    private $countQuery;

    /**
     * PaginationAdapter constructor.
     *
     * @param \PDO $pdo
     * @param string $query Requête permettant de récupérer les résultats
     * @param string $countQuery Requête qui compte le nombre de résultats
     */
    public function __construct(\PDO $pdo, string $query, string $countQuery)
    {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->countQuery = $countQuery;
    }

    /**
     * Retourne le nombre de résultats
     *
     * @return integer The number of results.
     */
    public function getNbResults(): int
    {
        return $this->pdo->query($this->countQuery)->fetchColumn();
    }

    /**
     * Retourne une partie des résultats en fonction de la page courante
     *
     * @param integer $offset The offset.
     * @param integer $length The length.
     * @return array|\Traversable The slice.
     */
    public function getSlice($offset, $length)
    {
        $statement = $this->pdo->prepare($this->query . " LIMIT :offset, :length");
        $statement->bindParam('offset', $offset, \PDO::PARAM_INT);
        $statement->bindParam('length', $length, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }
}
