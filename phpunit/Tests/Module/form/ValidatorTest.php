<?php
/**
 * @package   ImpressPages
 *
 *
 */

class ValidatorTest extends \PhpUnit\GeneralTestCase
{

    public function testEmail()
    {
        $emailValidator = new \Ip\Form\Validator\Email();

        $result = $emailValidator->validate(array('fieldKey' => 'not-an-email'), 'fieldKey', \Ip\Form::ENVIRONMENT_ADMIN);
        $this->assertEquals('Please enter a valid email address.', $result);

        $result = $emailValidator->validate(array('fieldKey' => 'correct@email.com'), 'fieldKey', \Ip\Form::ENVIRONMENT_ADMIN);
        $this->assertEquals(false, $result);


        $result = $emailValidator->validate(array('fieldKey' => ''), 'fieldKey', \IP\Form::ENVIRONMENT_ADMIN);
        $this->assertEquals(false, $result);

    }



    public function testFile()
    {
        $fileField = new \Ip\Form\Field\File(array());
        $result = $fileField->validate(array('fieldKey' => array('file' => array('unexisting'))), 'fieldKey', \Ip\Form::ENVIRONMENT_ADMIN);
        $this->assertEquals('Session has ended. Please remove and re-upload files.', $result);
    }

}
