<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */

namespace Modules\developer\std_mod;


if (!defined('CMS')) exit;

class Config // extends MimeType
{
  static function getMceStyles()
  {
    global $site;
    $site->requireConfig('standard/content_management/config.php');
    return \Modules\standard\content_management\Config::getMceStyles();
  }

  static function getMceInit($mode, $selector, $purpose){
    switch($purpose){
      case 'frontend':
        return self::frontendMceInit($mode, $selector);
        break;
      case 'backend':
      default:
        return self::backendMceInit($mode, $selector);
        break;
    }
  }

  static function frontendMceInit($mode, $selector){
    //tinymce styles
    $tinyMceStylesStr = '';
    $classesArray = '';
    foreach(self::getMceStyles() as $style){
      if($tinyMceStylesStr != ''){
        $tinyMceStylesStr .= ';';
      }
      $tinyMceStylesStr .= $style['translation'].'='.$style['css_style'];

      if($style['css_style'] != ''){
        if($classesArray != ''){
          $classesArray .= ',';
        }
        $classesArray .= '"'.$style['css_style'].'"';
      }

    }
    //end tinymce styles



    return '
    tinyMCE.init( {
      theme : "advanced",
      '.$mode.',
      '.$selector.',
      plugins : "paste,simplebrowser,advlink,advimage,inlinepopups,iplink",
      theme_advanced_buttons1 : "pastetext,separator,justifyleft,justifycenter,justifyright,separator,undo,redo,separator",
      theme_advanced_buttons2 : "bold,italic,underline,styleselect,image",
      theme_advanced_buttons3 : "bullist,numlist,outdent,indent,link,unlink,sub,sup,code",
      theme_advanced_toolbar_location : "top",
      theme_advanced_toolbar_align : "left",
      theme_advanced_statusbar_location : "bottom",
      theme_advanced_resizing : true,
      extended_valid_elements : "style",
      file_browser_callback : "simplebrowser_browse",
      width: "100%",
      height: "300px",
      content_css : "'.BASE_URL.THEME_DIR.THEME.'/ip_content.css",
      theme_advanced_styles : "'.$tinyMceStylesStr.'",
      forced_root_block : "p",

      document_base_url : "'.BASE_URL.'",
      remove_script_host : true,
      relative_urls : false,
      convert_urls : false,

      paste_auto_cleanup_on_paste : true,
      paste_retain_style_properties: false,
      paste_strip_class_attributes: true,
      paste_remove_spans: true,
      paste_remove_styles: true,
      paste_convert_middot_lists: true,

      paste_preprocess : function(pl, o) {
        o.content = o.content.stripScripts();
        var tmpContent = o.content;
        tmpContent = tmpContent.replace(new RegExp(\'<!(?:--[\\s\\S]*?--\s*)?>\', \'g\'), \'\') //remove comments
        tmpContent = tmpContent.replace(/(<([^>]+)>)/ig,"</p><p>");
        tmpContent = tmpContent.replace(/\n/ig," "); //remove newlines
        tmpContent = tmpContent.replace(/\r/ig," "); //remove newlines
        tmpContent = tmpContent.replace(/[\t]+/ig," "); //remove tabs
        tmpContent = tmpContent.replace(/[ ]+/ig," ");  //remove multiple spaces
        tmpContent = tmpContent.replace(/(<\/p><p>([ ]*(<\/p><p>)*[ ]*)*<\/p><p>)/ig, "</p><p>"); //remove multiple paragraphs
        o.content = tmpContent;
      },
      paste_postprocess : function(pl, o) {

      }

    });
';

  }

  static function backendMceInit($mode, $selector){
    //tinymce styles
    $tinyMceStylesStr = '';
    $classesArray = '';
    foreach(self::getMceStyles() as $style){
      if($tinyMceStylesStr != ''){
        $tinyMceStylesStr .= ';';
      }
      $tinyMceStylesStr .= $style['translation'].'='.$style['css_style'];

      if($style['css_style'] != ''){
        if($classesArray != ''){
          $classesArray .= ',';
        }
        $classesArray .= '"'.$style['css_style'].'"';
      }

    }
    //end tinymce styles



    return '
    tinyMCE.init( {
      theme : "advanced",
      '.$mode.',
      '.$selector.',
      plugins : "paste,simplebrowser,advlink,advimage,inlinepopups,iplink",
      theme_advanced_buttons1 : "pastetext,separator,justifyleft,justifycenter,justifyright,separator,undo,redo,separator",
      theme_advanced_buttons2 : "bold,italic,underline,styleselect,image",
      theme_advanced_buttons3 : "bullist,numlist,outdent,indent,link,unlink,sub,sup,code",
      theme_advanced_toolbar_location : "top",
      theme_advanced_toolbar_align : "left",
      theme_advanced_statusbar_location : "bottom",
      theme_advanced_resizing : true,
      extended_valid_elements : "style",
      file_browser_callback : "simplebrowser_browse",
      width: "500px",
      height: "300px",
      content_css : "'.BASE_URL.THEME_DIR.THEME.'/ip_content.css",
      theme_advanced_styles : "'.$tinyMceStylesStr.'",
      forced_root_block : "p",

      document_base_url : "'.BASE_URL.'",
      remove_script_host : true,
      relative_urls : false,
      convert_urls : false,

      paste_auto_cleanup_on_paste : true,
      paste_retain_style_properties: false,
      paste_strip_class_attributes: true,
      paste_remove_spans: true,
      paste_remove_styles: true,
      paste_convert_middot_lists: true,

      paste_preprocess : function(pl, o) {
        o.content = o.content.stripScripts();
        var tmpContent = o.content;

        tmpContent = tmpContent.replace(new RegExp(\'<!(?:--[\\s\\S]*?--\s*)?>\', \'g\'), \'\') //remove comments
        tmpContent = tmpContent.replace(/(<([^>]+)>)/ig,"</p><p>");
        tmpContent = tmpContent.replace(/\n/ig," "); //remove newlines
        tmpContent = tmpContent.replace(/\r/ig," "); //remove newlines
        tmpContent = tmpContent.replace(/[\t]+/ig," "); //remove tabs
        tmpContent = tmpContent.replace(/[ ]+/ig," ");  //remove multiple spaces
        tmpContent = tmpContent.replace(/(<\/p><p>([ ]*(<\/p><p>)*[ ]*)*<\/p><p>)/ig, "</p><p>"); //remove multiple paragraphs

        o.content = tmpContent;
      },
      paste_postprocess : function(pl, o) {

      }

    });
';

  }

}



