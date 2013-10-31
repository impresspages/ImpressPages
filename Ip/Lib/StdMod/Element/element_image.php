<?php
/**
 * @package		Library
 *
 *
 */
namespace Ip\Lib\StdMod;


class ElementImage extends Element{ //data element in area
    var $copies;
    var $memImages;


    function __construct($variables){
        parent::__construct($variables);

        $this->memImages = array();
        $this->copies = array();

        if(!isset($variables['copies']) || $variables['copies'] == ''){
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
            trigger_error('ElementPhoto copies array not set. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
            else
            trigger_error('ElementPhoto copies array not set.');
            exit;
        }

        if(isset($variables['sortable']) && $variables['sortable'] == true){
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
            trigger_error('ElementPhoto can\'t be sortable. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
            else
            trigger_error('ElementPhoto can\'t be sortable.');
            exit;
        }


        foreach ($variables as $name => $value) {
            switch ($name){
                case 'copies':
                    $this->copies = $value;
                    break;
            }
        }

    }

    function printFieldNew($prefix, $parentId = null, $area = null){
        $html = new \Ip\Lib\StdMod\StdModHtmlOutput();
        $html->inputFile($prefix, $this->disabledOnInsert);
        return $html->html;
    }


    function printFieldUpdate($prefix, $record, $area = null){



        $value = $record[$this->copies[0]['dbField']];

        /* translation */
        global $parametersMod;
        $deleteTranslation = '&nbsp;'.$parametersMod->getValue('developer', 'std_mod', 'admin_translations','delete').'&nbsp;';
        /*eof translation*/

        $image = \Ip\Config::baseUrl($this->copies[0]['destDir'].$value);

    $sizing = '';

    if(file_exists(\Ip\Config::baseFile($this->copies[0]['destDir'].$value)) && is_file(\Ip\Config::baseFile($this->copies[0]['destDir'].$value)))
    {
        $imageSize = getimagesize(\Ip\Config::baseFile($this->copies[0]['destDir'].$value));

        if( $imageSize[0] >= 200 && $imageSize[0]>=$imageSize[1] ) // width more than 200 and image is horizontal
        {
          $sizing.='width="200" '; // limit only width (let browser scale height)
        }elseif ( $imageSize[1] >= 200 && $imageSize[1] >= $imageSize[0] ) // height more than 200 and image is vertical
          {
            $sizing.='height="200" '; // limit only height (let browse scale width)
          }
    }
        $html = new \Ip\Lib\StdMod\StdModHtmlOutput();
        if($value)
        $html->html('<span class="label"><img '.$sizing.'src="'.$image.'"/></span><br />');
        $html->inputFile($prefix, $this->disabledOnUpdate);
        if($value){
            $html->html('<span class="label"><input  class="stdModBox" type="checkbox" name="'.$prefix.'_delete"></span>');
            $html->html($deleteTranslation.'');
        }


        return $html->html;

         
    }



    function getParameters($action, $prefix, $area){

        return null; //array("name"=>$this->dbField, "value"=>$_REQUEST[''.$prefix]);
    }


    function previewValue($record, $area){
        if($record[$this->copies[0]['dbField']])
        return '<img width="80" src="'.$this->copies[0]['destDir'].$record[$this->copies[0]['dbField']].'" >';
        else
        return '';
    }



    function checkField($prefix, $action, $area){
        global $parametersMod;



        if(
        $action == 'insert' && $this->disabledOnInsert || $action == 'insert' && !$this->visibleOnInsert ||
        $action == 'update' && $this->disabledOnUpdate || $action == 'update' && !$this->visibleOnUpdate
        ){
            return null;
        }

        if(isset($_POST[$prefix.'_delete']) && $this->required)
        return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_required');



        $this->newMemImages = array();
        foreach($this->copies as $key => $copy){
            $upload_image = new \Library\Php\File\UploadImage();
            $error = $upload_image->upload($prefix,$copy['width'], $copy['height'], \Ip\Config::temporaryFile(''), $copy['type'], $copy['forced'], $copy['quality']);
            if($error == UPLOAD_ERR_OK){
                $this->newMemImages[$key] = $upload_image->fileName;
            }elseif($error ==  UPLOAD_ERR_NO_FILE && $this->required && (sizeof($this->memImages) != sizeof($this->copies)) && $action== 'insert'){
                return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_required');
            }elseif($error ==  UPLOAD_ERR_NO_FILE){
                return null;
            }elseif($error == UPLOAD_ERR_EXTENSION)
            return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_file_type').' JPEG, GIF, PNG';
            else{
                return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_file_upload')." ".$error;
            }
        }

        if(sizeof($this->newMemImages) > 0)
        $this->memImages = $this->newMemImages;
        elseif(isset($_POST[$prefix.'_delete']))
        $this->memImages = array();

        return null;
    }

    function processInsert( $prefix, $id, $area){

        if($this->visibleOnInsert && !$this->disabledOnInsert){

            // delete photo selected
            if(isset($_POST[$prefix.'_delete']) && !$this->required)
            $this->memImages = array();
            // eof delete photo selected
             

            if(sizeof($this->memImages) == sizeof($this->copies)){

                foreach($this->copies as $key => $copy){
                    $newBasename = \Library\Php\File\Functions::copyTemporaryFile($this->memImages[$key], $copy['destDir']);

                    $sql = "update `".DB_PREF."".$area->dbTable."` set `".$copy['dbField']."` = '".mysql_real_escape_string($newBasename)."' where `".$area->dbPrimaryKey."` = '".mysql_real_escape_string($id)."' ";
                    $rs = mysql_query($sql);
                    if (!$rs)
                    trigger_error("Can't update photo field ".$sql);
                }
            }

        }


    }
    function processUpdate($prefix, $id, $area){


        global $parametersMod;


        if($this->visibleOnUpdate && !$this->disabledOnUpdate){
            // delete photo selected
            if(isset($_POST[$prefix.'_delete']) && !$this->required ){

                foreach($this->copies as $key => $copy){
                    $sql = "select `".$copy['dbField']."` as existing_photo from `".DB_PREF."".$area->dbTable."` where `".$area->dbPrimaryKey."` = '".mysql_real_escape_string($id)."' ";
                    $rs = mysql_query($sql);
                    if ($rs){
                        if ($lock = mysql_fetch_assoc($rs)){
                            if($lock['existing_photo'] != "" && file_exists($copy['destDir'].$lock['existing_photo']))
                            unlink($copy['destDir'].$lock['existing_photo']);
                        }
                    }else{
                        trigger_error("Can't get field to update ".$sql);
                        return;
                    }

                }


                foreach($this->copies as $key => $copy){

                    $sql = "update `".DB_PREF."".$area->dbTable."` set `".$copy['dbField']."` = NULL where `".$area->dbPrimaryKey."` = '".mysql_real_escape_string($id)."' ";
                    $rs = mysql_query($sql);
                    if (!$rs)
                    trigger_error("Can't update photo field ".$sql);
                }

            }

            // eof delete photo selected
             

            if(sizeof($this->memImages) == sizeof($this->copies)){
                foreach($this->copies as $key => $copy){
                    $newBasename = \Library\Php\File\Functions::copyTemporaryFile($this->memImages[$key], $copy['destDir']);

                    $sql = "update `".DB_PREF."".$area->dbTable."` set `".$copy['dbField']."` = '".$newBasename."' where `".$area->dbPrimaryKey."` = '".mysql_real_escape_string($id)."' ";
                    $rs = mysql_query($sql);
                    if (!$rs) {
                        trigger_error("Can't update photo field ".$sql);
                    }
                }
            }


        }

    }
    function processDelete($area, $id){

        // delete photo selected


        foreach($this->copies as $key => $copy){
            $sql = "select `".$copy['dbField']."` as existing_photo from `".DB_PREF."".$area->dbTable."` where `".$area->dbPrimaryKey."` = '".mysql_real_escape_string($id)."' ";
            $rs = mysql_query($sql);
            if ($rs){
                if ($lock = mysql_fetch_assoc($rs)){
                    if($lock['existing_photo'] != "" && file_exists($copy['destDir'].$lock['existing_photo']))
                    unlink($copy['destDir'].$lock['existing_photo']);
                }
            }else{
                trigger_error("Can't get field to update ".$sql);
                return;
            }

            $sql = "update `".DB_PREF."".$area->dbTable."` set `".$copy['dbField']."` = NULL where `".$area->dbPrimaryKey."` = '".mysql_real_escape_string($id)."' ";
            $rs = mysql_query($sql);
            if (!$rs)
            trigger_error("Can't update photo field ".$sql);
        }

    }



    function printSearchField($level, $key, $area){
        if (isset($_REQUEST['search'][$level][$key]))
        $value = $_REQUEST['search'][$level][$key];
        else
        $value = '';
        return '<input name="search['.$level.']['.$key.']" value="'.htmlspecialchars($value).'" />';
    }

    function getFilterOption($value, $area){
        $answer = '';
        foreach($this->copies as $copy){
            if($answer != '')
            $answer .= ' or ';
            $answer .= " `".mysql_real_escape_string($copy['dbField'])."` like '%".mysql_real_escape_string($value)."%' ";
        }
        $answer = ' ( '.$answer.' ) ';
        return $answer;
    }





}

