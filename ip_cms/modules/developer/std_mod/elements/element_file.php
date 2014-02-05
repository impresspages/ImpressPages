<?php
/**
 * @package		Library
 *
 *
 */
namespace Modules\developer\std_mod;


require_once (LIBRARY_DIR.'php/file/upload_file.php');


class ElementFile extends Element{ //data element in area
    var $memFile;
    var $extensions;
    var $destDir;

    function __construct($variables){
        if(!isset($variables['order'])){
            $variables['order'] = true;
        }


        parent::__construct($variables);



        if(!isset($variables['dbField']) || $variables['dbField'] == ''){
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
            trigger_error('ElementFile dbField parameter not set. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
            else
            trigger_error('ElementFile dbField parameter not set.');
            exit;
        }


        $this->tmpFiles = array();
        $this->extensions = array();

        $this->destDir = FILE_DIR;

        foreach ($variables as $name => $value) {
            switch ($name){
                case 'extensions':
                    $this->extensions = $value;
                    break;
                case 'destDir':
                    $this->destDir = $value;
                    break;
                case 'dbField':
                    $this->dbField = $value;
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


        $value = null;


        $value = $record[''.$this->dbField];

        /* translation */
        global $parametersMod;
        $deleteTranslation = '&nbsp;'.$parametersMod->getValue('developer', 'std_mod', 'admin_translations','delete').'&nbsp;';
        /*eof translation*/

        if($value != ''){
            $file = $this->destDir.$value;
        }

        $html = new StdModHtmlOutput();

        if($value)
        $html->html('<span class="label"><a target="_blank" href="'.$file.'" >'.htmlspecialchars(basename($file)).'</a></span><br />');

        $html->inputFile($prefix, $this->disabledOnUpdate);
        if($value){
            $html->html('<span class="label"><input class="stdModBox" type="checkbox" name="'.$prefix.'_delete"></span>');
            $html->html($deleteTranslation.'');
        }


        return $html->html;

         
    }



    function getParameters($action, $prefix, $area){
        return null;
    }


    function previewValue($record, $area){
        if($record[$this->dbField]){
            return '<a target="_blank" href="'.$this->destDir.$record[$this->dbField].'" >'.htmlspecialchars(mb_substr(basename($record[$this->dbField]), 0, $this->previewLength)).'</a>';
        }else{
            return '';
        }
    }


    function checkField($prefix, $action, $area){
        global $parametersMod;


        if(
        $action == 'insert' && $this->disabledOnInsert || $action == 'insert' && !$this->visibleOnInsert ||
        $action == 'update' && $this->disabledOnUpdate || $action == 'update' && !$this->visibleOnUpdate
        ){
            return null;
        }

        $uploadFile = new \Library\Php\File\UploadFile();
        if(sizeof($this->extensions) > 0){
            $uploadFile->allowOnly($this->extensions);
        }
        if(isset($_FILES[$prefix])){
            $error = $uploadFile->upload($prefix, TMP_FILE_DIR);
            if($error == UPLOAD_ERR_OK){
                $this->memFile = $uploadFile->fileName;
                return null;
            }elseif($error ==  UPLOAD_ERR_NO_FILE && $this->required && !isset($this->memFile) && $action== 'insert'){
                return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_required');
            }elseif($error ==  UPLOAD_ERR_NO_FILE){
                return null;
            }elseif($error == UPLOAD_ERR_EXTENSION)
            return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_file_type').' '.implode(', ', $this->extensions);
            else{
                return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_file_upload')." ".$error;
            }

        }

        if(isset($_POST[$prefix.'_delete']) && $this->required)
        return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_required');

        return null;
    }

    function processInsert( $prefix, $id, $area){
        global $parametersMod;

        if($this->visibleOnInsert && !$this->disabledOnInsert){
            // delete photo selected
            if(isset($_POST[$prefix.'_delete']) && !$this->required)
            unset($this->memFile);
            // eof delete photo selected
             

            if(isset($this->memFile) && $this->memFile != ''){
                require_once(LIBRARY_DIR.'php/file/functions.php');
                $newName = \Library\Php\File\Functions::genUnoccupiedName($this->memFile, $this->destDir);
                if(copy(TMP_FILE_DIR.$this->memFile,$this->destDir.$newName)){
                    $sql = "update `".DB_PREF."".$area->dbTable."` set `".$this->dbField."` = ";

                    if(!$this->secure)
                        $sql .= "'".$newName."'";
                    else
                        $sql .= "AES_ENCRYPT('".mysql_real_escape_string($newName)."', '".$this->secureKey."')";

                    $sql .= " where `".$area->dbPrimaryKey."` = '".mysql_real_escape_string($id)."' ";

                    $rs = mysql_query($sql);
                    if (!$rs)
                    trigger_error("Can't update photo field ".$sql);
                }else
                trigger_error("Can't copy file from ".htmlspecialchars(TMP_FILE_DIR.$this->memFile)." to ".htmlspecialchars($this->destDir.$newName));
            }

        }
    }
    function processUpdate($prefix, $id, $area){
        if($this->visibleOnUpdate && !$this->disabledOnUpdate){
            if(isset($_POST[$prefix.'_delete']) && !$this->required || isset($this->memFile) && $this->memFile != ''){
                $sql = "select `".$this->dbField."` as existing_file from `".DB_PREF."".$area->dbTable."` where `".$area->dbPrimaryKey."` = '".mysql_real_escape_string($id)."' ";
                $rs = mysql_query($sql);
                if ($rs){
                    if ($lock = mysql_fetch_assoc($rs)){
                        if($lock['existing_file'] != "" && file_exists($this->destDir.$lock['existing_file']))
                        unlink($this->destDir.$lock['existing_file']);
                    }
                }else{
                    trigger_error("Can't get field to update ".$sql);
                    return;
                }
            }

            // delete file selected
            if(isset($_POST[$prefix.'_delete']) && !$this->required){
                $sql = "update `".DB_PREF."".$area->dbTable."` set `".$this->dbField."` = NULL where `".$area->dbPrimaryKey."` = '".mysql_real_escape_string($id)."' ";
                $rs = mysql_query($sql);
                if (!$rs)
                trigger_error("Can't update photo field ".$sql);
            }
            // eof delete file selected
             
            if(isset($this->memFile) && $this->memFile != ''){
                require_once(LIBRARY_DIR.'php/file/functions.php');
                $newName = \Library\Php\File\Functions::genUnoccupiedName($this->memFile, $this->destDir);
                if(copy(TMP_FILE_DIR.$this->memFile,$this->destDir.$newName)){
                    $sql = "update `".DB_PREF."".$area->dbTable."` set `".$this->dbField."` = ";

                    if(!$this->secure)
                        $sql .= "'".$newName."'";
                    else
                        $sql .= "AES_ENCRYPT('".mysql_real_escape_string($newName)."', '".$this->secureKey."')";

                    $sql .= " where `".$area->dbPrimaryKey."` = '".mysql_real_escape_string($id)."' ";
                    $rs = mysql_query($sql);
                    if (!$rs)
                    trigger_error("Can't update photo field ".$sql);
                }else
                trigger_error("Can't copy file from ".htmlspecialchars(TMP_FILE_DIR.$this->memFile)." to ".htmlspecialchars($this->destDir.$newName));
            }

        }
    }
    function processDelete($area, $id){

        // delete photo selected


        $sql = "select `".$this->dbField."` as existing_file from `".DB_PREF."".$area->dbTable."` where `".$area->dbPrimaryKey."` = '".mysql_real_escape_string($id)."' ";
        $rs = mysql_query($sql);
        if ($rs){
            if ($lock = mysql_fetch_assoc($rs)){
                if($lock['existing_file'] != "" && file_exists($this->destDir.$lock['existing_file']))
                unlink($this->destDir.$lock['existing_file']);
            }
        }else{
            trigger_error("Can't get field to update ".$sql);
            return;
        }

        $sql = "update `".DB_PREF."".$area->dbTable."` set `".$this->dbField."` = NULL where `".$area->dbPrimaryKey."` = '".mysql_real_escape_string($id)."' ";
        $rs = mysql_query($sql);
        if (!$rs)
        trigger_error("Can't update photo field ".$sql);

    }

    function printSearchField($level, $key, $area){
        if (isset($_REQUEST['search'][$level][$key]))
        $value = $_REQUEST['search'][$level][$key];
        else
        $value = '';
        return '<input name="search['.$level.']['.$key.']" value="'.htmlspecialchars($value).'" />';
    }

    function getFilterOption($value, $area){
        if(!$this->secure)
            $dbField =  "`".$this->dbField."`";
        else
            $dbField =  "AES_DECRYPT(".$this->dbField.", '".$this->secureKey."')";

        return " ".$dbField." like '%".mysql_real_escape_string($value)."%' ";
    }




}

