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
        foreach($sourceWidgets as $key => $value){
            if (self::_copyWidget($sourceId, $targetId, $value, $position)){
                $position++;
            }

        }

    }

    private static function _copyWidget($sourceId, $targetId, $widget, $position){
        switch($widget['group_key'].'/'.$widget['module_key']){

            case 'text_photos/faq':
                $sql = "select * from `".DB_PREF."mc_text_photos_faq` where `id` = '".(int)$widget['module_id']."' ";
                $rs = mysql_query($sql);
                if($rs){
                    $lock = mysql_fetch_assoc($rs);
                    $values = $lock;
                    $values['row_number'] = $position;
                    $values['content_element_id'] = $targetId;
                    $values['visible'] = $widget['visible'];
                    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/faq/module.php');
                    $widget = new \Modules\standard\content_management\Widgets\text_photos\faq\Module();
                    $widget->create_new_instance($values);
                } else {
                    trigger_error($sql.' '.mysql_error());
                }
                return true;
                break;
            case 'text_photos/logo_gallery':
                $sql = "select * from `".DB_PREF."mc_text_photos_logo_gallery` where `id` = '".(int)$widget['module_id']."' ";
                $rs = mysql_query($sql);
                if($rs){
                    $lock = mysql_fetch_assoc($rs);
                    $values = $lock;
                    $values['row_number'] = $position;
                    $values['content_element_id'] = $targetId;
                    $values['visible'] = $widget['visible'];
                    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/logo_gallery/module.php');
                    $widget = new \Modules\standard\content_management\Widgets\text_photos\logo_gallery\Module();
                    $widget->create_new_instance($values);

                    $sqlMax = "select max(id) as max_id from `".DB_PREF."mc_text_photos_logo_gallery` where 1";
                    $rsMax = mysql_query($sqlMax);
                    if(!$rsMax){
                        trigger_error($sqlMax.' '.mysql_error());
                    }
                    $lockMax = mysql_fetch_assoc($rsMax);
                    $galleryId = $lockMax['max_id'];


                    $sql2 = "select * from `".DB_PREF."mc_text_photos_logo_gallery_logo` where `logo_gallery` = '".(int)$lock['id']."' order by `row_number` ";
                    $rs2 = mysql_query($sql2);
                    if($rs2){
                        while($logo = mysql_fetch_assoc($rs2)){
                            $values = array();
                            copy(BASE_DIR.IMAGE_DIR.$logo['logo'], BASE_DIR.TMP_IMAGE_DIR.$logo['logo']);
                            $values['new_photo1'] = $logo['logo'];
                            $values['title1'] = $logo['link'];
                            $widget->insert_photo($galleryId, 1, $values);
                        }
                    } else {
                        trigger_error($sql2.' '.mysql_error());
                    }
                } else {
                    trigger_error($sql.' '.mysql_error());
                }
                return true;
                break;
            case 'text_photos/photo':
                $sql = "select * from `".DB_PREF."mc_text_photos_photo` where `id` = '".(int)$widget['module_id']."' ";
                $rs = mysql_query($sql);
                if($rs){
                    $lock = mysql_fetch_assoc($rs);
                    $values = $lock;
                    $values['row_number'] = $position;
                    $values['content_element_id'] = $targetId;
                    $values['visible'] = $widget['visible'];
                    $values['new_photo'] = $values['photo'];
                    $values['new_bigphoto'] = $values['photo_big'];

                    copy(BASE_DIR.IMAGE_DIR.$values['photo'], BASE_DIR.TMP_IMAGE_DIR.$values['photo']);
                    copy(BASE_DIR.IMAGE_DIR.$values['photo_big'], BASE_DIR.TMP_IMAGE_DIR.$values['photo_big']);

                    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/photo/module.php');
                    $widget = new \Modules\standard\content_management\Widgets\text_photos\photo\Module();
                    $widget->create_new_instance($values);
                } else {
                    trigger_error($sql.' '.mysql_error());
                }
                return true;
                break;
            case 'text_photos/photo_gallery':
                $sql = "select * from `".DB_PREF."mc_text_photos_photo_gallery` where `id` = '".(int)$widget['module_id']."' ";
                $rs = mysql_query($sql);
                if($rs){
                    $lock = mysql_fetch_assoc($rs);
                    $values = $lock;
                    $values['row_number'] = $position;
                    $values['content_element_id'] = $targetId;
                    $values['visible'] = $widget['visible'];
                    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/photo_gallery/module.php');
                    $widget = new \Modules\standard\content_management\Widgets\text_photos\photo_gallery\Module();
                    $widget->create_new_instance($values);

                    $sqlMax = "select max(id) as max_id from `".DB_PREF."mc_text_photos_photo_gallery` where 1";
                    $rsMax = mysql_query($sqlMax);
                    if(!$rsMax){
                        trigger_error($sqlMax.' '.mysql_error());
                    }
                    $lockMax = mysql_fetch_assoc($rsMax);
                    $galleryId = $lockMax['max_id'];


                    $sql2 = "select * from `".DB_PREF."mc_text_photos_photo_gallery_photo` where `photo_gallery` = '".(int)$lock['id']."' order by `row_number` ";
                    $rs2 = mysql_query($sql2);
                    if($rs2){
                        while($logo = mysql_fetch_assoc($rs2)){
                            $values = array();
                            copy(BASE_DIR.IMAGE_DIR.$logo['photo'], BASE_DIR.TMP_IMAGE_DIR.$logo['photo']);
                            copy(BASE_DIR.IMAGE_DIR.$logo['photo_big'], BASE_DIR.TMP_IMAGE_DIR.$logo['photo_big']);
                            $values['new_photo1'] = $logo['photo'];
                            $values['new_bigphoto1'] = $logo['photo_big'];
                            $values['title1'] = $logo['title'];
                            $widget->insert_photo($galleryId, 1, $values);
                        }
                    } else {
                        trigger_error($sql2.' '.mysql_error());
                    }
                } else {
                    trigger_error($sql.' '.mysql_error());
                }
                return true;
                break;
            case 'text_photos/separator':
                $sql = "select * from `".DB_PREF."mc_text_photos_separator` where `id` = '".(int)$widget['module_id']."' ";
                echo $sql;
                $rs = mysql_query($sql);
                if($rs){
                    $lock = mysql_fetch_assoc($rs);
                    $values = $lock;
                    $values['row_number'] = $position;
                    $values['content_element_id'] = $targetId;
                    $values['visible'] = $widget['visible'];
                    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/separator/module.php');
                    $widget = new \Modules\standard\content_management\Widgets\text_photos\separator\Module();
                    $widget->create_new_instance($values);
                } else {
                    trigger_error($sql.' '.mysql_error());
                }
                return true;
                break;
            case 'text_photos/table':
                $sql = "select * from `".DB_PREF."mc_text_photos_table` where `id` = '".(int)$widget['module_id']."' ";
                $rs = mysql_query($sql);
                if($rs){
                    $lock = mysql_fetch_assoc($rs);
                    $values = $lock;
                    $values['row_number'] = $position;
                    $values['content_element_id'] = $targetId;
                    $values['visible'] = $widget['visible'];
                    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/table/module.php');
                    $widget = new \Modules\standard\content_management\Widgets\text_photos\table\Module();
                    $widget->create_new_instance($values);
                } else {
                    trigger_error($sql.' '.mysql_error());
                }
                return true;
                break;                
            case 'text_photos/text':
                $sql = "select * from `".DB_PREF."mc_text_photos_text` where `id` = '".(int)$widget['module_id']."' ";
                $rs = mysql_query($sql);
                if($rs){
                    $lock = mysql_fetch_assoc($rs);
                    $values = $lock;
                    $values['row_number'] = $position;
                    $values['content_element_id'] = $targetId;
                    $values['visible'] = $widget['visible'];
                    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text/module.php');
                    $widget = new \Modules\standard\content_management\Widgets\text_photos\text\Module();
                    $widget->create_new_instance($values);
                } else {
                    trigger_error($sql.' '.mysql_error());
                }
                return true;
                break;

            case 'text_photos/text_photo':
                $sql = "select * from `".DB_PREF."mc_text_photos_text_photo` where `id` = '".(int)$widget['module_id']."' ";
                $rs = mysql_query($sql);
                if($rs){
                    $lock = mysql_fetch_assoc($rs);
                    $values = $lock;
                    $values['row_number'] = $position;
                    $values['content_element_id'] = $targetId;
                    $values['visible'] = $widget['visible'];

                    $values['new_photo'] = $values['photo'];
                    $values['new_bigphoto'] = $values['photo_big'];

                    copy(BASE_DIR.IMAGE_DIR.$values['photo'], BASE_DIR.TMP_IMAGE_DIR.$values['photo']);
                    copy(BASE_DIR.IMAGE_DIR.$values['photo_big'], BASE_DIR.TMP_IMAGE_DIR.$values['photo_big']);


                    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text_photo/module.php');
                    $widget = new \Modules\standard\content_management\Widgets\text_photos\text_photo\Module();
                    $widget->create_new_instance($values);
                } else {
                    trigger_error($sql.' '.mysql_error());
                }
                return true;
                break;
            case 'text_photos/text_title':
                $sql = "select * from `".DB_PREF."mc_text_photos_text_title` where `id` = '".(int)$widget['module_id']."' ";
                $rs = mysql_query($sql);
                if($rs){
                    $lock = mysql_fetch_assoc($rs);
                    $values = $lock;
                    $values['row_number'] = $position;
                    $values['content_element_id'] = $targetId;
                    $values['visible'] = $widget['visible'];
                    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text_title/module.php');
                    $widget = new \Modules\standard\content_management\Widgets\text_photos\text_title\Module();
                    $widget->create_new_instance($values);
                } else {
                    trigger_error($sql.' '.mysql_error());
                }
                return true;
                break;
            case 'text_photos/title':
                $sql = "select * from `".DB_PREF."mc_text_photos_title` where `id` = '".(int)$widget['module_id']."' ";
                $rs = mysql_query($sql);
                if($rs){
                    $lock = mysql_fetch_assoc($rs);
                    $values = $lock;
                    $values['row_number'] = $position;
                    $values['content_element_id'] = $targetId;
                    $values['visible'] = $widget['visible'];
                    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/module.php');
                    $widget = new \Modules\standard\content_management\Widgets\text_photos\title\Module();
                    $widget->create_new_instance($values);
                } else {
                    trigger_error($sql.' '.mysql_error());
                }
                return true;
                break;                
            case 'misc/rich_text':
                $sql = "select * from `".DB_PREF."mc_misc_rich_text` where `id` = '".(int)$widget['module_id']."' ";
                $rs = mysql_query($sql);
                if($rs){
                    $lock = mysql_fetch_assoc($rs);
                    $values = $lock;
                    $values['row_number'] = $position;
                    $values['content_element_id'] = $targetId;
                    $values['visible'] = $widget['visible'];
                    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/misc/rich_text/module.php');
                    $widget = new \Modules\standard\content_management\Widgets\misc\rich_text\Module();
                    $widget->create_new_instance($values);
                } else {
                    trigger_error($sql.' '.mysql_error());
                }
                return true;
                break;
            case 'misc/contact_form':
                $sql = "select * from `".DB_PREF."mc_misc_contact_form` where `id` = '".(int)$widget['module_id']."' ";
                $rs = mysql_query($sql);
                if($rs){
                    $lock = mysql_fetch_assoc($rs);
                    $values = $lock;
                    $values['row_number'] = $position;
                    $values['content_element_id'] = $targetId;
                    $values['visible'] = $widget['visible'];

                    $sqlField = "select * from `".DB_PREF."mc_misc_contact_form_field` where `contact_form` = '".(int)$widget['module_id']."' order by id asc";
                    $rsField = mysql_query($sqlField);
                    if(!$rsField){
                        trigger_error($sql.' '.mysql_error());
                    } else {
                        $i = 0;
                        while($lockField = mysql_fetch_assoc($rsField)){
                            $values['field_'.$i.'_name'] = $lockField['name'];
                            $values['field_'.$i.'_type'] = $lockField['type'];
                            $values['field_'.$i.'_required'] = $lockField['required'];
                            $values['field_'.$i.'_values'] = $lockField['values'];
                            $i++;
                        }

                        require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/misc/contact_form/module.php');
                        $widgetObject = new \Modules\standard\content_management\Widgets\misc\contact_form\Module();
                        $widgetObject->create_new_instance($values);


                    }

                } else {
                    trigger_error($sql.' '.mysql_error());
                }
                return true;
                break;
            case 'misc/file':
                $sql = "select * from `".DB_PREF."mc_misc_file` where `id` = '".(int)$widget['module_id']."' ";
                $rs = mysql_query($sql);
                if($rs){
                    $lock = mysql_fetch_assoc($rs);
                    $values = $lock;
                    $values['row_number'] = $position;
                    $values['content_element_id'] = $targetId;
                    $values['visible'] = $widget['visible'];
                    $values['new_photo'] = $values['photo'];

                    copy(BASE_DIR.FILE_DIR.$values['photo'], BASE_DIR.TMP_FILE_DIR.$values['photo']);

                    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/misc/file/module.php');
                    $widget = new \Modules\standard\content_management\Widgets\misc\file\Module();
                    $widget->create_new_instance($values);
                } else {
                    trigger_error($sql.' '.mysql_error());
                }
                return true;
                break;
            case 'misc/html_code':
                $sql = "select * from `".DB_PREF."mc_misc_html_code` where `id` = '".(int)$widget['module_id']."' ";
                $rs = mysql_query($sql);
                if($rs){
                    $lock = mysql_fetch_assoc($rs);
                    $values = $lock;
                    $values['row_number'] = $position;
                    $values['content_element_id'] = $targetId;
                    $values['visible'] = $widget['visible'];


                    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/misc/html_code/module.php');
                    $widget = new \Modules\standard\content_management\Widgets\misc\html_code\Module();
                    $widget->create_new_instance($values);
                } else {
                    trigger_error($sql.' '.mysql_error());
                }
                return true;
                break;

            case 'misc/video':
                $sql = "select * from `".DB_PREF."mc_misc_video` where `id` = '".(int)$widget['module_id']."' ";
                $rs = mysql_query($sql);
                if($rs){
                    $lock = mysql_fetch_assoc($rs);
                    $values = $lock;
                    $values['row_number'] = $position;
                    $values['content_element_id'] = $targetId;
                    $values['visible'] = $widget['visible'];
                    $values['new_photo'] = $values['photo'];

                    copy(BASE_DIR.VIDEO_DIR.$values['photo'], BASE_DIR.TMP_VIDEO_DIR.$values['photo']);

                    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/misc/video/module.php');
                    $widget = new \Modules\standard\content_management\Widgets\misc\video\Module();
                    $widget->create_new_instance($values);
                } else {
                    trigger_error($sql.' '.mysql_error());
                }
                return true;
                break;

            default:
                //@todo config
                break;

        }

    }

}