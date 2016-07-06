<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Config;


class AdminController extends \Ip\Controller
{

    public function index()
    {


        ipAddJs('Ip/Internal/Config/assets/config.js');
        ipAddCss('Ip/Internal/Config/assets/config.css');

        $form = Forms::getForm();
        $advancedForm = false;
        if (ipAdminPermission('Config advanced')) {
            $advancedForm = Forms::getAdvancedForm();
        }
        $data = array(
            'form' => $form,
            'advancedForm' => $advancedForm
        );
        return ipView('view/configWindow.php', $data)->render();

    }


    public function saveValue()
    {
        $request = \Ip\ServiceLocator::request();

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

        if (
            !in_array($fieldName, array('websiteTitle', 'websiteEmail', 'gmapsApiKey'))
            &&
            !(
                in_array($fieldName, array('automaticCron', 'cronPassword', 'removeOldRevisions', 'removeOldRevisionsDays', 'removeOldEmails', 'removeOldEmailsDays', 'allowAnonymousUploads', 'trailingSlash'))
                &&
                ipAdminPermission('Config advanced')
            )
        ) {
            throw new \Exception('Unknown config value');
        }




        $emailValidator = new \Ip\Form\Validator\Email();
        $error = $emailValidator->getError(array('value' => $value), 'value', \Ip\Form::ENVIRONMENT_ADMIN);
        if ($fieldName === 'websiteEmail' && $error !== false) {
            return $this->returnError($error);
        }


        if (in_array($fieldName, array('websiteTitle', 'websiteEmail'))) {
            if (!isset($post['languageId'])) {
                throw new \Exception('Missing required parameter');
            }
            $languageId = $post['languageId'];
            $language = ipContent()->getLanguage($languageId);
            ipSetOptionLang('Config.' . $fieldName, $value, $language->getCode());
        } else {
            ipSetOption('Config.' . $fieldName, $value);
        }


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
