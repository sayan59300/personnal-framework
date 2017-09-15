<?php

namespace Itval\core\Classes;

/**
 * Class Validator Classe contenant les fonctions de validations
 *
 * @package Itval\core\Classes
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class Validator
{

    /**
     * Retourne false ou la chaine de caractère si elle est valide
     *
     * @param  string $value
     * @return bool|string
     */
    public static function isValidEmail(string $value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Retourne false ou la chaine de caractère si elle est validée par le regex
     *
     * @param  string $regex
     * @param  string $value
     * @return bool|string
     */
    public static function isValidString(string $regex, string $value)
    {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => $regex]]);
    }

    /**
     * Retourne false ou la valeur de l'entier si elle est valide
     *
     * @param  int $value
     * @return mixed
     */
    public static function isValidInt(int $value)
    {
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Retourne false ou la valeur du float si elle est valide
     *
     * @param  float $value
     * @return mixed
     */
    public static function isValidFloat(float $value)
    {
        return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
    }

    /**
     * Vérifie que la session d'authentification existe et qu'un utilisateur est connecté
     *
     * @return bool
     */
    public static function isAuthenticated(): bool
    {
        if (currentUser() && currentUser()->statut === 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Contrôle si l'utilisateur est l'utilisateur connecté
     *
     * @param  string $id
     * @return bool
     */
    public static function isCurrentUser(string $id): bool
    {
        if (currentUser()->id === $id) {
            return true;
        }
    }

    /**
     * Contrôle si le token csrf est valide
     *
     * @return bool
     */
    public static function isValidToken(): bool
    {
        if ((filter_input(INPUT_POST, 'csrf_token') === Session::read('csrf_token'))) {
            return true;
        } else {
            return false;
        }
    }
}
