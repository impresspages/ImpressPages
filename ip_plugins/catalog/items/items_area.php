<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see license.html
 */
namespace Modules\catalog\items;

if (!defined('BACKEND')) exit;

require_once(BASE_DIR.MODULE_DIR.'developer/std_mod/std_mod.php');
require_once(BASE_DIR.MODULE_DIR.'standard/languages/db.php');
require_once(BASE_DIR.PLUGIN_DIR.'catalog/categories/db.php');
require_once(BASE_DIR.PLUGIN_DIR.'catalog/items/db.php');
require_once(BASE_DIR.PLUGIN_DIR.'catalog/items/element_select_category.php');
require_once(BASE_DIR.PLUGIN_DIR.'catalog/items/element_url_lang.php');
require_once(BASE_DIR.LIBRARY_DIR.'php/text/specialchars.php');

class ItemsArea extends \Modules\developer\std_mod\Area{
  protected $configObjects;
  function __construct(){ 
    global $parametersMod;
       
    parent::__construct(
      array(
      'title' => $parametersMod->getValue('catalog', 'items', 'admin_translations', 'items'),
      'dbTable' => 'm_catalog_item',
      'dbPrimaryKey' => 'id',
      'searchable' => true,
      'orderBy' => 'id',
      'orderDirection' => 'desc',
      'sortable' => true,
      'newRecordPosition' => 'null',
      'sortField' => 'priority',      
      'sortType' => 'numbers'      
      )    
    );
      
    
    //create configuration objects
    $this->configObjects = array();
    if(is_dir(BASE_DIR.CONFIG_DIR.'catalog/items/')){
      $configFiles = scandir(BASE_DIR.CONFIG_DIR.'catalog/items/');
      foreach($configFiles as $key => $configFile){
        if($configFile != '.' && $configFile != '..' && !is_dir(BASE_DIR.CONFIG_DIR.'catalog/items/'.$configFile)){
          require_once(BASE_DIR.CONFIG_DIR.'catalog/items/'.$configFile);
        }      
      }  
      
      $allClasses = get_declared_classes();
      foreach($allClasses as $key => $class){
        if(strpos($class, 'Modules\\catalog\\items\\Config\\') === 0){
          eval ('$tmpConfig = new '.$class.'();');
          $configObjects[] = $tmpConfig;
        }
      }    
    }
      
    
    
    
    
    $element = new \Modules\developer\std_mod\ElementBoolLang(
    array(     
    'title' => $parametersMod->getValue('catalog', 'items', 'admin_translations', 'visible'),
    'dbField' => 'visible',
    'showOnList' => false,
    'searchable' => true,
    'defaultValue' => true,
    'translationTable' => 'm_catalog_item_translation',
    'translationField' => 'visible' 
    )
    );
    $this->addElement($element);
        
    
    $element = new \Modules\developer\std_mod\ElementTextLang(
    array(
    'title' => $parametersMod->getValue('catalog', 'items', 'admin_translations', 'title'),
    'useInBreadcrumb' => true,      
    'dbField' => 'title',
    'showOnList' => true,
    'searchable' => true,
    'translationTable' => 'm_catalog_item_translation',
    'translationField' => 'title'  
    )
    );
    $this->addElement($element);
  
    
    
    $element = new ElementSelectCategory(
    array(      
    'title' => $parametersMod->getValue('catalog', 'items', 'admin_translations', 'category'),
    'dbField' => 'category_id',
    'showOnList' => true,
    'searchable' => true 
    )
    );
    $this->addElement($element);
  
    
    
    $element = new \Modules\developer\std_mod\ElementNumber(
    array(     
    'title' => $parametersMod->getValue('catalog', 'items', 'admin_translations', 'price'),
    'dbField' => 'price',
    'showOnList' => true,
    'searchable' => true
    )
    );
    $this->addElement($element);
  
    
    
    $element = new \Modules\developer\std_mod\ElementNumber(
    array(     
    'title' => $parametersMod->getValue('catalog', 'items', 'admin_translations', 'discount'),
    'dbField' => 'discount',
    'showOnList' => true,
    'searchable' => true
    )
    );
    $this->addElement($element);
    
    
    $element = new \Modules\developer\std_mod\ElementNumber(
    array(     
    'title' => $parametersMod->getValue('catalog', 'items', 'admin_translations', 'quantity'),
    'dbField' => 'quantity',
    'showOnList' => true,
    'searchable' => true,
    'defaultValue' => 0,
    'required' => true,
    'minVal' => 0
    )
    );
    $this->addElement($element);
        
    
    $element = new \Modules\developer\std_mod\ElementWysiwygLang(
    array(     
    'title' => $parametersMod->getValue('catalog', 'items', 'admin_translations', 'description'),
    'showOnList' => false,
    'searchable' => true,
    'translationTable' => 'm_catalog_item_translation',
    'translationField' => 'description' 
    )
    );
    $this->addElement($element);   
    
    //include fields from configration files
    foreach($this->configObjects as $key => $object){
      if(method_exists($object, 'additionalElements')){
        $additionalElements = $object->additionalElements();
        foreach($additionalElements as $element){
          $this->addElement($element);
        }
      } 
    }
    

  
     
    
    $element = new \Modules\developer\std_mod\ElementFile(
    array(     
    'title' => $parametersMod->getValue('catalog', 'items', 'admin_translations', 'file'),
    'dbField' => 'file',
    'showOnList' => true,
    'searchable' => true
    )
    );
    $this->addElement($element);
    

    
    $element = new ElementUrlLang(
    array(     
    'title' => $parametersMod->getValue('catalog', 'items', 'admin_translations', 'url'),
    'showOnList' => false,
    'searchable' => true,
    'translationTable' => 'm_catalog_item_translation',
    'translationField' => 'meta_url' 
    )
    );
    $this->addElement($element);    
    
    
    
    
    $element = new \Modules\developer\std_mod\ElementTextareaLang(
    array(     
    'title' => $parametersMod->getValue('catalog', 'items', 'admin_translations', 'meta_description'),
    'showOnList' => false,
    'searchable' => true,
    'translationTable' => 'm_catalog_item_translation',
    'translationField' => 'meta_description' 
    )
    );
    $this->addElement($element);    
  
    
    
    
    
    $element = new \Modules\developer\std_mod\ElementTextareaLang(
    array(     
    'title' => $parametersMod->getValue('catalog', 'items', 'admin_translations', 'meta_keywords'),
    'showOnList' => false,
    'searchable' => true,
    'translationTable' => 'm_catalog_item_translation',
    'translationField' => 'meta_keywords' 
    )
    );
    $this->addElement($element);
    
    
    
      
    $copies  = array();  
    $copy = array(
      'width' => 200,
      'height' => 200,
      'quality' => 90,
      'dbField' => 'first_photo',
      'type' => 'crop',
      'forced' => 1,
      'destDir' => IMAGE_DIR            
    );
    $copies[] = $copy;     

    
    $element = new \Modules\developer\std_mod\ElementImage(
    array(     
    'title' => $parametersMod->getValue('catalog', 'items', 'admin_translations', 'photo'),
    'showOnList' => true,
    'searchable' => true,
    'visibleOnInsert' => false,
    'visibleOnUpdate' => false,
    'copies' => $copies
    )
    );
    $this->addElement($element);       
  

    $element = new \Modules\developer\std_mod\ElementDateTime(
    array(     
    'title' => $parametersMod->getValue('catalog', 'items', 'admin_translations', 'created'),
    'dbField' => 'created',
    'showOnList' => false,
    'searchable' => false,
    'defaultValue' => date('Y-m-d H:i:s'),
    'visibleOnInsert' => false
    )
    );
    $this->addElement($element);    

    $element = new \Modules\developer\std_mod\ElementDateTime(
    array(     
    'title' => $parametersMod->getValue('catalog', 'items', 'admin_translations', 'modified'),
    'dbField' => 'modified',
    'showOnList' => false,
    'searchable' => false,
    'defaultValue' => date('Y-m-d H:i:s'),
    'visibleOnInsert' => false
    )
    );
    $this->addElement($element);    
    
        
    

    
  }
   
  

  public function afterUpdate($recordId){
    
    $this->updateUrl($recordId);
    
    $this->updateText($recordId);
    
    
    //actions from configuration files
    foreach($this->configObjects as $key => $object){
      if(method_exists($object, 'afterUpdate')){
        $object->afterUpdate($recordId);
      } 
    }
  }

  public function afterInsert($recordId){
    
    $this->updateUrl($recordId);

    $this->updateText($recordId);
    
        
    //actions from configuration files
    foreach($this->configObjects as $key => $object){
      if(method_exists($object, 'afterInsert')){
        $object->afterInsert($recordId);
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
  
  public function allowDelete($recordId){
    return true;
  }  
  
  private function updateText($recordId){
    require_once(BASE_DIR.LIBRARY_DIR.'php/text/html2text.php');
    //generate text field for search
    $languages = \Modules\standard\languages\Db::getLanguages();

    foreach($languages as $language){
      $item = \Modules\catalog\items\Db::getItemById($language['id'], $recordId);
    
      $html2text = new \Library\Php\Text\Html2Text();
      $html2text->set_html($item['description']);
      $description = $html2text->get_text(); 
      $text = $item['title'].' '.$description.' '.$item['meta_keywords'].' '.$item['meta_description'].' '.$item['meta_url'].' '.$item['price'].' '.$item['discount'].' '.($item['price']-$item['discount']).' '.$item['quantity'].' '.$item['file'];
      
      $html = '<h1>'.htmlspecialchars($item['title']).'</h1>'.$item['description'].' <p>'.$item['meta_keywords'].' '.$item['meta_description'].' '.$item['meta_url'].' '.$item['price'].' '.$item['discount'].' '.($item['price']-$item['discount']).' '.$item['quantity'].' '.$item['file'].'</p>';
      
      $sql = " update `".DB_PREF."m_catalog_item_translation` set `text` = '".mysql_real_escape_string($text)."', `html` = '".mysql_real_escape_string($html)."'
      where `language_id` =  ".(int)$language['id']." and `record_id` = ".(int)$recordId." ";
      $rs = mysql_query($sql);
      if(!$rs){
        trigger_error($sql." ".mysql_error());
      }        
    }    
  }
  
  
  private function updateUrl($recordId){
    $languages = \Modules\standard\languages\Db::getLanguages();

    foreach($languages as $language){
      $item = \Modules\catalog\items\Db::getItemById($language['id'], $recordId);
    
      if ($item['meta_url'] == '') {
        $url = \Library\Php\Text\Specialchars::url($item['title']);
        if (\Modules\catalog\items\Db::getItemByUrl($language['id'], $url) || \Modules\catalog\categories\Db::getCategoryByUrl($language['id'], $url)) {
          $i = 1;
          while(\Modules\catalog\items\Db::getItemByUrl($language['id'], $url.$i) || \Modules\catalog\categories\Db::getCategoryByUrl($language['id'], $url.$i)){
            $i++;
          }
          $url .= $i;
        }
        $sql = " update `".DB_PREF."m_catalog_item_translation` set `meta_url` = '".mysql_real_escape_string($url)."'
        where `language_id` =  ".(int)$language['id']." and `record_id` = ".(int)$recordId." ";
        $rs = mysql_query($sql);
        if(!$rs){
          trigger_error($sql." ".mysql_error());
        }        
      }      
    }
  }
  

    

  
}