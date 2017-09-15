<?php

use PHPUnit\Framework\TestCase;
use Itval\core\Classes\Asset;

require_once 'components.php';

class AssetTest extends TestCase
{

    /**
     * Css
     */
    public function testAssetCssWithLocalFile()
    {
        $css = Asset::getAssetCss('style.css');
        $this->assertEquals('<link rel="stylesheet" href="http:///public/resources/css/style.css" type="text/css" />', $css);
    }

    public function testAssetCssWithLocalFileAndAttributes()
    {
        $css = Asset::getAssetCss('style.css', false, ['class' => 'attributCss']);
        $this->assertEquals('<link rel="stylesheet" href="http:///public/resources/css/style.css" type="text/css" class="attributCss" />', $css);
    }

    public function testAssetCssWithCdn()
    {
        $css = Asset::getAssetCss('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', true);
        $this->assertEquals('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" type="text/css" />', $css);
    }

    public function testAssetCssWithCdnAndAttributes()
    {
        $css = Asset::getAssetCss(
            'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
            true,
            [
                'integrity' => 'sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u',
                'crossorigin' => 'anonymous'
            ]
        );
        $this->assertEquals('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" type="text/css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous" />', $css);
    }

    /**
     * Js
     */
    public function testAssetJsWithLocalFile()
    {
        $js = Asset::getAssetJs('app.js');
        $this->assertEquals('<script src="http:///public/resources/js/app.js" ></script>', $js);
    }

    public function testAssetJsWithLocalFileAndAttributes()
    {
        $js = Asset::getAssetJs('app.js', false, ['class' => 'attributJs']);
        $this->assertEquals('<script src="http:///public/resources/js/app.js" class="attributJs" ></script>', $js);
    }

    public function testAssetJsWithCdn()
    {
        $js = Asset::getAssetJs('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', true);
        $this->assertEquals('<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" ></script>', $js);
    }

    public function testAssetJsWithCdnAndAttributes()
    {
        $js = Asset::getAssetCss(
            'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
            true,
            ['integrity' => 'sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u',
                'crossorigin' => 'anonymous']
        );
        $this->assertEquals('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" type="text/css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous" />', $js);
    }
}
