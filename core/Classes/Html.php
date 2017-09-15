<?php

namespace Itval\core\Classes;

/**
 * Class Html Classe qui contient les fonctions génériques pour générer le contenu html
 *
 * @package Itval\core\Classes
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class Html
{

    /**
     * Génère les attributs
     *
     * @param  array $attributesArgs
     * @return string
     */
    protected static function getAttributes(array $attributesArgs = []): string
    {
        $attributes = '';
        foreach ($attributesArgs as $key => $value) {
            if ($key === 'checked') {
                if ($value === 'checked') {
                    $attributes .= $value . '=""';
                }
            } else {
                $attributes .= $key . '="' . $value . '" ';
            }
        }
        return $attributes;
    }
}
