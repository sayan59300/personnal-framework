<?php

namespace Itval\core\Classes;

/**
 * Class Asset Classe qui génère les différents assets
 *
 * @package Itval\core\Classes
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class Asset extends Html
{

    /**
     * Retourne asset CSS
     *
     * @param  string $href
     * @param  bool   $cdn
     * @param  array  $attributesArgs
     * @return string
     */
    public static function getAssetCss(string $href, bool $cdn = false, array $attributesArgs = []): string
    {
        $attributes = '';
        if (isset($attributesArgs)) {
            $attributes = self::getAttributes($attributesArgs);
        }
        if ($cdn) {
            return '<link rel="stylesheet" href="' . $href . '" type="text/css" ' . $attributes . '/>';
        }
        return '<link rel="stylesheet" href="' . ASSETS_CSS . DS . $href . '" type="text/css" ' . $attributes . '/>';
    }

    /**
     * Retourne asset icone
     *
     * @param  string $href
     * @param  bool   $cdn
     * @param  array  $attributesArgs
     * @return string
     */
    public static function getAssetIcon(string $href, bool $cdn = false, array $attributesArgs = []): string
    {
        $attributes = '';
        if (isset($attributesArgs)) {
            $attributes = self::getAttributes($attributesArgs);
        }
        if ($cdn) {
            return '<link rel="icon" href="' . $href . ' " ' . $attributes . '/>';
        }
        return '<link rel="icon" href="' . IMG . DS . $href . '" ' . $attributes . '/>';
    }

    /**
     * Retourne asset javasript
     *
     * @param  string $src
     * @param  bool   $cdn
     * @param  array  $attributesArgs
     * @return string
     */
    public static function getAssetJs(string $src, bool $cdn = false, array $attributesArgs = []): string
    {
        $attributes = '';
        if (isset($attributesArgs)) {
            $attributes = self::getAttributes($attributesArgs);
        }
        if ($cdn) {
            return '<script src="' . $src . '" ' . $attributes . '></script>';
        }
        return '<script src="' . ASSETS_JS . DS . $src . '" ' . $attributes . '></script>';
    }
}
