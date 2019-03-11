<?php

namespace Itval\core\DAO\Exception;

use Exception;
use Throwable;

/**
 * Class PaginatedException Exception levée lors de l'appel de la fonction findPaginated de l'ORM
 *
 * @package Itval\core\DAO\Exception
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class PaginatedException extends Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $this->message = 'Une erreur est survenue lors de la requête paginée';
    }
}
