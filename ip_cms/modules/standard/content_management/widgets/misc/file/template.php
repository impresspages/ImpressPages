<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\misc\file;   

if (!defined('CMS')) exit;
class Template {

  public static function generateHtml($title, $file, $layout = null) {
    switch($layout) {
      default:
      case "default":
        if($title == '') {
          $title = basename($file);
        }
        return '
<div class="ipWidget ipWidgetFile">
  <a class="ipWidgetFileLink" href="'.$file.'">'.htmlspecialchars($title).'</a>
</div>
';
        break;
    }
  }

}

