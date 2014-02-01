<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Content\Widget\Gallery;




class Controller extends \Ip\WidgetController{

    public function getTitle() {
        return __('Gallery', 'ipAdmin', false);
    }



    public function update($widgetId, $postData, $currentData) {

        if (isset($postData['method'])) {
            switch($postData['method']) {
                case 'move':
                    if (!isset($postData['originalPosition'])) {
                        throw new \Ip\Exception("Missing required parameter");
                    }
                    $originalPosition = $postData['originalPosition'];
                    if (!isset($postData['newPosition'])) {
                        throw new \Ip\Exception("Missing required parameter");
                    }
                    $newPosition = $postData['newPosition'];

                    if (!isset($currentData['images'][$originalPosition])) {
                        throw new \Ip\Exception("Moved image doesn't exist");
                    }

                    $movedImage = $currentData['images'][$originalPosition];
                    unset($currentData['images'][$originalPosition]);
                    array_splice($currentData['images'], $newPosition, 0, array($movedImage));
                    return $currentData;
                case 'add':
                    if (!isset($postData['images']) || !is_array($postData['images'])) {
                        throw new \Ip\Exception("Missing required parameter");
                    }


                    foreach($postData['images'] as $image){
                        if (!isset($image['fileName']) || !isset($image['status'])){ //check if all required data present
                            continue;
                        }

                        //just to be sure
                        if (!file_exists(ipFile('file/repository/' . $image['fileName']))) {
                            continue;
                        }

                        //bind new image to the widget
                        \Ip\Internal\Repository\Model::bindFile($image['fileName'], 'Content', $widgetId);


                        //find image title
                        if (!empty($image['title'])) {
                            $title = $image['title'];
                        } else {
                            $title = basename($image['fileName']);
                        }

                        $newImage = array(
                            'imageOriginal' => $image['fileName'],
                            'title' => $title,
                        );

                        $currentData['images'][] = $newImage;
                    }

                    return $currentData;
                case 'crop':
                    break;
                case 'update' :

//                    //check if crop coordinates are set
//                    if (!isset($image['cropX1']) || !isset($image['cropY1']) || !isset($image['cropX2']) || !isset($image['cropY2'])) {
//                        break;
//                    }
//
//                    $existingImageData = self::_findExistingImage($image['fileName'], $currentData['images']);
//                    if (!$existingImageData) {
//                        break; //existing image not found. Impossible to recalculate coordinates if image does not exists.
//                    }
//
//                    //find image title
//                    if ($image['title'] == '') {
//                        $title = basename($image['fileName']);
//                    } else {
//                        $title = $image['title'];
//                    }
//
//                    $newImage = array(
//                        'imageOriginal' => $existingImageData['imageOriginal'],
//                        'title' => $title,
//                        'cropX1' => $image['cropX1'],
//                        'cropY1' => $image['cropY1'],
//                        'cropX2' => $image['cropX2'],
//                        'cropY2' => $image['cropY2'],
//                    );
//                    $newData['images'][] = $newImage;
//

                    break;



                case 'delete':
                    if (!isset($postData['position'])) {
                        throw new \Ip\Exception("Missing required parameter");
                    }
                    $deletePosition = (int)$postData['position'];

                    unset($currentData['images'][$deletePosition]);
                    $currentData['images'] = array_values($currentData['images']); // 'reindex' array
                    return $currentData;
                default:
                    throw new \Ip\Exception('Unknown command');

            }

        }


        $newData = $currentData;
        $newData['images'] = array(); //we will create new images array.



        //check if images array is set
        if (!isset($postData['images']) || !is_array($postData['images'])) {
            return $newData;
        }

        foreach($postData['images'] as $image){
            if (!isset($image['fileName']) || !isset($image['status'])){ //check if all required data present
                continue;
            }

            switch($image['status']){


            }
        }


        //delete images that does not exist in posted array
        //Usually it should not happen ever. But just in case we are checking it and deleting unused images.
        if (isset($currentData['images']) && is_array($currentData['images'])) {
            //loop all current images
            foreach ($currentData['images'] as $curImage) {
                if (!$this->_findExistingImage($curImage, $widgetId)) {
                    $this->_deleteOneImage($curImage, $widgetId);
                }
            }
        }


        return $newData;
    }


    public function adminHtmlSnippet()
    {
        $snippets = array();
        return ipView('snippet/gallery.php')->render();

    }



    private function _findExistingImage ($imageOriginalFile, $allImages) {

        if (!is_array($allImages)) {
            return false;
        }

        $answer = false;
        foreach ($allImages as $imageKey => $image) {
            if (isset($image['imageOriginal']) && $image['imageOriginal'] == $imageOriginalFile) {
                $answer = $image;
                break;
            }
        }

        return $answer;

    }




    public function generateHtml($revisionId, $widgetId, $instanceId, $data, $skin)
    {
        $reflectionService = \Ip\Internal\Repository\ReflectionService::instance();

        if (isset($data['images']) && is_array($data['images'])) {
            //loop all current images
            foreach ($data['images'] as $curImageKey => &$curImage) {
                if (empty($curImage['imageOriginal'])) {
                    continue;
                }
                $desiredName = isset($curImage['title']) ? $curImage['title'] : '';

                //create big image reflection
                $bigWidth = ipGetOption('Config.lightboxWidth');
                $bigHeight = ipGetOption('Config.lightboxHeight');

                try {
                    $transformBig = new \Ip\Internal\Repository\Transform\ImageFit($bigWidth, $bigHeight);
                    $curImage['imageBig'] = ipFileUrl('file/' . $reflectionService->getReflection($curImage['imageOriginal'], $desiredName, $transformBig));
                } catch (\Ip\Internal\Repository\TransformException $e) {
                    //do nothing
                } catch (\Ip\Internal\Repository\Exception $e) {
                    //do nothing
                }



                try {
                    if (isset($curImage['cropX1']) && isset($curImage['cropY1']) && isset($curImage['cropX2']) && isset($curImage['cropY2']) ) {
                        $transformSmall = new \Ip\Internal\Repository\Transform\ImageCrop(
                            $curImage['cropX1'],
                            $curImage['cropY1'],
                            $curImage['cropX2'],
                            $curImage['cropY2'],
                            ipGetOption('Content.widgetGalleryWidth'),
                            ipGetOption('Content.widgetGalleryHeight'),
                            ipGetOption('Content.widgetGalleryQuality')
                        );

                    } else {
                        $transformSmall = new \Ip\Internal\Repository\Transform\ImageCropCenter(
                            ipGetOption('Content.widgetGalleryWidth'),
                            ipGetOption('Content.widgetGalleryHeight'),
                            ipGetOption('Content.widgetGalleryQuality')
                        );

                    }
                    $curImage['imageSmall'] = ipFileUrl('file/' . $reflectionService->getReflection($curImage['imageOriginal'], $curImage['title'], $transformSmall));
                } catch (\Ip\Internal\Repository\TransformException $e) {
                    //do nothing
                } catch (\Ip\Internal\Repository\Exception $e) {
                    //do nothing
                }

            }
        }
        return parent::generateHtml($revisionId, $widgetId, $instanceId, $data, $skin);
    }


    public function delete($widgetId, $data) {
        if (!isset($data['images']) || !is_array($data['images'])) {
            return;
        }

        foreach($data['images'] as $imageKey => $image) {
            self::_deleteOneImage($image, $widgetId);
        };
    }

    private function _deleteOneImage($image, $widgetId) {
        if (!is_array($image)) {
            return;
        }
        if (isset($image['imageOriginal']) && $image['imageOriginal']) {
            \Ip\Internal\Repository\Model::unbindFile($image['imageOriginal'], 'Content', $widgetId);
        }
    }





    /**
    *
    * Duplicate widget action. This function is executed after the widget is being duplicated.
    * All widget data is duplicated automatically. This method is used only in case a widget
    * needs to do some maintenance tasks on duplication.
    * @param int $oldId old widget id
    * @param int $newId duplicated widget id
    * @param array $data data that has been duplicated from old widget to the new one
    */
    public function duplicate($oldId, $newId, $data) {
        if (!isset($data['images']) || !is_array($data['images'])) {
            return;
        }

        foreach($data['images'] as $imageKey => $image) {
            if (!is_array($image)) {
                return;
            }
            if (isset($image['imageOriginal']) && $image['imageOriginal']) {
                \Ip\Internal\Repository\Model::bindFile($image['imageOriginal'], 'Content', $newId);
            }
        }

    }

}
