<?php

namespace Itval\core\DAO\Exception;

use Exception;
use Throwable;

/**
 * Class QueryException Exception levée lors d'une erreur de génération de requête SQL
 *
 * @package Itval\core\DAO\Exception
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class QueryException extends Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $this->message = 'Une erreur est survenue lors de la création de la requête SQL';
    }
}
