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
        if (ipAdminPermission('Config advanced')) {
            ipAddJs('Ip/Internal/Config/assets/securityHeadersCheck.js');
            ipAddJsVariable('ipSecurityHeadersCheck', array(
                'url' => ipConfig()->baseUrl(),
                'https' => ipRequest()->isHttps(),
                'text' => array(
                    'header' => __('Header', 'Ip-admin', false),
                    'value' => __('Value', 'Ip-admin', false),
                    'status' => __('Status', 'Ip-admin', false),
                    'sent' => __('Sent', 'Ip-admin', false),
                    'testing' => __('Testing (report-only)', 'Ip-admin', false),
                    'needsHttps' => __('Needs HTTPS', 'Ip-admin', false),
                    'missing' => __('Missing', 'Ip-admin', false),
                    'optional' => __('Not set (optional)', 'Ip-admin', false),
                    'hidden' => __('Hidden', 'Ip-admin', false),
                    'revealsPhp' => __('Reveals the PHP version', 'Ip-admin', false),
                    'checking' => __('Checking…', 'Ip-admin', false),
                    'failed' => __('Couldn\'t load the home page to check it.', 'Ip-admin', false),
                ),
            ));
        }
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
            !in_array($fieldName, array_merge(
                array('websiteTitle', 'websiteEmail', 'websiteDescription', 'gmapsApiKey', 'sitemapEnabled', 'llmsTxtEnabled', 'llmsFullTxtEnabled'),
                array('structuredDataEnabled', 'organizationType', 'organizationTelephone', 'organizationSameAs'),
                array_keys(\Ip\Internal\Seo\Model::addressFields()),
                array_map(array('\Ip\Internal\Seo\Model', 'aiCrawlerOption'), array_keys(\Ip\Internal\Seo\Model::aiCrawlers()))
            ))
            &&
            !(
                in_array($fieldName, array('automaticCron', 'cronPassword', 'removeOldRevisions', 'removeOldRevisionsDays', 'removeOldEmails', 'removeOldEmailsDays', 'allowAnonymousUploads', 'trailingSlash',
                    'securityNosniff', 'securityFrameOptions', 'securityReferrerPolicy', 'securityPermissionsPolicy', 'securityHsts', 'securityHstsSubdomains', 'securityCsp', 'securityCspReportOnly', 'securityHidePoweredBy'))
                &&
                ipAdminPermission('Config advanced')
            )
        ) {
            throw new \Exception('Unknown config value');
        }




        if ($fieldName === 'securityFrameOptions' && !array_key_exists((string)$value, \Ip\Internal\Security\Model::frameOptions())
            || $fieldName === 'securityReferrerPolicy' && !array_key_exists((string)$value, \Ip\Internal\Security\Model::referrerPolicies())
        ) {
            throw new \Exception('Unknown config value');
        }

        $emailValidator = new \Ip\Form\Validator\Email();
        $error = $emailValidator->getError(array('value' => $value), 'value', \Ip\Form::ENVIRONMENT_ADMIN);
        if ($fieldName === 'websiteEmail' && $error !== false) {
            return $this->returnError($error);
        }


        if (in_array($fieldName, array('websiteTitle', 'websiteEmail', 'websiteDescription'))) {
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
