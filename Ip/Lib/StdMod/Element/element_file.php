<?php
/**
 * @package		Library
 *
 *
 */
namespace Ip\Lib\StdMod;


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

        $this->destDir = ipFile('file/');

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
         
        $html = new \Ip\Lib\StdMod\StdModHtmlOutput();
        $html->inputFile($prefix, $this->disabledOnInsert);
        return $html->html;
    }


    function printFieldUpdate($prefix, $record, $area = null){


        $value = null;


        $value = $record[''.$this->dbField];

        /* translation */
        global $parametersMod;
        $deleteTranslation = '&nbsp;'.__('Delete', 'ipAdmin').'&nbsp;';
        /*eof translation*/

        if($value != ''){
            $file = $this->destDir.$value;
        }

        $html = new \Ip\Lib\StdMod\StdModHtmlOutput();

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

        $uploadFile = new \Ip\Internal\File\UploadFile();
        if(sizeof($this->extensions) > 0){
            $uploadFile->allowOnly($this->extensions);
        }
        if(isset($_FILES[$prefix])){
            $error = $uploadFile->upload($prefix, ipFile('file/tmp/'));
            if($error == UPLOAD_ERR_OK){
                $this->memFile = $uploadFile->fileName;
                return null;
            }elseif($error ==  UPLOAD_ERR_NO_FILE && $this->required && !isset($this->memFile) && $action== 'insert'){
                return __('Required field', 'ipAdmin');
            }elseif($error ==  UPLOAD_ERR_NO_FILE){
                return null;
            }elseif($error == UPLOAD_ERR_EXTENSION)
            return __('Incorrect file type', 'ipAdmin').' '.implode(', ', $this->extensions);
            else{
                return __('File upload error', 'ipAdmin')." ".$error;
            }

        }

        if(isset($_POST[$prefix.'_delete']) && $this->required)
        return __('Required field', 'ipAdmin');

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

                $newBasename = \Ip\Internal\File\Functions::copyTemporaryFile($this->memFile, $this->destDir);

                $sql = "update `".DB_PREF."".$area->dbTable."` set `".$this->dbField."` = '".$newBasename."' where `".$area->dbPrimaryKey."` = '".ip_deprecated_mysql_real_escape_string($id)."' ";
                $rs = ip_deprecated_mysql_query($sql);
                if (!$rs)
                trigger_error("Can't update photo field ".$sql);
            }

        }
    }
    function processUpdate($prefix, $id, $area){
        if($this->visibleOnUpdate && !$this->disabledOnUpdate){
            if(isset($_POST[$prefix.'_delete']) && !$this->required || isset($this->memFile) && $this->memFile != ''){
                $sql = "select `".$this->dbField."` as existing_file from `".DB_PREF."".$area->dbTable."` where `".$area->dbPrimaryKey."` = '".ip_deprecated_mysql_real_escape_string($id)."' ";
                $rs = ip_deprecated_mysql_query($sql);
                if ($rs){
                    if ($lock = ip_deprecated_mysql_fetch_assoc($rs)){
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
                $sql = "update `".DB_PREF."".$area->dbTable."` set `".$this->dbField."` = NULL where `".$area->dbPrimaryKey."` = '".ip_deprecated_mysql_real_escape_string($id)."' ";
                $rs = ip_deprecated_mysql_query($sql);
                if (!$rs)
                trigger_error("Can't update photo field ".$sql);
            }
            // eof delete file selected
             
            if(isset($this->memFile) && $this->memFile != ''){

                $newBasename = \Ip\Internal\File\Functions::copyTemporaryFile($this->memFile, $this->destDir);

                $sql = "update `".DB_PREF."".$area->dbTable."` set `".$this->dbField."` = '".$newBasename."' where `".$area->dbPrimaryKey."` = '".ip_deprecated_mysql_real_escape_string($id)."' ";
                $rs = ip_deprecated_mysql_query($sql);
                if (!$rs)
                trigger_error("Can't update photo field ".$sql);
            }

        }
    }
    function processDelete($area, $id){

        // delete photo selected


        $sql = "select `".$this->dbField."` as existing_file from `".DB_PREF."".$area->dbTable."` where `".$area->dbPrimaryKey."` = '".ip_deprecated_mysql_real_escape_string($id)."' ";
        $rs = ip_deprecated_mysql_query($sql);
        if ($rs){
            if ($lock = ip_deprecated_mysql_fetch_assoc($rs)){
                if($lock['existing_file'] != "" && file_exists($this->destDir.$lock['existing_file']))
                unlink($this->destDir.$lock['existing_file']);
            }
        }else{
            trigger_error("Can't get field to update ".$sql);
            return;
        }

        $sql = "update `".DB_PREF."".$area->dbTable."` set `".$this->dbField."` = NULL where `".$area->dbPrimaryKey."` = '".ip_deprecated_mysql_real_escape_string($id)."' ";
        $rs = ip_deprecated_mysql_query($sql);
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
        return " `".$this->dbField."` like '%".ip_deprecated_mysql_real_escape_string($value)."%' ";
    }




}

