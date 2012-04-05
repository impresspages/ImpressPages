<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\standard\configuration;
if (!defined('CMS')) exit;


class System{


    public function init() {
        global $site;
        

        $revision = $site->getRevision();
        $data = array (
            'ipBaseUrl' => BASE_URL,
            'ipLibraryDir' => LIBRARY_DIR,
            'ipThemeDir' => THEME_DIR,
            'ipModuleDir' => MODULE_DIR,
            'ipTheme' => THEME,
            'ipLanguageCode' => $site->getCurrentLanguage()->getCode(),
            'ipManagementUrl' => $site->generateUrl(),
            'ipZoneName' => $site->getCurrentZone() ? $site->getCurrentZone()->getName() : '',
            'ipPageId' => $site->getCurrentElement() ? $site->getCurrentElement()->getId() : null,
            'ipRevisionId' => $revision['revisionId']
        );
        $configJs = \Ip\View::create('view/config.php', $data)->render();
        $site->addJavascriptContent('IpConfig', $configJs, 0);

        
        
        if ($site->managementState()) {
            $configJs = '';
            $configJs .= \Ip\View::create('tinymce/paste_preprocess.js')->render();
            $configJs .= \Ip\View::create('tinymce/min.js')->render();
            $configJs .= \Ip\View::create('tinymce/med.js')->render();
            $configJs .= \Ip\View::create('tinymce/max.js')->render();
            $configJs .= \Ip\View::create('tinymce/table.js')->render();
            $site->addJavascriptContent('TinyMceConfig', $configJs, 0);
        };
        
        if (!$site->managementState()) {
            $data = array(
                'languageCode' => $site->getCurrentLanguage()->getCode()
            );
            $configJs = '';
            $configJs .= \Ip\View::create('jquerytools/validator.js', $data)->render();
            $site->addJavascriptContent('ValidatorConfig', $configJs, 0);
        }
        
    }
}