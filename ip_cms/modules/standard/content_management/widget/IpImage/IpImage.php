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
require_once(BASE_DIR.LIBRARY_DIR.'php/image/functions.php');

class IpImage extends \Modules\standard\content_management\Widget{



    public function prepareData($instanceId, $postData, $currentData) {
        global $parametersMod;
        $answer = '';


        $destinationDir = BASE_DIR.IMAGE_DIR;

        $newData = $currentData;
        $newData['imageWindowWidth'] = $postData['imageWindowWidth'];

        if (isset($postData['newImage']) && file_exists(BASE_DIR.$postData['newImage']) && is_file(BASE_DIR.$postData['newImage'])) {

            if (TMP_FILE_DIR.basename($postData['newImage']) != $postData['newImage']) {
                throw new \Exception("Security notice. Try to access an image (".$postData['newImage'].") from a non temporary folder.");
            }

            //new original image
            $unocupiedName = \Library\Php\File\Functions::genUnocupiedName($postData['newImage'], $destinationDir);
            copy($postData['newImage'], $destinationDir.$unocupiedName);
            $newData['imageOriginal'] = IMAGE_DIR.$unocupiedName;



            $bigImageName = \Library\Php\Image\Functions::resize(
            $postData['newImage'],
            $parametersMod->getValue('standard', 'content_management', 'widget_photo', 'big_width'),
            $parametersMod->getValue('standard', 'content_management', 'widget_photo', 'big_height'),
            BASE_DIR.IMAGE_DIR,
            \Library\Php\Image\Functions::CROP_TYPE_FIT,
            false,
            $parametersMod->getValue('standard', 'content_management', 'widget_photo', 'big_quality')
            );
            $newData['imageBig'] = IMAGE_DIR.$bigImageName;
        }

        if (isset($postData['cropX1']) && isset($postData['cropY1']) && isset($postData['cropX2']) && isset($postData['cropY2']) && isset($postData['scale']) ) {
            //new small image
            $ratio = ($postData['cropX2'] - $postData['cropX1']) / ($postData['cropY2'] - $postData['cropY1']);
            $requiredWidth = round($parametersMod->getValue('standard', 'content_management', 'widget_photo', 'width') * $postData['scale']);
            $requiredHeight = round($requiredWidth / $ratio);
            $smallImageName = \Library\Php\Image\Functions::crop (
            $newData['imageOriginal'],
            $destinationDir,
            $postData['cropX1'],
            $postData['cropY1'],
            $postData['cropX2'],
            $postData['cropY2'],
            $parametersMod->getValue('standard', 'content_management', 'widget_photo', 'quality'),
            $requiredWidth,
            $requiredHeight
            );
            $newData['imageSmall'] = IMAGE_DIR.$smallImageName;
            $newData['scale'] = $postData['scale'];
            $newData['cropX1'] = $postData['cropX1'];
            $newData['cropY1'] = $postData['cropY1'];
            $newData['cropX2'] = $postData['cropX2'];
            $newData['cropY2'] = $postData['cropY2'];

        }



        if (isset($postData['title'])) {
            $newData['title'] = $postData['title'];
        }

        return $newData;
    }

    public function delete($widgetId, $data) {
/*        if ($data['imageOriginal']) {
            unlink(BASE_DIR.$data['imageOriginal']);
        }
        if ($data['imageSmall']) {
            unlink(BASE_DIR.$data['imageSmall']);
        }
        if ($data['imageBig']) {
            unlink(BASE_DIR.$data['imageBig']);
        }*/
    }
   



}