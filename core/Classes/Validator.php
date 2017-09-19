<?php

namespace Itval\core\Classes;

use Itval\core\DAO\Tables;

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
            array_push($this->errors, $this->invalidValue($key));
            return false;
        }
        if (!is_null($confirmation)) {
            if ($this->values[$key] !== $this->values[$confirmation]) {
                array_push($this->errors, "La confirmation de l'email ne correspond pas");
                return false;
            }
            return true;
        }
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
            array_push($this->errors, "Le champ $key est requis");
            return false;
        }
        return true;
    }

    /**
     * Retourne le message pour les champs invalides
     *
     * @param string $key
     * @return string
     */
    private function invalidValue(string $key)
    {
        return "Le champ $key n'est pas valide";
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
            array_push($this->errors, $this->invalidValue($key));
            return false;
        }
        if (!is_null($confirmation)) {
            if ($this->values[$key] !== $this->values[$confirmation]) {
                array_push($this->errors, "La confirmation du champ $key ne correspond pas");
                return false;
            }
            return true;
        }
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
                    array_push($this->errors, $this->invalidValue($key));
                    return false;
                }
                return true;
            }
            return false;
        }
        if (!is_integer($this->values[$key])) {
            array_push($this->errors, $this->invalidValue($key));
            return false;
        }
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
                    array_push($this->errors, $this->invalidValue($key));
                    return false;
                }
                return true;
            }
            return false;
        }
        if (!is_float($this->values[$key])) {
            array_push($this->errors, $this->invalidValue($key));
            return false;
        }
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
        $id = $this->values['id'] ?? 0;
        $check = $model->find(['fields' => $field, 'conditions' => 'id = ' . $id]);
        if ($check !== []) {
            if ($this->values[$field] !== current($check)->username) {
                if (!$model->isAvailable($field, $this->values[$field])) {
                    array_push($this->errors, "Le champ $field est déja pris");
                    return false;
                }
                return true;
            }
            return true;
        } else {
            if (!$model->isAvailable($field, $this->values[$field])) {
                array_push($this->errors, "Le champ $field est déja pris");
                return false;
            }
            return true;
        }
    }

    /**
     * Retourne le tableau d'erreurs
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
