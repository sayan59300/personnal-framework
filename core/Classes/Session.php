<?php

namespace Itval\core\Classes;

/**
 * Class Session Classe qui permet de gérer les sessions
 *
 * @package Itval\core\Classes
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class Session
{

    /**
     * Permet de donner une valeur à une variable de session
     *
     * @param string $key   Clé de la variable de session
     * @param mixed  $value Valeur à attribuer
     */
    public static function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Ajoute un attribut dans l'objet stocké dans la variable de session
     *
     * @param  string $key   Clé de la variable de session
     * @param  string $name  Nom de l'attribut de l'objet
     * @param  mixed  $value Valeur à attribuer à l'attribut
     * @return bool
     */
    public static function add(string $key, string $name, $value): bool
    {
        if (is_object($_SESSION[$key])) {
            $_SESSION[$key]->$name = $value;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Ajoute une valeur à la fin d'un tableau stocké en variable de session en l'initialisant si il n'éxiste pas
     *
     * @param string $key   Clé de la variable de session
     * @param mixed  $value Valeur à ajouter à la fin du tableau
     */
    public static function apend(string $key, $value)
    {
        if (isset($_SESSION[$key])) {
            array_push($_SESSION[$key], $value);
        } else {
            self::set($key, $value);
        }
    }

    /**
     * Retourne la valeur d'une variable de session
     *
     * @param  string $key Clé de la variable de session
     * @return mixed
     */
    public static function read(string $key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
    }

    /**
     * Supprime une variable de session
     *
     * @param string $key Clé de la variable de session
     */
    public static function delete(string $key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
}
