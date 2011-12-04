<?php

/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\standard\menu_management;


if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

require_once(__DIR__.'/db.php');
require_once (BASE_DIR.MODULE_DIR.'standard/content_management/db.php');
require_once (BASE_DIR.LIBRARY_DIR.'php/file/upload_file.php');
require_once (BASE_DIR.LIBRARY_DIR.'php/file/upload_image.php');
require_once (BASE_DIR.MODULE_DIR.'standard/content_management/widgets/widget.php');

$tmpModules = \Modules\standard\content_management\Db::menuModules();

foreach($tmpModules as $groupKey => $group) {
    foreach ($group as $moduleKey => $module){
        require_once (BASE_DIR.MODULE_DIR.'standard/content_management/widgets/'.$module['group_name'].'/'.$module['module_name'].'/module.php');
    }
}


class Model {



    public static function deletePage ($pageId) {
        self::_deletePageRecursion($pageId);
        return true;
    }


    private static function _deletePageRecursion ($id) {
        $children = Db::pageChildren($id);
        if ($children) {
            foreach($children as $key => $lock) {
                self::_deletePageRecursion($lock['id']);
            }
        }

        //delete paragraphs
        $widgets = Db::pageWidgets($id);
        foreach($widgets as $key => $lock) {
            eval(' $tmp_module = new \\Modules\\standard\\content_management\\Widgets\\'.$lock['group_key'].'\\'.$lock['module_key'].'\\Module(); ');
            $tmp_module->delete_by_id($lock['module_id']);
        }
        //end delete paragraphs

        Db::deletePage($id);

    }


    /**
     *
     * Copy page
     * @param unknown_type $nodeId
     * @param unknown_type $newParentId
     * @param int $position page position in the subtree
     */
    public static function copyPage($nodeId, $destinationPageId, $position){

        $children = Db::pageChildren($destinationPageId);

        if (!empty($children)) {
            $rowNumber = $children[count($children) - 1]['row_number'] + 1;
        } else {
            $rowNumber = 0;
        }


        self::_copyPageRecursion($nodeId = $nodeId, $destinationPageId = $destinationPageId, $rowNumber = $rowNumber);

        $contentManagementSystem = new \Modules\standard\content_management\System();
        $contentManagementSystem->clearCache(BASE_URL);
    }

    /**
     *
     * Copy page internal recursion
     * @param unknown_type $nodeId
     * @param unknown_type $destinationPageId
     * @param unknown_type $newIndex
     * @param unknown_type $newPages
     */
    private static function _copyPageRecursion ($nodeId, $destinationPageId, $rowNumber, $newPages = null) {
        //$newPages are the pages that have been copied already and should be skiped to duplicate again. This situacion can occur when copying the page to it self
        if($newPages == null){
            $newPages = array();
        }
        $newNodeId = Db::copyPage($nodeId, $destinationPageId, $rowNumber);
        $newPages[$newNodeId] = 1;
        self::_copyWidgets($nodeId, $newNodeId);


        $children = Db::pageChildren($nodeId);
        if($children){
            foreach($children as $key => $lock){
                if(!isset($newPages[$lock['id']])){
                    self::_copyPageRecursion($lock['id'], $newNodeId, $key, $newPages);
                }
            }
        }

    }

    private static function _copyWidgets($sourceId, $targetId){

        $sourceWidgets = Db::pageWidgets($sourceId);

        $position = 0;
        foreach($sourceWidgets as $key => $widget){
            $widgetData = self::getWidgetData($widget);
            self::addWidget($targetId, $widgetData, $widget);
        }

    }


    public static function getWidgetData ($widget) {
        require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/'.$widget['group_key'].'/'.$widget['module_key'].'/module.php');
        eval ('$widgetObject = new \\Modules\\standard\\content_management\\Widgets\\'.$widget['group_key'].'\\'.$widget['module_key'].'\\Module(); ');
        $answer = $widgetObject->getData($widget['module_id']);
        return $answer;
    }

    public static function addWidget($targetId, $widgetData, $widget){

        $values = $widgetData;

        $values['content_element_id'] = $targetId;
        $values['row_number'] = $widget['row_number'];
        $values['visible'] = $widget['visible'];

        switch($widget['group_key'].'/'.$widget['module_key']){

            case 'text_photos/photo':
                $values['new_photo'] = basename($values['photo']);
                $values['new_bigphoto'] = basename($values['photo_big']);
                copy($values['photo'], BASE_DIR.TMP_IMAGE_DIR.basename($values['photo']));
                copy($values['photo_big'], BASE_DIR.TMP_IMAGE_DIR.basename($values['photo_big']));
                break;
            case 'text_photos/text_photo':
                $values['new_photo'] = basename($values['photo']);
                $values['new_bigphoto'] = basename($values['photo_big']);
                copy($values['photo'], BASE_DIR.TMP_IMAGE_DIR.basename($values['photo']));
                copy($values['photo_big'], BASE_DIR.TMP_IMAGE_DIR.basename($values['photo_big']));
                break;
            case 'misc/file':
                $values['new_photo'] = basename($values['photo']);
                copy($values['photo'], BASE_DIR.TMP_FILE_DIR.basename($values['photo']));
                break;
            case 'misc/video':
                $values['new_photo'] = basename($values['photo']);
                copy($values['photo'], BASE_DIR.TMP_VIDEO_DIR.basename($values['photo']));
                break;
        }



        require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/'.$widget['group_key'].'/'.$widget['module_key'].'/module.php');
        eval ('$widgetObject = new \\Modules\\standard\\content_management\\Widgets\\'.$widget['group_key'].'\\'.$widget['module_key'].'\\Module();');
        $widgetObject->create_new_instance($values);


        switch ($widget['group_key'].'/'.$widget['module_key']) {
            case 'text_photos/logo_gallery':
                $sqlMax = "select max(id) as max_id from `".DB_PREF."mc_text_photos_logo_gallery` where 1";
                $rsMax = mysql_query($sqlMax);
                if(!$rsMax){
                    trigger_error($sqlMax.' '.mysql_error());
                }
                $lockMax = mysql_fetch_assoc($rsMax);
                $galleryId = $lockMax['max_id'];
                foreach($values['logos'] as $logoKey => $logo){
                    $tmpValues = array();
                    copy($logo['logo'], BASE_DIR.TMP_IMAGE_DIR.basename($logo['logo']));
                    $tmpValues['new_photo1'] = basename($logo['logo']);
                    $tmpValues['title1'] = $logo['link'];
                    $widgetObject->insert_photo($galleryId, 1, $tmpValues);
                }
                break;
            case 'text_photos/photo_gallery':

                $sqlMax = "select max(id) as max_id from `".DB_PREF."mc_text_photos_photo_gallery` where 1";
                $rsMax = mysql_query($sqlMax);
                if(!$rsMax){
                    trigger_error($sqlMax.' '.mysql_error());
                }
                $lockMax = mysql_fetch_assoc($rsMax);
                $galleryId = $lockMax['max_id'];


                foreach($values['photos'] as $photoKey => $photo){
                    $tmpValues = array();
                    copy($photo['photo'], BASE_DIR.TMP_IMAGE_DIR.basename($photo['photo']));
                    copy($photo['photo_big'], BASE_DIR.TMP_IMAGE_DIR.basename($photo['photo_big']));
                    $tmpValues['new_photo1'] = basename($photo['photo']);
                    $tmpValues['new_bigphoto1'] = basename($photo['photo_big']);
                    $tmpValues['title1'] = $photo['title'];
                    $widgetObject->insert_photo($galleryId, 1, $tmpValues);
                }
                break;


        }

    }

}