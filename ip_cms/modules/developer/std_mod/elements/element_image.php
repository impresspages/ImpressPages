<?php
/**
 * @package		Library
 *
 *
 */
namespace Modules\developer\std_mod;


require_once(LIBRARY_DIR.'php/file/upload_image.php');


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
        $html = new StdModHtmlOutput();
        $html->inputFile($prefix, $this->disabledOnInsert);
        return $html->html;
    }


    function printFieldUpdate($prefix, $record, $area = null){



        $value = $record[$this->copies[0]['dbField']];

        /* translation */
        global $parametersMod;
        $deleteTranslation = '&nbsp;'.$parametersMod->getValue('developer', 'std_mod', 'admin_translations','delete').'&nbsp;';
        /*eof translation*/

        $image = BASE_URL.$this->copies[0]['destDir'].$value;

    $sizing = '';

    if(file_exists(BASE_DIR.$this->copies[0]['destDir'].$value) && is_file(BASE_DIR.$this->copies[0]['destDir'].$value))
    {
        $imageSize = getimagesize(BASE_DIR.$this->copies[0]['destDir'].$value);

        if( $imageSize[0] >= 200 && $imageSize[0]>=$imageSize[1] ) // width more than 200 and image is horizontal
        {
          $sizing.='width="200" '; // limit only width (let browser scale height)
        }elseif ( $imageSize[1] >= 200 && $imageSize[1] >= $imageSize[0] ) // height more than 200 and image is vertical
          {
            $sizing.='height="200" '; // limit only height (let browse scale width)
          }
    }
        $html = new StdModHtmlOutput();
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
            $error = $upload_image->upload($prefix,$copy['width'], $copy['height'], TMP_IMAGE_DIR, $copy['type'], $copy['forced'], $copy['quality']);
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
                require_once(LIBRARY_DIR.'php/file/functions.php');
                foreach($this->copies as $key => $copy){
                    $new_name = \Library\Php\File\Functions::genUnoccupiedName($this->memImages[$key], $copy['destDir']);
                    if(copy(TMP_IMAGE_DIR.$this->memImages[$key],$copy['destDir'].$new_name)){
                        $sql = "update `".DB_PREF."".$area->dbTable."` set `".$copy['dbField']."` = ";

                        if(!$this->secure)
                            $sql .= "'".mysql_real_escape_string($new_name)."'";
                        else
                            $sql .= "AES_ENCRYPT('".mysql_real_escape_string($new_name)."', '".$this->secureKey."')";

                        $sql .= " where `".$area->dbPrimaryKey."` = '".mysql_real_escape_string($id)."' ";
                        $rs = mysql_query($sql);
                        if (!$rs)
                        trigger_error("Can't update photo field ".$sql);
                    }else
                    trigger_error("Can't copy file from ".htmlspecialchars(TMP_IMAGE_DIR.$this->memImages[$key])." to ".htmlspecialchars($copy['destDir'].$new_name));
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
                require_once(LIBRARY_DIR.'php/file/functions.php');
                foreach($this->copies as $key => $copy){
                    $new_name = \Library\Php\File\Functions::genUnoccupiedName($this->memImages[$key], $copy['destDir']);
                    if(copy(TMP_IMAGE_DIR.$this->memImages[$key],$copy['destDir'].$new_name)){
                        $sql = "update `".DB_PREF."".$area->dbTable."` set `".$copy['dbField']."` = ";

                        if(!$this->secure)
                            $sql .= "'".$new_name."'";
                        else
                            $sql .= "AES_ENCRYPT('".mysql_real_escape_string($new_name)."', '".$this->secureKey."')";

                        $sql .= " where `".$area->dbPrimaryKey."` = '".mysql_real_escape_string($id)."' ";

                        $rs = mysql_query($sql);
                        if (!$rs)
                        trigger_error("Can't update photo field ".$sql);
                    }else
                    trigger_error("Can't copy file from ".htmlspecialchars(TMP_IMAGE_DIR.$this->memImages[$key])." to ".htmlspecialchars($copy['destDir'].$new_name));
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
            if(!$this->secure)
                $dbField =  "`".$copy['dbField']."`";
            else
                $dbField =  "AES_DECRYPT(".$copy['dbField'].", '".$this->secureKey."')";

            if($answer != '')
            $answer .= ' or ';
            $answer .= " ".$dbField." like '%".mysql_real_escape_string($value)."%' ";
        }
        $answer = ' ( '.$answer.' ) ';
        return $answer;
    }





}

