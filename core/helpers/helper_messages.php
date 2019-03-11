<?php

/* * *************** * */
/* * HELPER MESSAGES * */
/* * *************** * */

use Itval\core\Classes\Session;
use Itval\core\Classes\Flash;

/**
 * Retourne une instance de la classe Flash
 *
 * @param string $type
 * @param string $message
 * @return string
 */
function flashMessage(string $type, string $message): string
{
    return Flash::getInstance()->$type($message);
}

/**
 * Construit un message flash d'erreur en session pour un affichage unique
 *
 * @param string $message
 */
function error(string $message): void
{
    Session::set('errors', flashMessage('error', $message));
}

/**
 * Construit un message flash de succés en session pour un affichage unique
 *
 * @param string $message
 */
function success(string $message): void
{
    Session::set('success', flashMessage('success', $message));
}

/**
 * Construit un message flash de warning en session pour un affichage unique
 *
 * @param string $message
 */
function warning(string $message): void
{
    Session::set('warning', flashMessage('warning', $message));
}

/**
 * Construit un message flash d'information en session pour un affichage unique
 *
 * @param string $message
 */
function info(string $message): void
{
    Session::set('info', flashMessage('info', $message));
}

/**
 * Affiche le message flash si il est défini et le détruit ensuite
 *
 * @param mixed $key
 */
function printFlash($key): void
{
    echo Session::read($key) ?? '';
    Session::delete($key);
}
