<?php
/**
 * @package   ImpressPages
 *
 *
 */
namespace Ip\Internal\Languages;



class AdminController extends \Ip\GridController
{
    static $urlBeforeUpdate;

    public function index()
    {
        ipAddJs('Ip/Internal/Languages/assets/languages.js');
        $response = parent::index() . $this->helperHtml();
        return $response ;
    }


    protected function helperHtml()
    {

        $helperData = array(
            'addForm' => $form = Helper::getAddForm()
        );
        return ipView('view/helperHtml.php', $helperData)->render();
    }




    protected function config()
    {

        $languages = ipContent()->getLanguages();
        $languageUrls = array();
        foreach($languages as $language) {
            $languageUrls[] = $language->getUrl();
        }

        $reservedDirs = ipGetOption('Config.reservedDirs');
        if (is_array($reservedDirs)) {
            $languageUrls = array_merge($languageUrls, $reservedDirs);
        }

        return array(
            'type' => 'table',
            'table' => 'language',
            'allowCreate' => false,
            'allowSearch' => false,
            'actions' => array(
                array(
                    'label' => __('Add', 'ipAdmin', false),
                    'class' => 'ipsCustomAdd'
                )
            ),
            'preventAction' => array($this, 'preventAction'),
            'beforeUpdate' => array($this, 'beforeUpdate'),
            'afterUpdate' => array($this, 'afterUpdate'),
            'beforeDelete' => array($this, 'beforeDelete'),
            'deleteWarning' => 'Are you sure you want to delete? All pages and other language related content will be lost forever!',
            'sortField' => 'languageOrder',
            'fields' => array(
                array(
                    'label' => __('Title', 'ipAdmin', false),
                    'field' => 'title',
                ),
                array(
                    'label' => __('Abbreviation', 'ipAdmin', false),
                    'field' => 'abbreviation',
                    'showInList' => true
                ),
                array(
                    'type' => 'Checkbox',
                    'label' => __('Visible', 'ipAdmin', false),
                    'field' => 'isVisible'
                ),
                array(
                    'label' => __('Url', 'ipAdmin', false),
                    'field' => 'url',
                    'showInList' => false,
                    'validators' => array(
                        array('Regex', '/^([^\/\\\])+$/', __('You can\'t use slash in URL.', 'ipAdmin', FALSE)),
                        array('Unique', array('table' => 'language', 'allowEmpty' => true), __('Language url should be unique', 'ipAdmin', false)),
                    )
                ),
                array(
                    'label' => __('RFC 4646 code', 'ipAdmin', false),
                    'field' => 'code',
                    'showInList' => false
                ),
                array(
                    'type' => 'Select',
                    'label' => __('Text direction', 'ipAdmin', false),
                    'field' => 'textDirection',
                    'showInList' => false,
                    'values' => array(
                        array('ltr', __('Left To Right', 'ipAdmin', false)),
                        array('rtl', __('Right To Left', 'ipAdmin', false))
                    )
                ),
            )
        );
    }


    public function addLanguage()
    {
        ipRequest()->mustBePost();
        $data = ipRequest()->getPost();
        if (empty($data['code'])) {
            throw new \Ip\Exception('Missing required parameter');
        }
        $code = $data['code'];
        $abbreviation = strtoupper($code);
        $url = $code;

        $languages = ipContent()->getLanguages();
        foreach($languages as $language) {
            if ($language->getCode() == $code) {
                return new \Ip\Response\Json(array(
                    'error' => 1,
                    'errorMessage' => __('This language already exist.', 'ipAdmin', FALSE)
                ));
            }
        }

        $languages = Fixture::languageList();

        if (!empty($languages[$code])) {
            $language = $languages[$code];
            $title = $language['nativeName'];
        } else {
            $title = $code;
        }

        Service::addLanguage($title, $abbreviation, $code, $url, 1, Service::TEXT_DIRECTION_LTR);

        return new \Ip\Response\Json(array());
    }

    public function preventAction($method, $params, $statusVariables)
    {
        if ($method === 'delete') {
            $languages = ipContent()->getLanguages();
            if (count($languages) === 1) {
                return __('Can\'t delete the last language.', 'ipAdmin', false);
            }
        }
    }

    public function beforeDelete($id)
    {
        Service::delete($id);
    }

    public function beforeUpdate($id, $newData)
    {



//        /**
//         * TODOXX check zone and language url's if they don't match system folder #139
//         * Beginning of page URL can conflict with system/core folders. This function checks if the folder can be used in URL beginning.
//         *
//         * @param $folderName
//         * @return bool true if URL is reserved for framework core
//         *
//         */
//        public function usedUrl($folderName)
//    {
//        $systemDirs = array();
//        // TODOXX make it smart with overriden paths #139
//        $systemDirs['Plugin'] = 1;
//        $systemDirs['Theme'] = 1;
//        $systemDirs['File'] = 1;
//        $systemDirs['install'] = 1;
//        $systemDirs['update'] = 1;
//        if(isset($systemDirs[$folderName])){
//            return true;
//        } else {
//            return false;
//        }
//    }

        $tmpLanguage = Db::getLanguageById($id);
        self::$urlBeforeUpdate = $tmpLanguage['url'];
    }

    public function afterUpdate($id, $newData)
    {
        $tmpLanguage = Db::getLanguageById($id);
        if($tmpLanguage['url'] != self::$urlBeforeUpdate && ipGetOption('Config.multilingual')) {
            $oldUrl = ipConfig()->baseUrl() . self::$urlBeforeUpdate.'/';
            $newUrl = ipConfig()->baseUrl() . $tmpLanguage['url'].'/';
            ipEvent('ipUrlChanged', array('oldUrl' => $oldUrl, 'newUrl' => $newUrl));
            $oldUrl = ipConfig()->baseUrl() . 'index.php/' . self::$urlBeforeUpdate.'/';
            $newUrl = ipConfig()->baseUrl() . 'index.php/' . $tmpLanguage['url'].'/';
            ipEvent('ipUrlChanged', array('oldUrl' => $oldUrl, 'newUrl' => $newUrl));
        }
    }

}
