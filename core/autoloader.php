<?php

/**
 * Autoloader qui charge tous les fichiers du dossier souhaité
 *
 * @param string $fileFolder
 */
function autoLoader($fileFolder)
{
    $filesListe = getClasseListe(scandir($fileFolder));
    getRequireList($filesListe, $fileFolder);
}

/**
 * Fonction de création de la liste des require_once
 *
 * @param array $file
 * @param string $folder
 */
function getRequireList(array $file, string $folder)
{
    foreach ($file as &$value) {
        $extension = explode('.', $value);
        if (end($extension) === 'php') {
            require_once $folder . $value;
        }
    }
}

/**
 * Fonction qui supprime . et .. du tableau de classes
 *
 * @param array $files
 * @return array
 */
function getClasseListe(array $files)
{
    foreach ($files as $key => $value) {
        if ($value == '.') {
            unset($files[$key]);
        }
        if ($value == '..') {
            unset($files[$key]);
        }
    }
    return $files;
}
