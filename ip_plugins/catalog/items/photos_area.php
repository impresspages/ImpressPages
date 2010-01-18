<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see license.html
 */
namespace Modules\catalog\items;

if (!defined('BACKEND')) exit;

require_once(BASE_DIR.MODULE_DIR.'developer/std_mod/std_mod.php');


class PhotosArea extends \Modules\developer\std_mod\Area{

  function __construct(){
    global $parametersMod;    
    parent::__construct(
      array(
      'dbTable' => 'm_catalog_item_photo',
      'title' => $parametersMod->getValue('catalog', 'items', 'admin_translations', 'photos'),
      'dbPrimaryKey' => 'id',
      'searchable' => false,
      'orderBy' => 'row_number',
      'dbReference' => 'record_id',
      'sortable' => true,
      'sortField' => 'row_number'
      )    
    );

   
  
    $copies  = array();  
    $copy = array(
      'width' => $parametersMod->getValue('catalog', 'items', 'options', 'photo_width'),
      'height' => $parametersMod->getValue('catalog', 'items', 'options', 'photo_height'),
      'quality' => $parametersMod->getValue('catalog', 'items', 'options', 'photo_quality'),
      'dbField' => 'photo',
      'type' => $parametersMod->getValue('catalog', 'items', 'options', 'photo_type'),
      'forced' => 1,
      'destDir' => IMAGE_DIR            
    );
    $copies[] = $copy;    

    $copy = array(
      'width' => $parametersMod->getValue('catalog', 'items', 'options', 'photo_big_width'),
      'height' => $parametersMod->getValue('catalog', 'items', 'options', 'photo_big_height'),
      'quality' => $parametersMod->getValue('catalog', 'items', 'options', 'photo_big_quality'),
      'dbField' => 'photo_big',
      'type' => $parametersMod->getValue('catalog', 'items', 'options', 'photo_big_type'),
      'forced' => 1,
      'destDir' => IMAGE_DIR            
    );
    $copies[] = $copy;      

    
    
    $element = new \Modules\developer\std_mod\ElementImage(
    array(     
    'title' => $parametersMod->getValue('catalog', 'items', 'admin_translations', 'photo'),
    'showOnList' => true,
    'copies' => $copies
    )
    );
    $this->addElement($element);    
    
  }
  
  public function afterInsert($recordId){
    $this->setFirstPhoto();
  }
  
  public function afterDelete($recordId){
    $this->setFirstPhoto();
  }
  
  public function afterUpdate($recordId){
    $this->setFirstPhoto();
  }
  
  public function afterSort(){
    $this->setFirstPhoto();
  }
  
  private function setFirstPhoto(){
    $sql = "select * from `".DB_PREF."m_catalog_item_photo` where record_id = ".(int)$this->parentId." order by row_number";
    $rs =  mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        $sql = "update `".DB_PREF."m_catalog_item` set `first_photo` = '".mysql_real_escape_string($lock['photo'])."', `first_photo_big` = '".mysql_real_escape_string($lock['photo_big'])."'
        where `id` = ".(int)$this->parentId." limit 1";
        $rs =  mysql_query($sql);
        if(!$rs)
          trigger_error($sql." ".mysql_error());
      } else {
        $sql = "update `".DB_PREF."m_catalog_item` set `first_photo` = null , `first_photo_big` = null 
        where `id` = ".(int)$this->parentId." limit 1";
        $rs =  mysql_query($sql);
        if(!$rs)
          trigger_error($sql." ".mysql_error());        
      }
    } else {
      trigger_error($sql." ".mysql_error());
      return false;
    }
    

    
  }
  
  
}