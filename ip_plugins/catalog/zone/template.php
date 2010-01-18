<?php 
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */

namespace Modules\catalog\zone;  
 
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;
class Template {
  
  public static function generateList($item, $subCategories, $elements, $pages, $currentPage){
    global $site;
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/template.php');
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text_photo/template.php');
    $answer = '';
    
    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($site->getTitle(), 1);
    
    //uncoment to show subcategories above the list of items  
    /*
    $categoryList = '';
    foreach($subCategories as $key => $element){
      $categoryList .= self::categoryInList($element);
    }
    
    if($categoryList != ''){
      $answer .= '<div class="modCatalogCategories">'.$categoryList.'</div>';
    }
    */

    $itemList = '';
    foreach($elements as $key => $element){
      $itemList .= self::itemInList($element);
    }
    
    if(sizeof($pages) > 1){
      $pagesHtml = self::pages($pages, $currentPage);
    }else{
      $pagesHtml = '';
    }    
    
    if($itemList != ''){
      $answer .= $pagesHtml.'<div class="modCatalogItems">'.$itemList.'</div>'.$pagesHtml;
    }
    
   
    
   
    
    return $answer;
  }
  
  public static function pages($pages, $currentPage){
    $answer = '';
    foreach($pages as $key => $pageLink){      
      if($key == $currentPage)
        $answer .= '<li class="current"><a href="'.$pageLink.'">'.($key+1).'</a></li>';
      else
        $answer .= '<li><a href="'.$pageLink.'">'.($key+1).'</a></li>';
    }
    
    if($answer != ''){
      $answer = '<ul class="modCatalogPages">'.$answer.'</ul>';
    }
    return $answer;
  }
  
  public static function categoryInList($category){
    
    
    $text = '<h2><a href="'.$category->getLink().'">'.htmlspecialchars($category->getPageTitle()).'</a></h2>';
    $text .= '<p>'.htmlspecialchars($category->getDescription()).'</p>';

    if($category->getPhoto()){
      $photo = BASE_URL.IMAGE_DIR.$category->getPhoto();
    } else {
      $photo = null;
    }


    if ($category->getPhoto())
      $image = '<a href="'.$category->getLink().'"><img class="contentModTextPhotoImageSmallLeft" src="'.$photo.'" alt="'.htmlspecialchars($category->getPageTitle()).'" /></a>';
    else
      $image = '';
    return ' 
      <div class="contentMod contentModTextPhoto">
        '.$image.'
        <div class="contentModTextPhotoText">'.$text.'</div> 
        <div class="clear"><!-- --></div>
      </div>
    
    ';

  }
  
  public static function itemInList($item){
    require_once(BASE_DIR.PLUGIN_DIR.'shop/currencies/module.php');
    
    global $site;
    
    $text = '<h2><a href="'.$item->getLink().'">'.htmlspecialchars($item->getPageTitle()).'</a></h2>';
    
    $text .= '<p>'.htmlspecialchars($item->getDescription()).'</p>';
    if($item->getPrice()){
      $text .= '<p class="modCatalogPrpertyPrice"><span class="modCatalogPrperty">Price: </span><span class="modCatalogValue">'.htmlspecialchars(\Modules\shop\currencies\Module::formatCurrency($item->getPrice() - $item->getDiscount())).'</span></p>';
    }
    
    if($item->getPhoto()){
      $photo = BASE_URL.IMAGE_DIR.$item->getPhoto();
    } else {
      $photo = null;
    }


    if ($photo)
      $image = '<a href="'.$item->getLink().'"><img class="contentModTextPhotoImageSmallLeft" src="'.$photo.'" alt="'.htmlspecialchars($item->getPageTitle()).'" /></a>';
    else
      $image = '';
    return ' 
      <div class="contentMod contentModTextPhoto">
        '.$image.'
        <div class="contentModTextPhotoText">'.$text.'</div> 
        <div class="clear"><!-- --></div>
      </div>
    
    ';

  }  
  
  
  public static function generateItem($item){
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/template.php');
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text_photo/template.php');
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/photo_gallery/template.php');
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/separator/template.php');
    require_once(BASE_DIR.PLUGIN_DIR.'shop/currencies/module.php');
    
    global $site;
    global $parametersMod;

    $answer = '';
    
    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($item->getPageTitle(), 1);

    
    $text = $item->getContent();

    
    if($item->getPrice()){
      $text .= '<p class="modCatalogPrpertyPrice"><span class="modCatalogPrperty">'.htmlspecialchars($parametersMod->getValue('catalog', 'zone', 'translations', 'price')).': </span><span class="modCatalogValue">'.htmlspecialchars(\Modules\shop\currencies\Module::formatCurrency($item->getPrice() - $item->getDiscount())).'</span></p>';
    }

    $text .= '<p class="modCatalogPrpertyQuantity"><span class="modCatalogPrperty">'.htmlspecialchars($parametersMod->getValue('catalog', 'zone', 'translations', 'quantity')).': </span><span class="modCatalogValue">'.((int)$item->getQuantity()).'</span></p>';
    
    if($item->getFile()){
      $text .= '<p class="modCatalogPrpertyFile"><span class="modCatalogValue"><a href="'.BASE_URL.FILE_DIR.$item->getFile().'">'.htmlspecialchars($item->getFile()).'</a></span></p>';
    }
    
    if($item->getPhoto()){
      $photo = $item->getPhoto();
    } else {
      $photo = null;
    }

    if($item->getPhotoBig()){
      $photoBig = $item->getPhotoBig();
    } else {
      $photoBig = null;
    }
    
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text_photo\Template::generateHtml($item->getPageTitle(), $photo, $photoBig, $text);
    
    $photos = $item->getPhotos();
    array_shift($photos);
    if(sizeof($photos) > 0){
      $answer .= \Modules\standard\content_management\Widgets\text_photos\photo_gallery\Template::generateHtml($photos);
    }
/*    if ($photo)
      $image = '<a href="'.$item->getLink().'"><img class="contentModTextPhotoImageLeft" src="'.$photo.'" alt="'.htmlspecialchars($item->getPageTitle()).'" /></a>';
    else
      $image = '';
    $answer .= ' 
      <div class="contentMod contentModTextPhoto">
        '.$image.'
        <div class="contentModTextPhotoText">'.$text.'</div> 
        <div class="clear"><!-- --></div>
      </div>
    
    ';*/
    return $answer;
  
  }
}