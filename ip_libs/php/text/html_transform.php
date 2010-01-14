<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Library\Php\Text;

/**
 * replaces special characters in a string
 * @package Library
 */
class HtmlTransform
{
  public static function prepareLink($link){
    return wordwrap(str_replace('&amp;', '&', $link), 20, "<wbr/>", true);    
  }
  
}