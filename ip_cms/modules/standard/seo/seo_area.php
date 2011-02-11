<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */
namespace Modules\standard\seo;

if (!defined('BACKEND')) exit;

require_once(BASE_DIR.MODULE_DIR.'developer/std_mod/std_mod.php');
require_once(__DIR__.'/db.php');
require_once(__DIR__.'/element_seo.php');


class SeoArea extends \Modules\developer\std_mod\Area {

  var $errors = array();
  var $urlsBeforeUpdate = array();

  function __construct() {
    global $parametersMod;
    parent::__construct(
            array(
            'dbTable' => 'zone',
            'title' => $parametersMod->getValue('developer','zones','admin_translations','zones'),
            'dbPrimaryKey' => 'id',
            'searchable' => false,
            'orderBy' => 'row_number',
            'sortable' => false,
            'allowInsert' => false,
            'allowDelete' => false,
            'searchable' => true
            )
    );



    $options = array(
            'title' => $parametersMod->getValue('developer','zones','admin_translations','name'),
            'showOnList' => true,
            'dbField' => 'translation',
            'useInBreadcrumb' => true,
            'required' => true,
            'visibleOnUpdate' => false);
    $element = new \Modules\developer\std_mod\ElementText($options);
    $this->addElement($element);



    $element = new \Modules\developer\std_mod\ElementTextLang(
            array(
                    'title' => $parametersMod->getValue('standard','seo','admin_translations','title'),
                    'showOnList' => true,
                    'searchable' => true,
                    'translationTable' => 'zone_parameter',
                    'translationField' => 'title',
                    'recordIdField' => 'zone_id'
    ));
    $this->addElement($element);

    $element = new \Modules\developer\std_mod\ElementTextLang(
            array(
                    'title' => $parametersMod->getValue('standard','seo','admin_translations','url'),
                    'showOnList' => true,
                    'searchable' => true,
                    'translationTable' => 'zone_parameter',
                    'translationField' => 'url',
                    'recordIdField' => 'zone_id'
    ));
    $this->addElement($element);

    $element = new \Modules\developer\std_mod\ElementTextareaLang(
            array(
                    'title' => $parametersMod->getValue('standard','seo','admin_translations','keywords'),
                    'showOnList' => true,
                    'searchable' => true,
                    'translationTable' => 'zone_parameter',
                    'translationField' => 'keywords',
                    'recordIdField' => 'zone_id'
    ));
    $this->addElement($element);


    $element = new \Modules\developer\std_mod\ElementTextareaLang(
            array(
                    'title' => $parametersMod->getValue('standard','seo','admin_translations','description'),
                    'showOnList' => true,
                    'searchable' => true,
                    'translationTable' => 'zone_parameter',
                    'translationField' => 'description',
                    'recordIdField' => 'zone_id'
    ));
    $this->addElement($element);



  }


  function beforeUpdate($id) {
    //find unique url
    $stdModDb = new \Modules\developer\std_mod\StdModDb();
    $languages = Db::getLanguages();
    foreach($languages as $key => $language){
      $parameter = Db::getParameter($id, $language['id']);
      $_REQUEST['i_n_2_'.$language['id']] = Db::newUrl($language['id'], $_REQUEST['i_n_2_'.$language['id']], $parameter['id']);
    }
    //end find unique url

    //store old url values
    foreach($languages as $key => $language){
      $parameter = Db::getParameter($id, $language['id']);
      $this->urlsBeforeUpdate[$language['id']] = $parameter['url'];
    }
    //end store old url values


  }


  function afterUpdate($id) {
    global $site;
    $stdModDb = new \Modules\developer\std_mod\StdModDb();
    $languages = Db::getLanguages();
    foreach($languages as $key => $language){
      $parameter = Db::getParameter($id, $language['id']);
      if($parameter['url'] != $this->urlsBeforeUpdate[$language['id']]){

        $oldUrl = $this->makeUrl($language['url'], $this->urlsBeforeUpdate[$language['id']]);
        $newUrl = $this->makeUrl($language['url'], $parameter['url']);
        $site->dispatchEvent('administrator', 'system', 'url_change', array('old_url'=>$oldUrl, 'new_url'=>$newUrl));

      }


      $_REQUEST['i_n_2_'.$language['id']] = Db::newUrl($language['id'], $_REQUEST['i_n_2_'.$language['id']], $parameter['id']);
    }

  }


  private function makeUrl($languageUrl, $zoneUrl){
    global $parametersMod;
    $answer = '';
    if($parametersMod->getValue('standard', 'languages', 'options', 'multilingual')){
      $answer = BASE_URL.urlencode($languageUrl).'/'.urlencode($zoneUrl).'/';
    }else{
      $answer = BASE_URL.urlencode($zoneUrl).'/';
    }
    return $answer;
  }


}