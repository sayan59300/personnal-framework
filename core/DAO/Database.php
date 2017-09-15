<?php

namespace Itval\core\DAO;

use PDO;

/**
 * Class Database singleton de connexion à la base de données
 *
 * @package Itval\core\DAO
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class Database
{

    /**
     * Variable qui stocke l'instance de la classe
     *
     * @var \PDO
     */
    private static $_pdo;

    /**
     * Database constructor.
     */
    private function __construct()
    {
    }

    /**
     * Singleton
     *
     * @return \PDO
     */
    public static function getPdo(): PDO
    {
        if (self::$_pdo === null) {
            try {
                $pdo = new PDO(
                    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
                    DB_USER,
                    DB_PASS,
                    [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']
                );
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$_pdo = $pdo;
            } catch (\PDOException $e) {
                if (VERSION === 'dev') {
                    die('Problème avec la base de donnée ' . $e->getMessage());
                } else {
                    die('Problème avec la base de donnée, contactez votre administrateur.');
                }
            }
        }
        return self::$_pdo;
    }
}
