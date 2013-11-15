<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Module\Config;




class AdminController extends \Ip\Controller{

    public function index()
    {

        \Ip\ServiceLocator::getSite()->addJavascript(\Ip\Config::coreModuleUrl('Config/public/config.js'));

        $form = Forms::getForm();
        $data = array (
            'form' => $form
        );
        return \Ip\View::create('view/configWindow.php', $data)->render();

    }


    public function saveValue()
    {
        $request = \Ip\ServiceLocator::getRequest();

        $request->mustBePost();

        $post = $request->getPost();
        if (empty($post['fieldName'])) {
            throw new \Exception('Missing required parameter');
        }
        $fieldName = $post['fieldName'];
        if (!isset($post['value'])) {
            throw new \Exception('Missing required parameter');
        }
        $value = $post['value'];

        if (!in_array($fieldName, array('automaticCron', 'keepOldRevision', 'websiteTitle', 'websiteEmail'))) {
            throw new \Exception('Unknown config value');
        }

        $emailValidator = new \Ip\Form\Validator\Email();
        if ($fieldName === 'websiteEmail' && $emailValidator->validate(array('value' => $value), 'value') !== false) {
            $this->returnError("Invalid value");
            return;
        }

        ipSetOption('Config.' . $fieldName, $value);


        return new \Ip\Response\Json(array(1));

    }

    private function returnError($errorMessage)
    {
        $data = array(
            'error' => $errorMessage
        );
        return new \Ip\Response\Json($data);
    }
}