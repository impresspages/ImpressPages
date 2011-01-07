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
      valid_elements : "@[class|style],strong,em,br,sup,sub,p,span,b,u,i,a[name|href|target|title],ul,ol,li",
      file_browser_callback : "simplebrowser_browse",
      width: "100%",
      height: "300px",
      content_css : "'.BASE_URL.THEME_DIR.THEME.'/ip_content.css",
      theme_advanced_styles : "'.$tinyMceStylesStr.'",
      forced_root_block : "p",

      document_base_url : "'.BASE_URL.'",
      remove_script_host : false,
      relative_urls : false,
      convert_urls : true,


      paste_auto_cleanup_on_paste : true,
      paste_retain_style_properties : false,
      paste_strip_class_attributes : false,
      paste_remove_spans : false,
      paste_remove_styles : false,
      paste_convert_middot_lists : true,

      paste_preprocess : function(pl, o) {
        o.content = o.content.stripScripts();
        var tmpContent = o.content;
        var classesArray = new Array ('.$classesArray.');


        tmpContent = tmpContent.replace(/(<strong>)/ig, "<b>"); /*replace strong with bold*/
        tmpContent = tmpContent.replace(/(<\\/strong>)/ig, "</b>");

        /* remove unknown classes */
        var pattern = /<[^<>]+class="[^"]+"[^<>]*>/gi; /* find all tags containing classes */
        var matches = tmpContent.match(pattern);
        for(var i =0; matches && i < matches.length; i++){ /* loop through found tags */
          var pattern2 = /class="[^"]+"/gi;  /* find class name */
          var matches2 = matches[i].match(pattern2);
          for(var i2 = 0; matches2 && i2 < matches2.length; i2++){ /* throw away unknown classes */
            var classExist = false;
            for(var classKey = 0; classKey < classesArray.length; classKey ++){
              if(\'class="\' + classesArray[classKey] + \'"\' == matches2[i2]){
                classExist = true;
              }
            }

            if(!classExist){
              tmpContent = tmpContent.replace(matches2[i2], "");
            }
          }
        }


        /* remove unknown inline styles */
        var styles = new Array("text-align: right;", "text-align: left;", "text-align: justify;");
        var pattern = /<[^<>]+style="[^"]+"[^<>]*>/gi; /* find all tags containing inline styles */
        var matches = tmpContent.match(pattern);
        for(var i =0; matches && i < matches.length; i++){ /* loop through found tags */
          var pattern2 = /style="[^"]+"/gi;  /* find style */
          var matches2 = matches[i].match(pattern2);
          for(var i2 = 0; matches2 && i2 < matches2.length; i2++){ /* throw away unknown inline styles */
            var styleExist = false;
            for(var styleKey = 0; styleKey < styles.length; styleKey ++){
              if(\'style="\' + styles[styleKey] + \'"\' == matches2[i2]){
                styleExist = true;
              }
            }

            if(!styleExist){
              tmpContent = tmpContent.replace(matches2[i2], "");
            }
          }
        }

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
      valid_elements : "@[class|style],strong,em,br,sup,sub,p,span,b,u,i,a[name|href|target|title],ul,ol,li",
      file_browser_callback : "simplebrowser_browse",
      width: "500px",
      height: "300px",
      content_css : "'.BASE_URL.THEME_DIR.THEME.'/ip_content.css",
      theme_advanced_styles : "'.$tinyMceStylesStr.'",
      forced_root_block : "p",

      document_base_url : "'.BASE_URL.'",
      remove_script_host : false,
      relative_urls : false,
      convert_urls : true,


      paste_auto_cleanup_on_paste : true,
      paste_retain_style_properties : false,
      paste_strip_class_attributes : false,
      paste_remove_spans : false,
      paste_remove_styles : false,
      paste_convert_middot_lists : true,

      paste_preprocess : function(pl, o) {
        o.content = o.content.stripScripts();
        var tmpContent = o.content;
        var classesArray = new Array ('.$classesArray.');


        tmpContent = tmpContent.replace(/(<strong>)/ig, "<b>"); /*replace strong with bold*/
        tmpContent = tmpContent.replace(/(<\\/strong>)/ig, "</b>");

        /* remove unknown classes */
        var pattern = /<[^<>]+class="[^"]+"[^<>]*>/gi; /* find all tags containing classes */
        var matches = tmpContent.match(pattern);
        for(var i =0; matches && i < matches.length; i++){ /* loop through found tags */
          var pattern2 = /class="[^"]+"/gi;  /* find class name */
          var matches2 = matches[i].match(pattern2);
          for(var i2 = 0; matches2 && i2 < matches2.length; i2++){ /* throw away unknown classes */
            var classExist = false;
            for(var classKey = 0; classKey < classesArray.length; classKey ++){
              if(\'class="\' + classesArray[classKey] + \'"\' == matches2[i2]){
                classExist = true;
              }
            }

            if(!classExist){
              tmpContent = tmpContent.replace(matches2[i2], "");
            }
          }
        }


        /* remove unknown inline styles */
        var styles = new Array("text-align: right;", "text-align: left;", "text-align: justify;");
        var pattern = /<[^<>]+style="[^"]+"[^<>]*>/gi; /* find all tags containing inline styles */
        var matches = tmpContent.match(pattern);
        for(var i =0; matches && i < matches.length; i++){ /* loop through found tags */
          var pattern2 = /style="[^"]+"/gi;  /* find style */
          var matches2 = matches[i].match(pattern2);
          for(var i2 = 0; matches2 && i2 < matches2.length; i2++){ /* throw away unknown inline styles */
            var styleExist = false;
            for(var styleKey = 0; styleKey < styles.length; styleKey ++){
              if(\'style="\' + styles[styleKey] + \'"\' == matches2[i2]){
                styleExist = true;
              }
            }

            if(!styleExist){
              tmpContent = tmpContent.replace(matches2[i2], "");
            }
          }
        }

        o.content = tmpContent;

      },
      paste_postprocess : function(pl, o) {
      }

    });
';

  }

}



