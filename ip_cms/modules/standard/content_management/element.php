<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management;


if (!defined('CMS')) exit;  

require_once (__DIR__.'/site_db.php');

/**
 * Website zone element. Typically each element represents one page on zone.<br />
 *
 * @package ImpressPages
 */

class Element extends \Frontend\Element {
  protected $dynamicModules;

  public function getLink() {
    if($this->link == null) {
      $this->generateDepthAndLink();
    }

    return $this->link;
  }

  public function getDepth() {
    if($this->depth == null)
      $this->generateDepthAndLink();

    return $this->depth;
  }


  public function generateContent() {

    global $site;
    global $moduleUrl;
    global $site;

    require_once (__DIR__.'/db.php');

    //redirects are made automatically by site class.
    /*    switch($this->type){
      case 'subpage':
        $tmpChildren = $site->getZone($this->zoneName)->getElements(null, $this->id, 0, $limit = 1);
        if(sizeof($tmpChildren) == 1){
          return '
          <script type="text/javascript">
          //<![CDATA[
            document.location = \''.$tmpChildren[0]->getLink().'\';
          //]]>  
          </script>
            ';
        }
      break;
      case 'redirect':
        return '
        <script type="text/javascript">
        //<![CDATA[
          document.location = \''.$this->redirectUrl.'\';
        //]]>  
        </script>
          ';
        
      break;
    }    */

    //if no redirect, put the content
    $moduleUrl = 'modules/standard/menu_management/';
    /* required by content modules */
    $answer = "";
    $answer .= $this->getHtml();

    if ($this->getDynamicModules() != "") {
      $dynamicModules = unserialize($this->getDynamicModules());
      $answerHtml = explode("<dynamic_module/>", $answer);
      $answer = "";
      foreach($answerHtml as $key => $html) {
        if (isset($dynamicModules[$key])) {

          require_once (__DIR__.'/widgets/widget.php');
          require_once (__DIR__.'/widgets/'.$dynamicModules[$key]['module_group'].'/'.$dynamicModules[$key]['module_name'].'/module.php');

          eval('$module = new \\Modules\\standard\\content_management\\Widgets\\'.$dynamicModules[$key]['module_group'].'\\'.$dynamicModules[$key]['module_name'].'\\Module(\''.$dynamicModules[$key]['module_name'].'\');');
          //widgets\standard\content_management\Widgets\misc\contact_form
          $tmpHtml = '';
          eval('$tmpHtml = $module->make_html('.$dynamicModules[$key]['id'].');');
          $answer .= $html.$tmpHtml;
        }else
          $answer .= $html;
      }
      return $answer;

    }else {
      return $answer;
    }


  }


  public function generateManagement() {
    global $parametersMod;
    global $site;

    require_once(__DIR__.'/edit_menu_management.php');


    $management = new EditMenuManagement($this->getId());
    return
            '
<!-- display loading util page is loaded-->
<div id="loading"> 
  <div id="loading_bg"
  style="width:100%; height: 100%; z-index: 999; position: fixed; left: 0; top: 0; filter: alpha(opacity=65); -moz-opacity: 0.65; background-color: #cccccc;">

  </div>
  <div id="loading_text"
  style="height: 60px; width: 100%; position: fixed; left:0px; top: 180px;  z-index: 1001;"
  >
    <table style="margin-left: auto; margin-right: auto;"><tr>
    <td style="font-family: Verdana, Tahoma, Arial; font-size: 14px; color: #505050; padding: 30px 33px; background-color: #eeeeee; border: 1px solid #999999;">
    '.$parametersMod->getValue('standard', 'configuration', 'system_translations', 'loading').'								</td>
    </tr></table>
  </div>
</div>
<script type="text/javascript">
  LibDefault.addEvent(window, \'load\', init);

  function init(){
    document.getElementById(\'loading\').style.display = \'none\';
  }
</script>
<!-- display loading until page is loaded-->
'.$management->manageElement().'
';               
  }


  public function getDynamicModules() {
    return $this->dynamicModules;
  }
  public function setDynamicModules($dynamicModules) {
    $this->dynamicModules=$dynamicModules;
  }

  private function generateDepthAndLink() {
    global $site;
    $tmpUrlVars = array();
    $tmpId = $this->getId();
    $element = DbFrontend::getElement($tmpId);
    while($element['parent'] !== null) {
      $tmpUrlVars[] = $element['url'];
      $element = DbFrontend::getElement($element['parent']);
    }
    $languageId = DbFrontend::languageByRootElement($element['id']);

    $urlVars = array();

    for($i=sizeof($tmpUrlVars)-1; $i >= 0; $i--) // " - 1: eliminating invisible root content element"
    {
      $urlVars[] = $tmpUrlVars[$i];
    }

    $this->depth = sizeof($urlVars);

    switch($this->type) {
      case 'inactive':
        $this->link = '';
        break;
      case 'subpage':
        $tmpChildren = $site->getZone($this->zoneName)->getElements($languageId, $this->id, 0, $limit = 1);
        if(sizeof($tmpChildren) == 1)
          $this->link = $tmpChildren[0]->getLink();
        else
          $this->link = $site->generateUrl($languageId, $this->zoneName, $urlVars);  //open current page if no subpages exist
        break;
      case 'redirect':
        if($site->managementState()) {
          if(strpos($this->redirectUrl, BASE_URL) === 0) {
            if(strpos($this->redirectUrl, 'cms_action=manage') === false) {
              if(strpos($this->redirectUrl, '?') === false) {
                $this->redirectUrl .= '?cms_action=manage';
              } else {
                $this->redirectUrl .= '&cms_action=manage';
              }
            }
          }
        }
        $this->link = $this->redirectUrl;
        break;
      case 'default':
      default:
        $this->link = $site->generateUrl($languageId, $this->zoneName, $urlVars);
        break;
    }

  }
}




