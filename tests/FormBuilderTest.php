<?php

use PHPUnit\Framework\TestCase;
use Itval\core\Classes\FormBuilder;

require_once 'components.php';

class FormBuilderTest extends TestCase
{

    public function setUp()
    {
        $this->builder = new FormBuilder('form', 'post', 'http://localhost.dev');
    }

    /**
     * Fonction setCsrfInput
     */
    public function testGetCsrfToken()
    {
        $token = random_token();
        $this->builder->setCsrfInput($token);
        $this->assertEquals(
            '<div>'
            . '<input type="hidden" name="csrf_token" value="' . $token . '"/>'
            . '</div>',
            $this->builder->elements['token_csrf']
        );
    }

    /**
     * Fonction setInput type text
     */
    public function testGetInputTextWithoutAttributesAndTextLabel()
    {
        $this->builder->setInput('text', 'nomdetest');
        $this->assertEquals(
            '<div class="form-group"><label for="nomdetest">Nomdetest</label><input type="text" '
            . 'class="form-control " name="nomdetest" id="nomdetest" /></div>',
            $this->builder->elements['nomdetest']
        );
    }

    public function testGetInputTextWithAttributesWithClassWithoutTextLabel()
    {
        $this->builder->setInput('text', 'nomdetest', ['class' => 'class-de-test', 'size' => '40', 'height' => '450']);
        $this->assertEquals(
            '<div class="form-group"><label for="nomdetest">Nomdetest</label><input type="text" '
            . 'class="form-control class-de-test" name="nomdetest" id="nomdetest" size="40" height="450" /></div>',
            $this->builder->elements['nomdetest']
        );
    }

    public function testGetInputTextWithAttributesWithTextLabel()
    {
        $this->builder->setInput('text', 'nomdetest', ['size' => '40', 'height' => '450'], 'text de label de test');
        $this->assertEquals(
            '<div class="form-group"><label for="nomdetest">text de label de test</label><input type="text" '
            . 'class="form-control " name="nomdetest" id="nomdetest" size="40" height="450" /></div>',
            $this->builder->elements['nomdetest']
        );
    }

    /**
     * Fonction setInput type hidden
     */
    public function testGetInputHiddenWithoutAttributes()
    {
        $this->builder->setInput('hidden', 'nomdetest');
        $this->assertEquals('<input type="hidden" name="nomdetest" />', $this->builder->elements['nomdetest']);
    }

    public function testGetInputHiddenWithAttributes()
    {
        $this->builder->setInput('hidden', 'nomdetest', ['value' => 'valeur de test', 'width' => '450']);
        $this->assertEquals('<input type="hidden" name="nomdetest" value="valeur de test" width="450" />', $this->builder->elements['nomdetest']);
    }

    /**
     * Fonction setInput type file
     */
    public function testGetInputFileWithAttributesWithTextLabel()
    {
        $this->builder->setInput('file', 'nomdetest', ['size' => '40', 'height' => '450'], 'text de label de test');
        $this->assertEquals(
            '<div class="form-group"><label for="nomdetest">text de label de test</label><input type="file" '
            . 'class="input-file " name="nomdetest" id="nomdetest" size="40" height="450" /></div>',
            $this->builder->elements['nomdetest']
        );
    }

    public function testSetButtonWithoutAttributes()
    {
        $this->builder->setButton('submit', 'nomdetest', 'bouton de test');
        $this->assertEquals(
            '<div class="form-group"><button type="submit" '
            . 'class="btn btn-primary" name="nomdetest" >bouton de test</button>'
            . '</div>',
            $this->builder->elements['nomdetest']
        );
    }

    /**
     * Fonction setButton
     */
    public function testSetButtonWithClassAttributes()
    {
        $this->builder->setButton('submit', 'nomdetest', 'bouton de test', ['class' => 'btn btn-success']);
        $this->assertEquals(
            '<div class="form-group"><button type="submit" '
            . 'class="btn btn-success" name="nomdetest" >bouton de test</button>'
            . '</div>',
            $this->builder->elements['nomdetest']
        );
    }

    public function testSetButtonWithClassAndOtherAttributes()
    {
        $this->builder->setButton('submit', 'nomdetest', 'bouton de test', ['class' => 'btn btn-success', 'width' => '350', 'height' => '500']);
        $this->assertEquals(
            '<div class="form-group"><button type="submit" '
            . 'class="btn btn-success" name="nomdetest" width="350" height="500" >bouton de test</button>'
            . '</div>',
            $this->builder->elements['nomdetest']
        );
    }

    public function testSetButtonWithoutClassWithOtherAttributes()
    {
        $this->builder->setButton('submit', 'nomdetest', 'bouton de test', ['width' => '350', 'height' => '500']);
        $this->assertEquals(
            '<div class="form-group"><button type="submit" '
            . 'class="btn btn-primary" name="nomdetest" width="350" height="500" >bouton de test</button>'
            . '</div>',
            $this->builder->elements['nomdetest']
        );
    }

    /**
     * Fonction setTextArea
     */
    public function testSetTextAreaWithoutAttributesTextLabelAndContent()
    {
        $this->builder->setTextArea('10', 'nomdetest');
        $this->assertEquals(
            '<div class="form-group"><label for="nomdetest">Nomdetest</label><textarea rows="10" '
            . 'class="form-control " name="nomdetest" ></textarea></div>',
            $this->builder->elements['nomdetest']
        );
    }

    public function testSetTextAreaWithAttributesWithoutTextLabelAndContent()
    {
        $this->builder->setTextArea('10', 'nomdetest', ['width' => '350', 'height' => '500']);
        $this->assertEquals(
            '<div class="form-group"><label for="nomdetest">Nomdetest</label><textarea rows="10" '
            . 'class="form-control " name="nomdetest" width="350" height="500" ></textarea></div>',
            $this->builder->elements['nomdetest']
        );
    }

    public function testSetTextAreaWithAttributesTextLabelWithoutContent()
    {
        $this->builder->setTextArea('10', 'nomdetest', ['width' => '350', 'height' => '500'], 'label de test');
        $this->assertEquals(
            '<div class="form-group"><label for="nomdetest">label de test</label><textarea rows="10" '
            . 'class="form-control " name="nomdetest" width="350" height="500" ></textarea></div>',
            $this->builder->elements['nomdetest']
        );
    }

    public function testSetTextAreaWithAttributesTextLabelContent()
    {
        $this->builder->setTextArea('10', 'nomdetest', ['width' => '350', 'height' => '500'], 'label de test', 'contenu de test');
        $this->assertEquals(
            '<div class="form-group"><label for="nomdetest">label de test</label><textarea rows="10" '
            . 'class="form-control " name="nomdetest" width="350" height="500" >contenu de test</textarea></div>',
            $this->builder->elements['nomdetest']
        );
    }
}
