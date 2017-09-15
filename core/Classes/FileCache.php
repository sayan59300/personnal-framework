<?php

namespace Itval\core\Classes;

use Itval\core\interfaces\CacheInterface;

/**
 * Class FileCache Permet de gérer le cache fichier
 *
 * @package Itval\core\Classes
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class FileCache implements CacheInterface
{

    /**
     * Durée de validité du fichier en cache exprimé en minutes
     * Default 60
     *
     * @var int
     */
    private $duration;

    /**
     * Dossier contenant les fichiers en cache
     *
     * @var string
     */
    private $directory;

    /**
     * FileCache constructor.
     *
     * @param int    $duration
     * @param string $directory
     */
    public function __construct($duration = 60, $directory = ROOT . DS . 'tmp' . DS . 'cache' . DS)
    {
        $this->duration = $duration;
        $this->directory = $directory;
    }

    /**
     * Permet de lire un fichier du cache
     *
     * @param  string $key
     * @return bool|string
     * @throws \Exception
     */
    public function read(string $key)
    {
        $file = $this->directory . $key;
        $lifetime = (time() - filemtime($file)) / 60;
        if ($lifetime > $this->duration) {
            return false;
        }
        if (!file_exists($file)) {
            if (VERSION === 'dev') {
                throw new \Exception('Le fichier demandé est absent du cache fichier');
            } else {
                return false;
            }
        }
        return file_get_contents($file);
    }

    /**
     * Permet de mettre des données en cache
     *
     * @param string $key
     * @param mixed  $value
     */
    public function write(string $key, $value): void
    {
        file_put_contents($this->directory . $key, $value);
    }

    /**
     * Permet de supprimer un fichier du cache
     *
     * @param  string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        $file = $this->directory . $key;
        if (file_exists($file)) {
            unlink($file);
            return true;
        }
        return false;
    }

    /**
     * Permet de vider le dossier cache
     */
    public function clear(): void
    {
        $files = glob($this->directory . '*');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    /**
     * Vérifie si le cache contient la clé indiquée
     *
     * @param  string $key
     * @return bool
     */
    public function containsValidKey(string $key): bool
    {
        $file = $this->directory . $key;
        $lifetime = (time() - filemtime($file)) / 60;
        if ($file && ($lifetime < $this->duration)) {
            return true;
        } else {
            return false;
        }
    }
}
