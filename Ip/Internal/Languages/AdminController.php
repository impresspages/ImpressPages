<?php
/**
 * @package   ImpressPages
 *
 *
 */
namespace Ip\Internal\Languages;



class AdminController extends \Ip\Grid\Controller
{
    public function init()
    {
        ipAddJs(ipFileUrl('Ip/Internal/Languages/assets/languages.js'));
    }


    public function index()
    {
        $response = parent::index() . $this->helperHtml();
        return $response ;
    }


    protected function helperHtml()
    {

        $helperData = array(
            'addForm' => $form = Helper::getAddForm()
        );
        return \Ip\View::create('view/helperHtml.php', $helperData)->render();
    }


    public function addLanguage()
    {
        ipRequest()->mustBePost();
        $data = ipRequest()->getPost();
        if (empty($data['code'])) {
            throw new \Ip\CoreException('Missing required parameter');
        }
        $code = $data['code'];
        $abbreviation = $code;
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


    protected function config()
    {
        return array(
            'type' => 'table',
            'table' => 'language',
            'allowInsert' => false,
            'allowSearch' => false,
            'pageSize' => 3,
            'actions' => array(
                array(
                    'label' => __('Add', 'ipAdmin', false),
                    'class' => 'ipsCustomAdd'
                )
            ),
            'preventAction' => array($this, 'preventAction'),
            'beforeDelete' => array($this, 'beforeDelete'),
            'deleteWarning' => 'Are you sure you want to delete? All pages and other language related content will be lost forever!',
            'fields' => array(
                array(
                    'label' => __('Title', 'ipAdmin', false),
                    'field' => 'd_long',
                ),
                array(
                    'label' => __('Abbreviation', 'ipAdmin', false),
                    'field' => 'd_short',
                    'showInList' => true
                ),
                array(
                    'type' => 'Checkbox',
                    'label' => __('Visible', 'ipAdmin', false),
                    'field' => 'visible'
                ),
                array(
                    'label' => __('Url', 'ipAdmin', false),
                    'field' => 'url',
                    'showInList' => false

                    /*
                    //TODOX add URL validator
                    'regExpression' => '/^([^\/\\\])+$/',
                    'regExpressionError' => __('Incorrect URL. You can\'t use slash in URL.', 'ipAdmin')
                    */
                ),
                array(
                    'label' => __('RFC 4646 code', 'ipAdmin', false),
                    'field' => 'code',
                    'showInList' => false
                ),
                array(
                    'type' => 'Select',
                    'label' => __('Text direction', 'ipAdmin', false),
                    'field' => 'text_direction',
                    'showInList' => false,
                    'values' => array(
                        array('ltr', __('Left To Right', 'ipAdmin', false)),
                        array('rtl', __('Right To Left', 'ipAdmin', false))
                    )
                ),
            )
        );
    }

    public function preventAction($method, $params, $statusVariables)
    {
        if ($method === 'delete') {
            $languages = ipContent()->getLanguages();
            if (count($languages) === 1) {
                return __('Can\'t delete last language.', 'ipAdmin', false);
            }
        }
    }

    public function beforeDelete($id)
    {
        Service::delete($id);
    }

}
