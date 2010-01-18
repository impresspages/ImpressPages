<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see license.html
 */
namespace Modules\catalog\categories;

if (!defined('BACKEND')) exit;

require_once(BASE_DIR.MODULE_DIR.'developer/std_mod/std_mod.php');
require_once(BASE_DIR.MODULE_DIR.'standard/languages/db.php');
require_once(BASE_DIR.PLUGIN_DIR.'catalog/categories/db.php');
require_once(BASE_DIR.PLUGIN_DIR.'catalog/categories/element_select_category.php');
require_once(BASE_DIR.PLUGIN_DIR.'catalog/categories/element_url_lang.php');
require_once(BASE_DIR.PLUGIN_DIR.'catalog/categories/element_modified.php');
require_once(BASE_DIR.PLUGIN_DIR.'catalog/items/db.php');
require_once(BASE_DIR.LIBRARY_DIR.'php/text/specialchars.php');

class CategoriesArea extends \Modules\developer\std_mod\Area{
  protected $configObjects;
  function __construct(){    
    global $parametersMod;
    parent::__construct(
      array(
      'title' => $parametersMod->getValue('catalog', 'categories', 'admin_translations', 'categories'),
      'dbTable' => 'm_catalog_category',
      'dbPrimaryKey' => 'id',
      'searchable' => true,
      'orderBy' => 'id',
      'whereCondition' => ' parent_id is null ',
      'dbReference' => 'parent_id',
      'sortable' => true,
      'newRecordPosition' => 'null',
      'sortField' => 'priority',
      'sortType' => 'numbers'
      )    
    );
    
    //include fields from configration files
    $this->configObjects = array();
    if(is_dir(BASE_DIR.CONFIG_DIR.'catalog/categories/')){
      $configFiles = scandir(BASE_DIR.CONFIG_DIR.'catalog/categories/');
      foreach($configFiles as $key => $configFile){
        if($configFile != '.' && $configFile != '..' && !is_dir(BASE_DIR.CONFIG_DIR.'catalog/categories/'.$configFile)){
          require_once(BASE_DIR.CONFIG_DIR.'catalog/categories/'.$configFile);
        }      
      }  
      
      $allClasses = get_declared_classes();
      foreach($allClasses as $key => $class){
        if(strpos($class, 'Modules\\catalog\\categories\\Config\\') === 0){
          eval ('$tmpConfig = new '.$class.'();');
          $configObjects[] = $tmpConfig;
        }
      }    
      
    }
        
    
    $element = new \Modules\developer\std_mod\ElementBoolLang(
    array(
    'title' => $parametersMod->getValue('catalog', 'categories', 'admin_translations', 'visible'),
    'dbField' => 'visible',
    'showOnList' => true,
    'searchable' => true,
    'defaultValue' => true,
    'translationTable' => 'm_catalog_category_translation',
    'translationField' => 'visible' 
    )
    );
    $this->addElement($element);
        
    
    $element = new \Modules\developer\std_mod\ElementTextLang(
    array(
    'title' => $parametersMod->getValue('catalog', 'categories', 'admin_translations', 'button_title'),
    'useInBreadcrumb' => true,      
    'showOnList' => true,
    'searchable' => true,
    'translationTable' => 'm_catalog_category_translation',
    'translationField' => 'button_title'  
    )
    );
    $this->addElement($element);

    $element = new \Modules\developer\std_mod\ElementTextLang(
    array(
    'title' => $parametersMod->getValue('catalog', 'categories', 'admin_translations', 'page_title'),
    'showOnList' => true,
    'searchable' => true,
    'translationTable' => 'm_catalog_category_translation',
    'translationField' => 'page_title'  
    )
    );
    $this->addElement($element);
    

    
    $element = new ElementUrlLang(
    array(
    'title' => $parametersMod->getValue('catalog', 'categories', 'admin_translations', 'url'),
    'dbField' => 'url',
    'showOnList' => true,
    'searchable' => true,
    'translationTable' => 'm_catalog_category_translation',
    'translationField' => 'url' 
    )
    );
    $this->addElement($element);    
    
    
    
    $element = new \Modules\developer\std_mod\ElementTextareaLang(
    array(
    'title' => $parametersMod->getValue('catalog', 'categories', 'admin_translations', 'description'),
    'dbField' => 'description',
    'showOnList' => true,
    'searchable' => true,
    'translationTable' => 'm_catalog_category_translation',
    'translationField' => 'description' 
    )
    );
    $this->addElement($element);    
    
    
    $element = new \Modules\developer\std_mod\ElementTextareaLang(
    array(
    'title' => $parametersMod->getValue('catalog', 'categories', 'admin_translations', 'keywords'),
    'dbField' => 'keywords',
    'showOnList' => true,
    'searchable' => true,
    'translationTable' => 'm_catalog_category_translation',
    'translationField' => 'keywords' 
    )
    );
    $this->addElement($element);
    
    

    
    $copies  = array();  
    $copy = array(
      'width' => 100,
      'height' => 100,
      'quality' => 100,
      'dbField' => 'photo',
      'type' => 'crop',
      'forced' => 1,
      'destDir' => IMAGE_DIR            
    );
    $copies[] = $copy;    
    
    
    $element = new \Modules\developer\std_mod\ElementImage(
    array(     
    'title' => $parametersMod->getValue('catalog', 'categories', 'admin_translations', 'photo'),
    'showOnList' => true,
    'copies' => $copies
    )
    );
    $this->addElement($element);       


    $element = new ElementSelectCategory(
    array(      
    'title' => $parametersMod->getValue('catalog', 'categories', 'admin_translations', 'parent_category'),
    'dbField' => 'parent_id',
    'showOnList' => true,
    'searchable' => true 
    )
    );
    $this->addElement($element);
  
    
    

    $element = new \Modules\developer\std_mod\ElementDateTime(
    array(     
    'title' => $parametersMod->getValue('catalog', 'categories', 'admin_translations', 'created'),
    'dbField' => 'created',
    'showOnList' => false,
    'searchable' => false,
    'defaultValue' => date('Y-m-d H:i:s'),
    'visibleOnInsert' => false
    )
    );
    $this->addElement($element);    

    $element = new ElementModified(
    array(     
    'title' => $parametersMod->getValue('catalog', 'categories', 'admin_translations', 'modified'),
    'dbField' => 'modified',
    'showOnList' => false,
    'searchable' => false,
    'defaultValue' => date('Y-m-d H:i:s'),
    'visibleOnInsert' => false
    )
    );
    $this->addElement($element);    
    
            
    
  }
  
  public function allowDelete($id){
    require_once(BASE_DIR.PLUGIN_DIR.'catalog/items/db.php');
    $allowDelete = true;
    
    $languages = \Frontend\Db::getLanguages(true);
    foreach($languages as $key => $language){    
      $items = \Modules\catalog\items\Db::getItems($language['id'], $id);
      if(sizeof($items) > 0){
        return false;
      }
    }
    
    //actions from configuration files
    foreach($this->configObjects as $key => $object){
      if(method_exists($object, 'allowDelete')){
        if(!$object->allowDelete($id)){
          return false;
        }
      } 
    }
    return true;    
  }  
  
  
  public function afterUpdate($recordId){
    $this->updateUrl($recordId);

  }
  public function afterInsert($recordId){
    $this->updateUrl($recordId);
    
    $languages = \Modules\standard\languages\Db::getLanguages();

    foreach($languages as $language){
      $item = \Modules\catalog\categories\Db::getCategoryById($language['id'], $recordId);

      if ($item['page_title'] == '' && $item['button_title'] != ''){
        $sql = " update `".DB_PREF."m_catalog_category_translation` set `page_title` = '".mysql_real_escape_string($item['button_title'])."'
        where `language_id` =  ".(int)$language['id']." and `record_id` = ".(int)$recordId." ";
        $rs = mysql_query($sql);
        if(!$rs){
          trigger_error($sql." ".mysql_error());
        }        
        
      }
      
      if ($item['page_title'] != '' && $item['button_title'] == ''){
        $sql = " update `".DB_PREF."m_catalog_category_translation` set `button_title` = '".mysql_real_escape_string($item['page_title'])."'
        where `language_id` =  ".(int)$language['id']." and `record_id` = ".(int)$recordId." ";
        $rs = mysql_query($sql);
        if(!$rs){
          trigger_error($sql." ".mysql_error());
        }        
        
      }
      
      
    }

    
  } 
  
  
  public function beforeInsert(){

    //actions from configuration files
    foreach($this->configObjects as $key => $object){
      if(method_exists($object, 'beforeInsert')){
        $object->beforeInsert();
      } 
    }    
  }
  

  
  public function beforeUpdate($recordId){
    //actions from configuration files
    foreach($this->configObjects as $key => $object){
      if(method_exists($object, 'afterUpdate')){
        $object->afterUpdate($recordId);
      } 
    }    
  }
  
  
  public function beforeDelete($recordId){
    //actions from configuration files
    foreach($this->configObjects as $key => $object){
      if(method_exists($object, 'beforeDelete')){
        $object->beforeDelete($recordId);
      } 
    }    
  }
  
  public function afterDelete($recordId){
    //actions from configuration files
    foreach($this->configObjects as $key => $object){
      if(method_exists($object, 'afterDelete')){
        $object->afterDelete($recordId);
      } 
    }    
  }
  
  public function beforeSort(){
    //actions from configuration files
    foreach($this->configObjects as $key => $object){
      if(method_exists($object, 'beforeSort')){
        $object->beforeSort();
      } 
    }    
    
  }
  
  public function afterSort(){
    //actions from configuration files
    foreach($this->configObjects as $key => $object){
      if(method_exists($object, 'afterSort')){
        $object->afterSort();
      }
    }
  }
  

  
  private function updateUrl($recordId){
    $languages = \Modules\standard\languages\Db::getLanguages();

    foreach($languages as $language){
      
      $item = \Modules\catalog\categories\Db::getCategoryById($language['id'], $recordId);
    
      if ($item['url'] == '') {
        $url = \Library\Php\Text\Specialchars::url($item['button_title']);
        if (\Modules\catalog\items\Db::getItemByUrl($language['id'], $url) || \Modules\catalog\categories\Db::getCategoryByUrl($language['id'], $url)) {
          $i = 1;
          while(\Modules\catalog\items\Db::getItemByUrl($language['id'], $url.$i) || \Modules\catalog\categories\Db::getCategoryByUrl($language['id'], $url.$i)){
            $i++;
          }
          $url .= $i;
        }
        
        $sql = " update `".DB_PREF."m_catalog_category_translation` set `url` = '".mysql_real_escape_string($url)."'
        where `language_id` =  ".(int)$language['id']." and `record_id` = ".(int)$recordId." ";
        $rs = mysql_query($sql);
        if(!$rs){
          trigger_error($sql." ".mysql_error());
        }        
      }
    }
  }
}