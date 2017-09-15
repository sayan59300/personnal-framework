<?php

namespace Itval\core\interfaces;

/**
 * Interface CacheInterface Interface permettant d'implémenter le système de gestion du cache
 *
 * @package Itval\core\interfaces
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
interface CacheInterface
{

    /**
     * Ajoute un fichier dans le cache
     *
     * @param string $key
     * @param $value
     * @return mixed
     */
    public function write(string $key, $value);

    /**
     * Retourne le fichier en cache
     *
     * @param string $key
     * @return mixed
     */
    public function read(string $key);

    /**
     * Supprime le fichier du cache
     *
     * @param string $key
     * @return mixed
     */
    public function delete(string $key);

    /**
     * Vérifie si une clé est présente en cache
     *
     * @param string $key
     * @return mixed
     */
    public function containsValidKey(string $key);
}
