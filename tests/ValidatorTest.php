<?php

use PHPUnit\Framework\TestCase;
use Itval\core\Classes\Validator;

require_once 'components.php';

class ValidatorTest extends TestCase
{

    /**
     * isValidEmail
     */
    public function testIsValidEmailWithCorrectEmail()
    {
        $response = Validator::isValidEmail('test@test.fr');
        $this->assertEquals('test@test.fr', $response);
    }

    public function testIsValidEmailWithoutCorrectEmail()
    {
        $response1 = Validator::isValidEmail('testtest.fr');
        $response2 = Validator::isValidEmail('test@testfr');
        $response3 = Validator::isValidEmail('@test.fr');
        $this->assertEquals(false, $response1);
        $this->assertEquals(false, $response2);
        $this->assertEquals(false, $response3);
    }

    /**
     * isValidString
     */
    public function testIsValidStringAlphabeticWithCorrectValue()
    {
        $response = Validator::isValidString(ALPHABETIC, 'Zéèçàùêâôîûäëïöü-zaa bug');
        $this->assertEquals('Zéèçàùêâôîûäëïöü-zaa bug', $response);
    }

    public function testIsValidStringAlphabeticWithNumericValue()
    {
        $response = Validator::isValidString(ALPHABETIC, 1234);
        $this->assertEquals(false, $response);
    }

    public function testIsValidStringAlphabeticWithStringNumericValue()
    {
        $response = Validator::isValidString(ALPHABETIC, '1234');
        $this->assertEquals(false, $response);
    }

    public function testIsValidStringAlphanumericWithCorrectValue()
    {
        $response = Validator::isValidString(ALPHANUMERIC, 'username593');
        $response2 = Validator::isValidString(ALPHANUMERIC, 'Username593');
        $this->assertEquals('username593', $response);
        $this->assertEquals('Username593', $response2);
    }

    public function testIsValidStringAlphanumericWithoutCorrectValue()
    {
        $response = Validator::isValidString(ALPHANUMERIC, 'username 593');
        $this->assertEquals(false, $response);
    }

    public function testIsValidStringPhoneNumberWithCorrectValue()
    {
        $response1 = Validator::isValidString(PHONE_NUMBER, '0107192836');
        $response2 = Validator::isValidString(PHONE_NUMBER, '0207192836');
        $response3 = Validator::isValidString(PHONE_NUMBER, '0307192836');
        $response4 = Validator::isValidString(PHONE_NUMBER, '0407192836');
        $response5 = Validator::isValidString(PHONE_NUMBER, '0507192836');
        $response6 = Validator::isValidString(PHONE_NUMBER, '0607192836');
        $response7 = Validator::isValidString(PHONE_NUMBER, '0707192836');
        $response8 = Validator::isValidString(PHONE_NUMBER, '0807192836');
        $response9 = Validator::isValidString(PHONE_NUMBER, '0907192836');
        $this->assertEquals('0107192836', $response1);
        $this->assertEquals('0207192836', $response2);
        $this->assertEquals('0307192836', $response3);
        $this->assertEquals('0407192836', $response4);
        $this->assertEquals('0507192836', $response5);
        $this->assertEquals('0607192836', $response6);
        $this->assertEquals('0707192836', $response7);
        $this->assertEquals('0807192836', $response8);
        $this->assertEquals('0907192836', $response9);
    }

    public function testIsValidStringPhoneNumberWithoutCorrectValue()
    {
        $response1 = Validator::isValidString(PHONE_NUMBER, '1007192836');
        $response2 = Validator::isValidString(PHONE_NUMBER, '2007192836');
        $response3 = Validator::isValidString(PHONE_NUMBER, '5007192836');
        $response4 = Validator::isValidString(PHONE_NUMBER, '0107192');
        $response5 = Validator::isValidString(PHONE_NUMBER, '010719234058');
        $this->assertEquals(false, $response1);
        $this->assertEquals(false, $response2);
        $this->assertEquals(false, $response3);
        $this->assertEquals(false, $response4);
        $this->assertEquals(false, $response5);
    }

    public function testIsValidStringNumericWithCorrectValue()
    {
        $response1 = Validator::isValidString(NUMERIC, '1007192836');
        $response2 = Validator::isValidString(NUMERIC, '20071007192836192836');
        $response3 = Validator::isValidString(NUMERIC, '50071836');
        $this->assertEquals('1007192836', $response1);
        $this->assertEquals('20071007192836192836', $response2);
        $this->assertEquals('50071836', $response3);
    }

    public function testIsValidStringNumericWithoutCorrectValue()
    {
        $response1 = Validator::isValidString(NUMERIC, '10071 92836');
        $response2 = Validator::isValidString(NUMERIC, '2007100-7192836192836');
        $response3 = Validator::isValidString(NUMERIC, '5007.1836');
        $this->assertEquals(false, $response1);
        $this->assertEquals(false, $response2);
        $this->assertEquals(false, $response3);
    }
}
