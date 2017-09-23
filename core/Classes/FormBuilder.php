<?php

namespace Itval\core\Classes;

/**
 * Class FormBuilder Classe qui génère les formulaires
 *
 * @package Itval\core\Classes
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class FormBuilder extends Html
{

    /**
     * Nom du formulaire
     *
     * @var string
     */
    public $name;

    /**
     * Méthode utilisée par le formulaire
     *
     * @var string
     */
    public $method;

    /**
     * Action à la soumission du formulaire
     *
     * @var string
     */
    public $action;

    /**
     * Tableau des éléments du formulaire
     *
     * @var array
     */
    public $elements = [];

    /**
     * FormBuilder constructor.
     *
     * @param $name
     * @param $method
     * @param $action
     */
    public function __construct($name, $method, $action)
    {
        $this->name = $name;
        $this->method = $method;
        $this->action = $action;
    }

    /**
     * Génère un input pour la gestion des failles CSRF et l'ajoute au tableau des elements
     *
     * @param  string $csrfToken
     * @return FormBuilder
     */
    public function setCsrfInput(string $csrfToken): self
    {
        $csrf = '<div>'
            . '<input type="hidden" '
            . 'name="csrf_token" value="' . $csrfToken . '"/>'
            . '</div>';
        $this->elements['token_csrf'] = $csrf;
        return $this;
    }

    /**
     * Génère un input et l'ajoute au tableau des elements
     *
     * @param  string      $type
     * @param  string      $name
     * @param  array       $attributesArgs
     * @param  string|null $textLabel
     * @return FormBuilder
     */
    public function setInput(string $type, string $name, array $attributesArgs = [], string $textLabel = null): self
    {
        $label = self::setLabel($name, $textLabel);
        $classes = $this->getClassAttributes($attributesArgs);
        if (isset($attributesArgs['class'])) {
            unset($attributesArgs['class']);
        }
        $attributes = self::formatAttributes($attributesArgs);
        if ($type === 'file') {
            $input = '<div class="form-group">' . $label . '<input type="file" '
                . 'class="input-file' . ' ' . $classes . '" name="' . $name . '" ' . 'id="' . $name . '" ' . $attributes . '/>'
                . '<span style="color: red;">' . Session::read('validator_error_' . $name) . '</span></div>';
        } elseif ($type === 'hidden') {
            $input = '<input type="' . $type . '" name="' . $name . '" ' . $attributes . '/>';
        } else {
            $input = '<div class="form-group">' . $label . '<input type="' . $type . '" '
                . 'class="form-control' . ' ' . $classes . '" name="' . $name . '" ' . 'id="' . $name . '" ' . $attributes . '/>'
                . '<span style="color: red;">' . Session::read('validator_error_' . $name) . '</span></div>';
        }
        $this->elements[$name] = $input;
        return $this;
    }

    /**
     * Génère un bouton et l'ajoute au tableau des elements
     *
     * @param  string $type
     * @param  string $name
     * @param  string $texte
     * @param  array  $attributesArgs
     * @return FormBuilder
     */
    public function setButton(string $type, string $name, string $texte, array $attributesArgs = []): self
    {
        $classes = $this->getClassAttributes($attributesArgs, 'btn btn-primary');
        if (isset($attributesArgs['class'])) {
            unset($attributesArgs['class']);
        }
        $attributes = self::formatAttributes($attributesArgs);
        $button = '<div class="form-group"><button type="' . $type . '" '
            . 'class="' . $classes . '" name="' .
            $name . '" ' . $attributes . '>' . $texte . '</button>' . '</div>';
        $this->elements[$name] = $button;
        return $this;
    }

    /**
     * Génère un textArea et l'ajoute au tableau des elements
     *
     * @param  string      $rows
     * @param  string      $name
     * @param  array       $attributesArgs
     * @param  string|null $textLabel
     * @param  string|null $content
     * @return FormBuilder
     */
    public function setTextArea(string $rows, string $name, array $attributesArgs = [], string $textLabel = null, string $content = null): self
    {
        $label = self::setLabel($name, $textLabel);
        $classes = $this->getClassAttributes($attributesArgs);
        if (isset($attributesArgs['class'])) {
            unset($attributesArgs['class']);
        }
        $attributes = self::formatAttributes($attributesArgs);
        $textArea = '<div class="form-group">' . $label . '<textarea rows="' . $rows . '" '
            . 'class="form-control' . ' ' . $classes . '" name="' . $name . '" ' . $attributes . '>' . $content . '</textarea>'
            . '<span style="color: red;">' . Session::read('validator_error_' . $name) . '</span></div>';
        $this->elements[$name] = $textArea;
        return $this;
    }

    /**
     * Génère un label
     *
     * @param  string      $id
     * @param  string|null $texte
     * @return string
     */
    private static function setLabel(string $id, string $texte = null): string
    {
        if (!$texte) {
            return '<label for="' . $id . '">' . ucfirst($id) . '</label>';
        }
        return '<label for="' . $id . '">' . $texte . '</label>';
    }

    /**
     * Retourne les attributs de la balise html correctement formattés
     *
     * @param  array $attributesArgs
     * @return string
     */
    private static function formatAttributes(array $attributesArgs): string
    {
        if (isset($attributesArgs)) {
            $attributes = self::getAttributes($attributesArgs);
        }
        return $attributes;
    }

    /**
     * Retourne l'attribut class correctement formatté
     *
     * @param  array       $attributes
     * @param  string|null $defaut
     * @return string
     */
    private function getClassAttributes(array $attributes, string $defaut = null): string
    {
        return $classes = $attributes['class'] ?? $defaut ?? '';
    }
}
