<?php

use PHPUnit\Framework\TestCase;
use Itval\core\Classes\Flash;

require_once 'components.php';

class FlashTest extends TestCase
{

    /**
     * @var Flash
     */
    private $flash;

    public function setUp()
    {
        $this->flash = Flash::getInstance();
    }

    /**
     * Méthode success
     */
    public function testSuccessFlash()
    {
        $success = $this->flash->success('Message success de test');
        $this->assertEquals(
            '<div class="alert alert-' . 'success' . ' alert-dismissible">'
            . '<button type="button" class="close" data-dismiss="alert">'
            . '<span aria-hidden="true">&times;</span>'
            . '<span class="sr-only">Close</span>'
            . '</button>' . 'Message success de test' . '</div>',
            $success
        );
    }

    /**
     * Méthode error
     */
    public function testErrorFlash()
    {
        $success = $this->flash->error('Message error de test');
        $this->assertEquals(
            '<div class="alert alert-' . 'danger' . ' alert-dismissible">'
            . '<button type="button" class="close" data-dismiss="alert">'
            . '<span aria-hidden="true">&times;</span>'
            . '<span class="sr-only">Close</span>'
            . '</button>' . 'Message error de test' . '</div>',
            $success
        );
    }

    /**
     * Méthode warning
     */
    public function testWarningFlash()
    {
        $success = $this->flash->warning('Message warning de test');
        $this->assertEquals(
            '<div class="alert alert-' . 'warning' . ' alert-dismissible">'
            . '<button type="button" class="close" data-dismiss="alert">'
            . '<span aria-hidden="true">&times;</span>'
            . '<span class="sr-only">Close</span>'
            . '</button>' . 'Message warning de test' . '</div>',
            $success
        );
    }

    /**
     * Méthode info
     */
    public function testInfoFlash()
    {
        $success = $this->flash->info('Message info de test');
        $this->assertEquals(
            '<div class="alert alert-' . 'info' . ' alert-dismissible">'
            . '<button type="button" class="close" data-dismiss="alert">'
            . '<span aria-hidden="true">&times;</span>'
            . '<span class="sr-only">Close</span>'
            . '</button>' . 'Message info de test' . '</div>',
            $success
        );
    }
}
