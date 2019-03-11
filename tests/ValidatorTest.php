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
        $this->assertEquals(0, $validator->getErrors());
    }

    public function testIsValidEmailWithValidEmailAndValidConfirmation()
    {
        $validator = new Validator(['email' => 'emaildetest@hotmail.fr', 'confirm_email' => 'emaildetest@hotmail.fr']);
        $validator->isValidEmail('email', false, 'confirm_email');
        $this->assertEquals(0, $validator->getErrors());
    }

    public function testIsValidEmailWithValidEmailAndInvalidConfirmation()
    {
        $validator = new Validator(['email' => 'emaildetest@hotmail.fr', 'confirm_email' => 'emailsetest@hotmail.fr']);
        $validator->isValidEmail('email', false, 'confirm_email');
        $this->assertEquals(1, $validator->getErrors());
        $this->assertEquals(' * La confirmation de l\'email ne correspond pas', $_SESSION['validator_error_confirm_email']);
    }

    public function testIsValidEmailWithInvalidEmail()
    {
        $validator = new Validator([
            'email' => 'testtest.fr',
            'email2' => 'test@testfr',
            'email3' => '@test.fr',
            'email4' => 'baltazar@test.fr',
            'email5' => 'baltazar@gmail.con']);
        $validator->isValidEmail('email');
        $validator->isValidEmail('email2');
        $validator->isValidEmail('email3');
        $validator->isValidEmail('email4');
        $validator->isValidEmail('email5');
        $this->assertEquals(4, $validator->getErrors());
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_email']);
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_email2']);
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_email3']);
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_email5']);
    }

    public function testIsValidEmailWithRequiredAndEmptyValue()
    {
        $validator = new Validator(['email' => '']);
        $validator->isValidEmail('email', true);
        $this->assertEquals(1, $validator->getErrors());
        $this->assertEquals(' * Le champ est requis', $_SESSION['validator_error_email']);
    }

    public function testIsValidStringWithValidValue()
    {
        $validator = new Validator(['nom' => 'Doe', 'username' => 'jdoe59']);
        $validator->isValidString('nom', DENOMINATION);
        $validator->isValidString('username', USERNAME);
        $this->assertEquals(0, $validator->getErrors());
    }

    public function testIsValidStringWithInvalidValue()
    {
        $validator = new Validator(['nom' => 'Doe_john', 'username' => 'j doe']);
        $validator->isValidString('nom', DENOMINATION);
        $validator->isValidString('username', USERNAME);
        $this->assertEquals(2, $validator->getErrors());
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_nom']);
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_username']);
    }

    public function testIsValidStringWithRequiredAndEmptyValue()
    {
        $validator = new Validator(['nom' => '', 'username' => '']);
        $validator->isValidString('nom', DENOMINATION, true);
        $validator->isValidString('username', USERNAME, true);
        $this->assertEquals(2, $validator->getErrors());
        $this->assertEquals(' * Le champ est requis', $_SESSION['validator_error_nom']);
        $this->assertEquals(' * Le champ est requis', $_SESSION['validator_error_username']);
    }

    public function testIsValidStringWithRequiredAndInvalidValue()
    {
        $validator = new Validator(['nom' => 'ertkj,sdf45', 'username' => 'sdfsdf_03']);
        $validator->isValidString('nom', DENOMINATION, true);
        $validator->isValidString('username', DENOMINATION, true);
        $this->assertEquals(2, $validator->getErrors());
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_nom']);
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_username']);
    }

    public function testIsValidIntWithValidValue()
    {
        $validator = new Validator(['id' => 1]);
        $validator->isValidInt('id');
        $this->assertEquals(0, $validator->getErrors());
    }

    public function testIsValidIntWithInvalidValue()
    {
        $validator = new Validator(['id' => 1.2, 'id2' => '2em']);
        $validator->isValidInt('id');
        $validator->isValidInt('id2');
        $this->assertEquals(2, $validator->getErrors());
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_id']);
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_id2']);
    }

    public function testIsValidIntWithRequiredAndEmptyValue()
    {
        $validator = new Validator(['id' => '']);
        $validator->isValidFloat('id', true);
        $this->assertEquals(1, $validator->getErrors());
        $this->assertEquals(' * Le champ est requis', $_SESSION['validator_error_id']);
    }

    public function testIsValidFloatWithValidValue()
    {
        $validator = new Validator(['id' => 1.2]);
        $validator->isValidFloat('id');
        $this->assertEquals(0, $validator->getErrors());
    }

    public function testIsValidFloatWithInvalidValue()
    {
        $validator = new Validator(['id' => 1, 'id2' => '2em']);
        $validator->isValidFloat('id');
        $validator->isValidFloat('id2');
        $this->assertEquals(2, $validator->getErrors());
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_id']);
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_id2']);
    }

    public function testIsValidFloatWithRequiredAndEmptyValue()
    {
        $validator = new Validator(['id' => '']);
        $validator->isValidInt('id', true);
        $this->assertEquals(1, $validator->getErrors());
        $this->assertEquals(' * Le champ est requis', $_SESSION['validator_error_id']);
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
        $this->assertEquals(6, $validator->getErrors());
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_slug4']);
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_slug5']);
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_slug6']);
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_slug7']);
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_slug8']);
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_slug9']);
    }

    public function testIsValidStringMinSize()
    {
        $validator = new Validator(
            [
            'username1' => 'usee593',
            'username2' => 'Urne-59300',
            'username3' => 'Username59300',
            'username4' => 'Username-59300'
            ]
        );
        $validator->isValidString('username1', USERNAME, false, null, 3);
        $validator->isValidString('username2', USERNAME, false, null, 5);
        $validator->isValidString('username3', USERNAME, false, null, 20);
        $validator->isValidString('username4', USERNAME, false, null, 30);
        $this->assertEquals(3, $validator->getErrors());
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_username2']);
        $this->assertEquals(' * La valeur entrée est trop courte : minimum 20 caractère(s)', $_SESSION['validator_error_username3']);
        $this->assertEquals(' * La valeur entrée est trop courte : minimum 30 caractère(s)', $_SESSION['validator_error_username4']);
    }

    public function testIsValidStringMaxSize()
    {
        $validator = new Validator(
            [
            'username1' => 'usee593',
            'username2' => 'Urne-59300',
            'username3' => 'Username59300',
            'username4' => 'Username-59300'
            ]
        );
        $validator->isValidString('username1', ALPHANUMERIC, false, null, null, 10);
        $validator->isValidString('username2', ALPHANUMERIC, false, null, null, 10);
        $validator->isValidString('username3', ALPHANUMERIC, false, null, null, 10);
        $validator->isValidString('username4', ALPHANUMERIC, false, null, null, 12);
        $this->assertEquals(3, $validator->getErrors());
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_username2']);
        $this->assertEquals(' * La valeur entrée est trop longue : maximum 10 caractère(s)', $_SESSION['validator_error_username3']);
        $this->assertEquals(' * La valeur entrée est trop longue : maximum 12 caractère(s)', $_SESSION['validator_error_username4']);
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
        $this->assertEquals(1, $validator->getErrors());
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_username3']);
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
        $this->assertEquals(0, $validator->getErrors());
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
        $this->assertEquals(5, $validator->getErrors());
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_phone1']);
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_phone2']);
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_phone3']);
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_phone4']);
        $this->assertEquals(' * La valeur entrée n\'est pas valide', $_SESSION['validator_error_phone5']);
    }
}
