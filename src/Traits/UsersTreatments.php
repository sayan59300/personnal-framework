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
     * Contrôle les valeurs passées par le formulaire d'inscription
     *
     * @param  array $values
     * @return bool|array
     */
    private function registrationValidation(array $values)
    {
        $wrongValues = [];
        if (isset($values['nom']) && !$values['nom']) {
            array_push($wrongValues, 'Le nom n\'est pas valide');
        }
        if (isset($values['prenom']) && !$values['prenom']) {
            array_push($wrongValues, 'Le prenom n\'est pas valide');
        }
        if (isset($values['email']) && !$values['email']) {
            array_push($wrongValues, 'L\'adresse email n\'est pas valide');
        } elseif (!isset($values['profil']) && isset($values['email']) && $values['email']) {
            if ($values['email'] !== $values['confirm_email']) {
                array_push($wrongValues, 'La confirmation de l\'adresse email n\'est pas valide');
            }
        }
        if (isset($values['username']) && !$values['username']) {
            array_push($wrongValues, 'Le nom d\'utilisateur n\'est pas valide');
        } else {
            $model = new UsersModel;
            $username = currentUser()->username ?? '';
            if ($model->isAvailable('username', $values['username']) && $values['username'] !== $username) {
                array_push($wrongValues, 'Le nom d\'utilisateur est déjà prit');
            }
        }
        if (isset($values['password']) && !$values['password']) {
            array_push($wrongValues, 'Le mot de passe n\'est pas valide');
        } else {
            if (isset($values['password']) && ($values['password'] !== $values['confirm_password'])) {
                array_push($wrongValues, 'La confirmation du mot de passe n\'est pas valide');
            }
        }
        if ($wrongValues == []) {
            return true;
        } else {
            return $wrongValues;
        }
    }

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
