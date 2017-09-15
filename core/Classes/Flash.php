<?php

namespace Itval\core\Classes;

/**
 * Class Flash Classe qui permet de gérer les messages flash
 *
 * @package Itval\core\Classes
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class Flash
{

    /**
     * Variable qui stocke l'instance de la classe
     *
     * @var Flash
     */
    private static $_instance;

    /**
     * Constructeur
     */
    private function __construct()
    {
    }

    /**
     * Retourne une instance unique (singleton)
     *
     * @return Flash
     */
    public static function getInstance(): self
    {
        if (self::$_instance === null) {
            self::$_instance = new Flash();
        }
        return self::$_instance;
    }

    /**
     * Retourne un message flash de succes de couleur verte
     *
     * @param  string $argMessage
     * @return string
     */
    public function success(string $argMessage): string
    {
        return $this->getFlashMsg('success', $argMessage);
    }

    /**
     * Retourne un message flash d'information de couleur bleu
     *
     * @param  string $argMessage
     * @return string
     */
    public function info(string $argMessage): string
    {
        return $this->getFlashMsg('info', $argMessage);
    }

    /**
     * Retourne un message flash d'avertissement de couleur jaune
     *
     * @param  string $argMessage
     * @return string
     */
    public function warning(string $argMessage): string
    {
        return $this->getFlashMsg('warning', $argMessage);
    }

    /**
     * Retourne un message flash d'avertissement important de couleur rouge
     *
     * @param  string $argMessage
     * @return string
     */
    public function error(string $argMessage): string
    {
        return $this->getFlashMsg('danger', $argMessage);
    }

    /**
     * Retourne un message flash formaté en fonction des arguments en paramètre
     *
     * @param  string $argType
     * @param  string $argMessage
     * @return string
     */
    private static function getFlashMsg(string $argType, string $argMessage): string
    {
        return '<div class="alert alert-' . $argType . ' alert-dismissible">'
            . '<button type="button" class="close" data-dismiss="alert">'
            . '<span aria-hidden="true">&times;</span>'
            . '<span class="sr-only">Close</span>'
            . '</button>' . $argMessage . '</div>';
    }
}
