<?php 
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management;   
 

if (!defined('CMS')) exit;

class Config // extends MimeType
{
  static function getMceStyles()
  {
    $tinyMceStyles = array();
    $tinyMceStyles[] = array('translation'=>'Text', 'css_style'=>'');
    $tinyMceStyles[] = array('translation'=>'Caption', 'css_style'=>'caption');
    $tinyMceStyles[] = array('translation'=>'Signature', 'css_style'=>'signature');
    $tinyMceStyles[] = array('translation'=>'Note', 'css_style'=>'note');
    return $tinyMceStyles;
  }


  static function getMceInit($collectionNumber){
    //tinymce styles
    $tinyMceStylesStr = '';
    foreach(self::getMceStyles() as $style){
      if($tinyMceStylesStr != ''){
        $tinyMceStylesStr .= ';';
      }
      $tinyMceStylesStr .= $style['translation'].'='.$style['css_style'];
    }
    //end tinymce styles


    return '

    tinyMCE
    .init( {
      theme : "advanced",
      mode : "exact",
      elements : "management_'.$collectionNumber.'_text",
      plugins : "paste,inlinepopups,iplink",
      theme_advanced_buttons1 : "pastetext,separator,justifyleft,justifycenter,justifyright,separator,undo,redo,separator",
      theme_advanced_buttons2 : "bold,italic,underline,styleselect",
      theme_advanced_buttons3 : "bullist,numlist,outdent,indent,link,unlink,sub,sup",
      theme_advanced_toolbar_location : "top",
      theme_advanced_toolbar_align : "left",
      theme_advanced_statusbar_location : "bottom",
      theme_advanced_resizing : true,
      theme_advanced_resize_horizontal : false,
      valid_elements : "sup[class],sub[class],p[class],b,u,i,a[name|href|target|title|class],ul[class],ol[class],li[class]",
      height : 300,
      content_css : "'.BASE_URL.TEMPLATE_DIR.TEMPLATE.'/ip_content.css",
      theme_advanced_styles : "'.$tinyMceStylesStr.'",
      forced_root_block : "p",

      document_base_url : "'.BASE_URL.'",
      remove_script_host : false,
      relative_urls : false,
      convert_urls : false,

      paste_auto_cleanup_on_paste : true,
      paste_retain_style_properties : false,
      paste_strip_class_attributes : true,
      paste_remove_spans : true,
      paste_remove_styles : true,
      paste_convert_middot_lists : true,

      paste_preprocess : function(pl, o) {
        o.content = o.content.stripScripts();
        var tmpContent = o.content;

        tmpContent = tmpContent.replace(/(<strong>)/ig, "<b>");
        tmpContent = tmpContent.replace(/(<\/strong>)/ig, "</b>");
        o.content = tmpContent;
      },
      paste_postprocess : function(pl, o) {
      }

    });


';
  }
}



