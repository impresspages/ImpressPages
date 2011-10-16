<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management\widget;

if (!defined('CMS')) exit;

require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widget.php');
require_once(BASE_DIR.LIBRARY_DIR.'php/file/functions.php');

class ipPicture extends \Modules\standard\content_management\Widget{


    
    public function prepareData($instanceId, $postData, $currentData) {
        $answer = '';
        
        $newData = $currentData;
        
        if (isset($postData['newPicture']) && file_exists(BASE_DIR.$postData['newPicture']) && is_file(BASE_DIR.$postData['newPicture'])) {
            if (isset($currentData['picture']) && file_exists(BASE_DIR.$currentData['picture']) && is_file(BASE_DIR.$currentData['picture'])) {
                unlink(BASE_DIR.$currentData['picture']);
            }
            
            $unocupiedName = \Library\Php\File\Functions::genUnocupiedName($postData['newPicture'], BASE_DIR.IMAGE_DIR);
            $newData['picture'] = IMAGE_DIR.$unocupiedName;    
        }
        return $newData;  
    }
    
    

    
}