<?php

namespace Itval\src\Traits;

use Itval\core\Factories\LoggerFactory;
use Itval\src\Models\UsersModel;

/**
 * Trait UsersTreatments Trait contenant les méthodes partagées de traitement des utilisateurs
 *
 * @package Itval\src\Traits
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
trait UsersTreatments
{

    /**
     * Ajoute le nouveau password à l'objet user si celui ci est defini
     *
     * @param  array      $values
     * @param  UsersModel $user
     * @return bool
     */
    private function setNewPassword(array $values, UsersModel $user): bool
    {
        if (isset($values['password'])) {
            LoggerFactory::getInstance('users')->addInfo('Modification du mot de passe', ['username' => $user->username]);
            $user->password = encrypted($values['password']);
            return true;
        } else {
            return false;
        }
    }
}
