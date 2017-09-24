<?php

namespace Itval\core\Classes;

use Itval\core\DAO\Tables;
use Itval\core\Factories\LoggerFactory;
use Itval\src\Models\UsersModel;

/**
 * Class Validator Classe contenant les fonctions de validations
 *
 * @package Itval\core\Classes
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class Validator
{
    /**
     * Contient les valeurs à valider
     *
     * @var array
     */
    private $values;

    /**
     * Contient les erreurs de validation
     *
     * @var array
     */
    private $errors = [];

    /**
     * Validator constructor.
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->values = $args;
    }

    /**
     * Retourne les valeurs (utilisé dans les tests unitaires)
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Contrôle la validité d'un email et sa confirmation si nécessaire
     *
     * @param string $key
     * @param bool $required
     * @param string $confirmation
     * @return bool
     */
    public function isValidEmail(string $key, bool $required = false, string $confirmation = null)
    {
        if ($required) {
            if ($this->required($key) === false) {
                return false;
            }
        }
        if (filter_var($this->values[$key], FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[$key] = $this->invalidValue();
            return false;
        }
        if (!is_null($confirmation)) {
            if ($this->values[$key] !== $this->values[$confirmation]) {
                $this->errors[$confirmation] = "La confirmation de l'email ne correspond pas";
                return false;
            }
            Session::delete('validator_error_' . $confirmation);
            return true;
        }
        Session::delete('validator_error_' . $key);
        Session::delete('validator_error_' . $confirmation);
        return true;
    }

    /**
     * Contrôle si une champ requis est renseigné
     *
     * @param string $key
     * @return bool
     */
    public function required(string $key): bool
    {
        if (empty($this->values[$key])) {
            $this->errors[$key] = "Le champ est requis";
            return false;
        }
        Session::delete('validator_error_' . $key);
        return true;
    }

    /**
     * Retourne le message pour les champs invalides
     *
     * @return string
     */
    private function invalidValue()
    {
        return "La valeur entrée n'est pas valide";
    }

    /**
     * Contrôle la validité d'un string en fonction du regex en arguments et sa confirmation si nécessaire
     *
     * @param string $key
     * @param  string $regex
     * @param bool $required
     * @param string $confirmation
     * @return bool
     */
    public function isValidString(string $key, string $regex, bool $required = false, string $confirmation = null)
    {
        if ($required) {
            if ($this->required($key) === false) {
                return false;
            }
        }
        if (filter_var($this->values[$key], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => $regex]]) === false) {
            $this->errors[$key] = $this->invalidValue();
            return false;
        }
        if (!is_null($confirmation)) {
            if ($this->values[$key] !== $this->values[$confirmation]) {
                $this->errors[$confirmation] = "Les valeurs ne correspondent pas";
                return false;
            }
            Session::delete('validator_error_' . $confirmation);
            return true;
        }
        Session::delete('validator_error_' . $key);
        Session::delete('validator_error_' . $confirmation);
        return true;
    }

    /**
     * Contrôle la validité d'un entier
     *
     * @param string $key
     * @param bool $required
     * @return bool
     */
    public function isValidInt(string $key, bool $required = false)
    {
        if ($required) {
            if ($this->required($key)) {
                if (!is_integer($this->values[$key])) {
                    $this->errors[$key] = $this->invalidValue();
                    return false;
                }
                Session::delete('validator_error_' . $key);
                return true;
            }
            return false;
        }
        if (!is_integer($this->values[$key])) {
            $this->errors[$key] = $this->invalidValue();
            return false;
        }
        Session::delete('validator_error_' . $key);
        return true;
    }

    /**
     * Contrôle la validité d'un float
     *
     * @param string $key
     * @param bool $required
     * @return bool
     */
    public function isValidFloat(string $key, bool $required = false)
    {
        if ($required) {
            if ($this->required($key)) {
                if (!is_float($this->values[$key])) {
                    $this->errors[$key] = $this->invalidValue();
                    return false;
                }
                Session::delete('validator_error_' . $key);
                return true;
            }
            return false;
        }
        if (!is_float($this->values[$key])) {
            $this->errors[$key] = $this->invalidValue();
            return false;
        }
        Session::delete('validator_error_' . $key);
        return true;
    }

    /**
     * Contrôle si le champ désiré est libre (unique en base de données)
     *
     * @param string $model
     * @param string $field
     * @return bool
     */
    public function isAvailable(string $model, string $field)
    {
        /** @var Tables $model */
        $model = new $model;
        if (strstr($field, '_')) {
            $parts = explode('_', $field);
            $modelField = $parts[0];
        } else {
            $modelField = $field;
        }
        $id = $this->values['id'] ?? 0;
        $check = $model->find(['fields' => $modelField, 'conditions' => 'id = ' . $id]);
        if ($check !== []) {
            if ($this->values[$field] !== current($check)->$modelField) {
                if (!$model->isAvailable($modelField, $this->values[$field])) {
                    $this->errors[$field] = "La valeur entrée est déja prise";
                    return false;
                }
                Session::delete('validator_error_' . $field);
                return true;
            }
            Session::delete('validator_error_' . $field);
            return true;
        } else {
            if (!$model->isAvailable($modelField, $this->values[$field])) {
                $this->errors[$field] = "La valeur entrée est déja prise";
                return false;
            }
            Session::delete('validator_error_' . $field);
            return true;
        }
    }

    /**
     * Retourne le tableau d'erreurs
     *
     * @return int
     */
    public function getErrors(): int
    {
        foreach ($this->errors as $key => $value) {
            Session::set('validator_error_' . $key, " * $value");
        }
        return count($this->errors);
    }

    /**
     * Permet d'ajouter une erreur depuis l'extérieur dans le cas de contrôles supplémentaires non gérés nativement
     *
     * @param string $field
     * @param string $message
     */
    public function setError(string $field, string $message)
    {
        $this->errors[$field] = $message;
        Session::set('validator_error_' . $field, " * $message");
    }
}
