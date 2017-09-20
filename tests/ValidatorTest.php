<?php

use Itval\core\Classes\Validator;
use PHPUnit\Framework\TestCase;

require_once 'components.php';

class ValidatorTest extends TestCase
{

    public function testValidatorConstructor()
    {
        $values = [
            "nom" => "Doe",
            "prenom" => "John",
            "email" => "john.doe@gmail.com"
        ];
        $validator = new Validator($values);
        $this->assertEquals($values, $validator->getValues());
    }

    public function testIsValidEmailWithValidEmail()
    {
        $validator = new Validator(['email' => 'emaildetest@hotmail.fr']);
        $validator->isValidEmail('email');
        $this->assertCount(0, $validator->getErrors());
    }

    public function testIsValidEmailWithValidEmailAndValidConfirmation()
    {
        $validator = new Validator(['email' => 'emaildetest@hotmail.fr', 'confirm_email' => 'emaildetest@hotmail.fr']);
        $validator->isValidEmail('email', false, 'confirm_email');
        $this->assertCount(0, $validator->getErrors());
    }

    public function testIsValidEmailWithValidEmailAndInvalidConfirmation()
    {
        $validator = new Validator(['email' => 'emaildetest@hotmail.fr', 'confirm_email' => 'emailsetest@hotmail.fr']);
        $validator->isValidEmail('email', false, 'confirm_email');
        $this->assertCount(1, $validator->getErrors());
        $this->assertEquals("La confirmation de l'email ne correspond pas", current($validator->getErrors()));
    }

    public function testIsValidEmailWithInvalidEmail()
    {
        $validator = new Validator(['email' => 'testtest.fr', 'email2' => 'test@testfr', 'email3' => '@test.fr']);
        $validator->isValidEmail('email');
        $validator->isValidEmail('email2');
        $validator->isValidEmail('email3');
        $this->assertCount(3, $validator->getErrors());
        $this->assertEquals("Le champ email n'est pas valide", $validator->getErrors()[0]);
        $this->assertEquals("Le champ email2 n'est pas valide", $validator->getErrors()[1]);
        $this->assertEquals("Le champ email3 n'est pas valide", $validator->getErrors()[2]);
    }

    public function testIsValidEmailWithRequiredAndEmptyValue()
    {
        $validator = new Validator(['email' => '']);
        $validator->isValidEmail('email', true);
        $this->assertCount(1, $validator->getErrors());
        $this->assertEquals("Le champ email est requis", current($validator->getErrors()));
    }

    public function testIsValidStringWithValidValue()
    {
        $validator = new Validator(['nom' => 'Doe', 'username' => 'jdoe59']);
        $validator->isValidString('nom', ALPHABETIC);
        $validator->isValidString('username', USERNAME);
        $this->assertCount(0, $validator->getErrors());
    }

    public function testIsValidStringWithInvalidValue()
    {
        $validator = new Validator(['nom' => 'Doe_john', 'username' => 'j doe']);
        $validator->isValidString('nom', ALPHABETIC);
        $validator->isValidString('username', USERNAME);
        $this->assertCount(2, $validator->getErrors());
        $this->assertEquals("Le champ nom n'est pas valide", $validator->getErrors()[0]);
        $this->assertEquals("Le champ username n'est pas valide", $validator->getErrors()[1]);
    }

    public function testIsValidStringWithRequiredAndEmptyValue()
    {
        $validator = new Validator(['nom' => '', 'username' => '']);
        $validator->isValidString('nom', ALPHABETIC, true);
        $validator->isValidString('username', USERNAME, true);
        $this->assertCount(2, $validator->getErrors());
        $this->assertEquals("Le champ nom est requis", $validator->getErrors()[0]);
        $this->assertEquals("Le champ username est requis", $validator->getErrors()[1]);
    }

    public function testIsValidStringWithRequiredAndInvalidValue()
    {
        $validator = new Validator(['nom' => 'ertkj-sdf45', 'username' => 'sdfsdf03']);
        $validator->isValidString('nom', ALPHABETIC, true);
        $validator->isValidString('username', ALPHABETIC, true);
        $this->assertCount(2, $validator->getErrors());
        $this->assertEquals("Le champ nom n'est pas valide", $validator->getErrors()[0]);
        $this->assertEquals("Le champ username n'est pas valide", $validator->getErrors()[1]);
    }

    public function testIsValidIntWithValidValue()
    {
        $validator = new Validator(['id' => 1]);
        $validator->isValidInt('id');
        $this->assertCount(0, $validator->getErrors());
    }

    public function testIsValidIntWithInvalidValue()
    {
        $validator = new Validator(['id' => 1.2, 'id2' => '2em']);
        $validator->isValidInt('id');
        $validator->isValidInt('id2');
        $this->assertCount(2, $validator->getErrors());
        $this->assertEquals("Le champ id n'est pas valide", $validator->getErrors()[0]);
        $this->assertEquals("Le champ id2 n'est pas valide", $validator->getErrors()[1]);
    }

    public function testIsValidIntWithRequiredAndEmptyValue()
    {
        $validator = new Validator(['id' => '']);
        $validator->isValidFloat('id', true);
        $this->assertCount(1, $validator->getErrors());
        $this->assertEquals("Le champ id est requis", $validator->getErrors()[0]);
    }

    public function testIsValidFloatWithValidValue()
    {
        $validator = new Validator(['id' => 1.2]);
        $validator->isValidFloat('id');
        $this->assertCount(0, $validator->getErrors());
    }

    public function testIsValidFloatWithInvalidValue()
    {
        $validator = new Validator(['id' => 1, 'id2' => '2em']);
        $validator->isValidFloat('id');
        $validator->isValidFloat('id2');
        $this->assertCount(2, $validator->getErrors());
        $this->assertEquals("Le champ id n'est pas valide", $validator->getErrors()[0]);
        $this->assertEquals("Le champ id2 n'est pas valide", $validator->getErrors()[1]);
    }

    public function testIsValidFloatWithRequiredAndEmptyValue()
    {
        $validator = new Validator(['id' => '']);
        $validator->isValidInt('id', true);
        $this->assertCount(1, $validator->getErrors());
        $this->assertEquals("Le champ id est requis", $validator->getErrors()[0]);
    }

    public function testIsValidStringSlug()
    {
        $validator = new Validator(
            [
                'slug1' => 'slug-de-test',
                'slug2' => 'slug-de-test-2',
                'slug3' => 'slug-2-test',
                'slug4' => 'slug--de-test',
                'slug5' => '--slug--de-test',
                'slug6' => '23-slug-de-test',
                'slug7' => '23',
                'slug8' => 'test23-slug-de-test',
                'slug9' => 'slug-de-test-',
                'slug10' => 'slug'
            ]
        );
        $validator->isValidString('slug1', SLUG);
        $validator->isValidString('slug2', SLUG);
        $validator->isValidString('slug3', SLUG);
        $validator->isValidString('slug4', SLUG);
        $validator->isValidString('slug5', SLUG);
        $validator->isValidString('slug6', SLUG);
        $validator->isValidString('slug7', SLUG);
        $validator->isValidString('slug8', SLUG);
        $validator->isValidString('slug9', SLUG);
        $validator->isValidString('slug10', SLUG);
        $this->assertCount(6, $validator->getErrors());
    }

    public function testIsValidStringPhrase()
    {
        $validator = new Validator(
            [
                'phrase1' => 'Ceci est une phrase simple.',
                'phrase2' => 'Ceci est une phrase, avec une virgule.',
                'phrase3' => 'Ceci est une phrase, avec une virgule et un point d\'intérogation ?',
                'phrase4' => 'Ceci est une phrase, avec une virgule et un point d\'exclamation !',
                'phrase5' => 'Ceci est une phrase, avec une virgule et un point d\'exclamation mal positionné!',
                'phrase6' => 'Ceci est une phrase, avec une virgule et un point virgule ; sans point à la fin',
                'phrase7' => 'Ceci est une phrase, avec une virgule et un point virgule ; avec point à la fin.',
                'phrase8' => 'Lorem ipsum dolor sit amet, consectetur : adipisicing elit ! Dicta dolor eius ; explicabo illo ipsam modi nemo, nesciunt quae. C\'orporis est facilis quis ullam voluptate, voluptatum ? Aliquid doloribus minima nam sed.',
                'phrase9' => 'Ceci est une phrase, avec une virgule et un point virgule ; avec point d\'intérogation collé à la fin?',
                'phrase10' => 'Ceci est une phrase, avec une virgule et un point virgule ; avec point d\'exclamation collé à la fin!'
            ]
        );
        $validator->isValidString('phrase1', TEXT);
        $validator->isValidString('phrase2', TEXT);
        $validator->isValidString('phrase3', TEXT);
        $validator->isValidString('phrase4', TEXT);
        $validator->isValidString('phrase5', TEXT);
        $validator->isValidString('phrase6', TEXT);
        $validator->isValidString('phrase7', TEXT);
        $validator->isValidString('phrase8', TEXT);
        $validator->isValidString('phrase9', TEXT);
        $validator->isValidString('phrase10', TEXT);
        $this->assertCount(4, $validator->getErrors());
    }

    public function testIsValidStringAlphanumeric()
    {
        $validator = new Validator(
            [
                'username1' => 'username593',
                'username2' => 'Username593',
                'username3' => 'Username-593'
            ]
        );
        $validator->isValidString('username1', ALPHANUMERIC);
        $validator->isValidString('username2', ALPHANUMERIC);
        $validator->isValidString('username3', ALPHANUMERIC);
        $this->assertCount(1, $validator->getErrors());
    }

    public function testIsValidStringPhoneNumberWithCorrectValue()
    {
        $validator = new Validator(
            [
                'phone1' => '0107192836',
                'phone2' => '0207192836',
                'phone3' => '0307192836',
                'phone4' => '0407192836',
                'phone5' => '0507192836',
                'phone6' => '0607192836',
                'phone7' => '0707192836',
                'phone8' => '0807192836',
                'phone9' => '0907192836'
            ]
        );
        $validator->isValidString('phone1', PHONE_NUMBER);
        $validator->isValidString('phone2', PHONE_NUMBER);
        $validator->isValidString('phone3', PHONE_NUMBER);
        $validator->isValidString('phone4', PHONE_NUMBER);
        $validator->isValidString('phone5', PHONE_NUMBER);
        $validator->isValidString('phone6', PHONE_NUMBER);
        $validator->isValidString('phone7', PHONE_NUMBER);
        $validator->isValidString('phone8', PHONE_NUMBER);
        $validator->isValidString('phone9', PHONE_NUMBER);
        $this->assertCount(0, $validator->getErrors());
    }

    public function testIsValidStringPhoneNumberWithoutCorrectValue()
    {
        $validator = new Validator(
            [
                'phone1' => '1007192836',
                'phone2' => '2007192836',
                'phone3' => '5007192836',
                'phone4' => '0107192',
                'phone5' => '010719234058'
            ]
        );
        $validator->isValidString('phone1', PHONE_NUMBER);
        $validator->isValidString('phone2', PHONE_NUMBER);
        $validator->isValidString('phone3', PHONE_NUMBER);
        $validator->isValidString('phone4', PHONE_NUMBER);
        $validator->isValidString('phone5', PHONE_NUMBER);
        $this->assertCount(5, $validator->getErrors());
    }
}
