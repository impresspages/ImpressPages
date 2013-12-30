<?php
/**
 * @package   ImpressPages
 *
 *
 */
namespace Ip\Internal\Languages;



class LanguageArea extends \Ip\Lib\StdMod\Area {

    var $errors = array();
    private $urlBeforeUpdate;

    function __construct() {
        parent::__construct(
        array(
            'dbTable' => 'language',
            'title' => __('Languages', 'ipAdmin'),
            'dbPrimaryKey' => 'id',
            'searchable' => false,
            'orderBy' => 'row_number',
            'sortable' => true,
            'sortField' => 'row_number',
            'newRecordPosition' => 'bottom'
            )
            );



            $element = new \Ip\Lib\StdMod\Element\Text(
            array(
                    'title' => __('Short', 'ipAdmin'),
                    'showOnList' => true,
                    'dbField' => 'd_short',
                    'required' => true
            )
            );
            $this->addElement($element);


            $element = new \Ip\Lib\StdMod\Element\Text(
            array(
                    'title' => __('Long', 'ipAdmin'),
                    'useInBreadcrumb' => true,
                    'showOnList' => true,
                    'dbField' => 'd_long',
            )
            );
            $this->addElement($element);

            $element = new \Ip\Lib\StdMod\Element\Bool(
            array(
                    'title' => __('Visible', 'ipAdmin'),
                    'showOnList' => true,
                    'dbField' => 'visible',
            )
            );
            $this->addElement($element);





            $element = new ElementUrl(
            array(
                    'title' => __('URL', 'ipAdmin'),
                    'showOnList' => true,
                    'dbField' => 'url',
                    'required' => true,
                    'regExpression' => '/^([^\/\\\])+$/',
                    'regExpressionError' => __('Incorrect URL. You can\'t use slash in URL.', 'ipAdmin')
            )
            );
            $this->addElement($element);



            $element = new \Ip\Lib\StdMod\Element\Text(
            array(
                    'title' => __('RFC 4646 code', 'ipAdmin'),
                    'showOnList' => true,
                    'dbField' => 'code',
                    'required' => true
            )
            );
            $this->addElement($element);



            $element = new \Ip\Lib\StdMod\Element\Text(
            array(
                    'title' => ipGetOption('Config.text_direction'),
                    'showOnList' => true,
                    'dbField' => 'text_direction',
                    'required' => true,
                    'defaultValue' => 'ltr'
            )
            );
            $this->addElement($element);


    }


    function afterInsert($id) {
        Db::createRootZoneElement($id);
    }

    function afterDelete($id) {
        Db::deleteRootZoneElement($id);

        // TODOX remove language options
    }


    function beforeUpdate($id) {
        $tmpLanguage = Db::getLanguageById($id);
        $this->urlBeforeUpdate = $tmpLanguage['url'];

    }


    function afterUpdate($id) {
        $tmpLanguage = Db::getLanguageById($id);
        if($tmpLanguage['url'] != $this->urlBeforeUpdate && ipGetOption('Config.multilingual')) {
            $oldUrl = ipFileUrl($this->urlBeforeUpdate.'/');
            $newUrl = ipFileUrl($tmpLanguage['url'].'/');
            ipDispatcher()->notify('site.urlChanged', array('oldUrl' => $oldUrl, 'newUrl' => $newUrl));
        }
    }

    function allowDelete($id) {

        $dbMenuManagement = new \Ip\Internal\Pages\Db();

        $answer = true;


        $zones = Db::getZones();
        foreach($zones as $key => $zone) {
            $rootElement = $dbMenuManagement->rootId($zone['id'], $id);
            $elements = $dbMenuManagement->pageChildren($rootElement);
            if(sizeof($elements) > 0) {
                $answer = false;
                $this->errors['delete'] = __('Please delete all pages in this language and then try again.', 'ipAdmin');
            }
        }

        if(sizeof(Db::getLanguages()) ==1) {
            $answer = false;
            $this->errors['delete'] = __('There should be at least one language.', 'ipAdmin');
        }


        return $answer;
    }

    function lastError($action) {
        if(isset($this->errors[$action]))
        return $this->errors[$action];
        else
        return '';
    }



}