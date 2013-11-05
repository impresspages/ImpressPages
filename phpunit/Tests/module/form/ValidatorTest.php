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

        $result = $emailValidator->validate(array('fieldKey' => 'not-an-email'), 'fieldKey');
        $this->assertEquals($result, 'Please enter a valid email address');

        $result = $emailValidator->validate(array('fieldKey' => 'correct@email.com'), 'fieldKey');
        $this->assertEquals($result, false);


        $result = $emailValidator->validate(array('fieldKey' => ''), 'fieldKey');
        $this->assertEquals($result, false);

    }



    public function testFile()
    {
        $fileField = new \Ip\Form\Field\File(array());
        $result = $fileField->validate(array('fieldKey' => array('file' => array('unexisting'))), 'fieldKey');
        $this->assertEquals('Session has ended. Please remove and re-upload files.', $result);
    }

}
