<?php
/* * *************** * */
/* * HELPER SECURITY * */
/* * *************** * */

use Itval\core\Classes\Session;

/**
 * Fonction de cryptage
 *
 * @param  string $value
 * @return string
 */
function encrypted(string $value): string
{
    return hash('sha512', md5(sha1($value)));
}

/**
 * Génération de mot de passe aléatoire
 *
 * @param  int $length
 * @return string
 */
function random_token(int $length = 74): string
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!$-_';
    $password = substr(str_shuffle($chars), 0, $length);
    return $password;
}

/**
 * Ecrit si l'utilisateur à vérifié son compte en fonction de la réponse passée en argument
 *
 * @param  string $value
 * @return string
 */
function printConfirm(string $value): string
{
    if ($value == '1') {
        return 'Oui';
    } else {
        return 'Non';
    }
}

/**
 * Retourne l'utilisateur connecté
 *
 * @return stdClass|null
 */
function currentUser()
{
    return Session::read('auth');
}

/**
 * Retourne un token csrf
 *
 * @return string
 */
function generateToken(): string
{
    $token = random_token();
    Session::set('csrf_token', $token);
    Session::set('time', time());
    return $token;
}
