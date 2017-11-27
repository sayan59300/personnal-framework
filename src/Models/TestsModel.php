<?php

namespace Itval\src\Models;

use Itval\core\DAO\Tables;

/**
 * Class TestsModel
 *
 * @package Itval\src\Models
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class TestsModel extends Tables
{

    /**
     * Identifiant du test
     *
     * @var int
     */
    public $id;

    /**
     * Nom du test
     *
     * @var string
     */
    public $nom;

    /**
     * Description du test
     *
     * @var string
     */
    public $description;

    /**
     * Contenu du test
     *
     * @var string
     */
    public $contenu;

    public function __construct(\PDO $pdo = null)
    {
        parent::__construct($pdo);
    }
}
