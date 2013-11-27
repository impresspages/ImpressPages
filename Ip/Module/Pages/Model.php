<?php

/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Module\Pages;


class Model {



    public static function deletePage ($zoneName, $pageId) {

        $zone = ipContent()->getZone($zoneName);
        if (!$zone) {
            throw new \Exception("Unknown zone " + $zoneName);
        } 
        self::_deletePageRecursion($zone, $pageId);
        return true;
    }


    private static function _deletePageRecursion (\Ip\Zone $zone, $id) {
        $children = Db::pageChildren($id);
        if ($children) {
            foreach($children as $key => $lock) {
                self::_deletePageRecursion($zone, $lock['id']);
            }
        }

        Db::deletePage($id);

        ipDispatcher()->notify('site.pageDeleted', array('zoneName' => $zone->getName(), 'pageId' => $id));
    }


    /**
     *
     * Copy page
     * @param unknown_type $nodeId
     * @param unknown_type $newParentId
     * @param int $position page position in the subtree
     */
    public static function copyPage($zoneName, $nodeId, $destinationZoneName, $destinationPageId, $position){

        $children = Db::pageChildren($destinationPageId);

        if (!empty($children)) {
            $rowNumber = $children[count($children) - 1]['row_number'] + 1;
        } else {
            $rowNumber = 0;
        }


        self::_copyPageRecursion($zoneName, $nodeId, $destinationZoneName, $destinationPageId, $rowNumber);

    }

    /**
     *
     * Copy page internal recursion
     * @param unknown_type $nodeId
     * @param unknown_type $destinationPageId
     * @param unknown_type $newIndex
     * @param unknown_type $newPages
     */
    private static function _copyPageRecursion ($zoneName, $nodeId, $destinationZoneName, $destinationPageId, $rowNumber, $newPages = null) {
        //$newPages are the pages that have been copied already and should be skiped to duplicate again. This situacion can occur when copying the page to it self
        if($newPages == null){
            $newPages = array();
        }
        $newNodeId = Db::copyPage($nodeId, $destinationPageId, $rowNumber);
        $newPages[$newNodeId] = 1;
        self::_copyWidgets($zoneName, $nodeId, $destinationZoneName, $newNodeId);


        $children = Db::pageChildren($nodeId);
        if($children){
            foreach($children as $key => $lock){
                if(!isset($newPages[$lock['id']])){
                    self::_copyPageRecursion($zoneName, $lock['id'], $destinationZoneName, $newNodeId, $key, $newPages);
                }
            }
        }

    }

    private static function _copyWidgets($zoneName, $sourceId, $destinationZoneName, $targetId){
        $oldRevision = \Ip\Revision::getPublishedRevision($zoneName, $sourceId);
        \Ip\Revision::duplicateRevision($oldRevision['revisionId'], $destinationZoneName, $targetId, 1);
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
                copy($values['photo'], ipConfig()->temporaryFile($values['new_photo']));
                copy($values['photo_big'], ipConfig()->temporaryFile($values['new_bigphoto']));
                break;
            case 'text_photos/text_photo':
                $values['new_photo'] = basename($values['photo']);
                $values['new_bigphoto'] = basename($values['photo_big']);
                copy($values['photo'], ipConfig()->temporaryFile($values['new_photo']));
                copy($values['photo_big'], ipConfig()->temporaryFile($values['new_bigphoto']));
                break;
            case 'misc/file':
                $values['new_photo'] = basename($values['photo']);
                copy($values['photo'], ipConfig()->temporaryFile($values['new_photo']));
                break;
            case 'misc/video':
                $values['new_photo'] = basename($values['photo']);
                copy($values['photo'], ipConfig()->temporaryFile($values['new_photo']));
                break;
        }


        //TODOX create new widget object
        eval ('$widgetObject = new \\Modules\\standard\\content_management\\Widgets\\'.$widget['group_key'].'\\'.$widget['module_key'].'\\Module();');
        $widgetObject->create_new_instance($values);


        switch ($widget['group_key'].'/'.$widget['module_key']) {
            case 'text_photos/logo_gallery':
                $sqlMax = "select max(id) as max_id from `".DB_PREF."mc_text_photos_logo_gallery` where 1";
                $rsMax = ip_deprecated_mysql_query($sqlMax);
                if(!$rsMax){
                    trigger_error($sqlMax.' '.ip_deprecated_mysql_error());
                }
                $lockMax = ip_deprecated_mysql_fetch_assoc($rsMax);
                $galleryId = $lockMax['max_id'];
                foreach($values['logos'] as $logoKey => $logo){
                    $tmpValues = array();
                    copy($logo['logo'], ipConfig()->temporaryFile(basename($logo['logo'])));
                    $tmpValues['new_photo1'] = basename($logo['logo']);
                    $tmpValues['title1'] = $logo['link'];
                    $widgetObject->insert_photo($galleryId, 1, $tmpValues);
                }
                break;
            case 'text_photos/photo_gallery':

                $sqlMax = "select max(id) as max_id from `".DB_PREF."mc_text_photos_photo_gallery` where 1";
                $rsMax = ip_deprecated_mysql_query($sqlMax);
                if(!$rsMax){
                    trigger_error($sqlMax.' '.ip_deprecated_mysql_error());
                }
                $lockMax = ip_deprecated_mysql_fetch_assoc($rsMax);
                $galleryId = $lockMax['max_id'];


                foreach($values['photos'] as $photoKey => $photo){
                    $tmpValues = array();
                    copy($photo['photo'], ipConfig()->temporaryFile(basename($photo['photo'])));
                    copy($photo['photo_big'], ipConfig()->temporaryFile(basename($photo['photo_big'])));
                    $tmpValues['new_photo1'] = basename($photo['photo']);
                    $tmpValues['new_bigphoto1'] = basename($photo['photo_big']);
                    $tmpValues['title1'] = $photo['title'];
                    $widgetObject->insert_photo($galleryId, 1, $tmpValues);
                }
                break;


        }

    }



}