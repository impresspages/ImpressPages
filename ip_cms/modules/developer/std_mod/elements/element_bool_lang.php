<?php
/**
 * @package		Library
 *
 *
 */
namespace Modules\developer\std_mod;

class ElementBoolLang extends Element{ //data element in area
    var $defaultValues; //array of default values for all languages
    var $translationField;
    var $translationTable;
    var $recordIdField;
    var $languageIdField;


    function __construct($variables){
        parent::__construct($variables);

        $this->recordIdField = 'record_id';
        $this->languageIdField = 'language_id';
        $this->translationField = 'translation';

        require_once(BASE_DIR.MODULE_DIR.'developer/std_mod/std_mod_db.php');
        $stdModDb = new StdModDb();

        $languages = $stdModDb->languages();
        $this->defaultValues = array();
        foreach($languages as $key => $language){
            $this->defaultValues[$language['id']] = false;
        }


        if(!isset($variables['translationTable']) || $variables['translationTable'] == ''){
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
            trigger_error('ElementBoolLang translationTable parameter not set. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
            else
            trigger_error('ElementBoolLang translationTable parameter not set.');
            exit;
        }


        if(isset($variables['sortable']) && $variables['sortable'] == true){
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
            trigger_error('ElementBoolLang can\'t be sortable. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
            else
            trigger_error('ElementBoolLang can\'t be sortable.');
            exit;
        }


        foreach ($variables as $name => $value) {
            switch ($name){
                case 'translationField':
                    $this->translationField = $value;
                    break;
                case 'translationTable':
                    $this->translationTable = $value;
                    break;
                case 'recordIdField':
                    $this->recordIdField = $value;
                    break;
                case 'languageIdField':
                    $this->languageIdField = $value;
                    break;
                case 'defaultValues':
                    $this->defaultValues = $value;
                    break;
                case 'defaultValue':
                    foreach($languages as $key => $language){
                        $this->defaultValues[$language['id']] = $value;
                    }
                    break;
            }
        }

    }

    function printFieldNew($prefix, $id = null, $area = null){
        $html = new StdModHtmlOutput();
        global $stdModDb;

        $languages = $stdModDb->languages();

        $answer = '';
        foreach($languages as $key => $language){
            $html->html('<span class="label">'.htmlspecialchars($language['d_short']).':</span>');
            if(isset($this->defaultValues[$language['id']]))
            $html->inputCheckbox($prefix.'_'.$language['id'], $this->defaultValues[$language['id']], $this->disabledOnInsert);
            else
            $html->inputCheckbox($prefix.'_'.$language['id'], $this->defaultValues[$language['id']], $this->disabledOnInsert);
        }

        return $html->html;
    }



    function printFieldUpdate($prefix, $record, $area){
        $html = new StdModHtmlOutput();
        global $stdModDb;

        $answer = '';
        $sql2 = "select *, AES_DECRYPT(".mysql_real_escape_string($this->translationField).", '".$this->secureKey."') AS ".$this->translationField." from `".DB_PREF.mysql_real_escape_string($this->translationTable)."` t where t.`".$this->recordIdField."` = '".(int)$record[$area->dbPrimaryKey]."' ";
        $rs2 = mysql_query($sql2);
        $values = array();
        if (!$rs2) {
            trigger_error("Can not get language field data. ".$sql2." ".mysql_error());
        } else {
            while($lock2 = mysql_fetch_assoc($rs2)){
                $values[$lock2[$this->languageIdField]] = $lock2[$this->translationField];
            }

            $languages = $stdModDb->languages();

            foreach($languages as $key => $language){
                $value = '';
                if(isset($values[$language['id']])){
                    $value = $values[$language['id']];
                }
                $html->html('<span class="label">'.htmlspecialchars($language['d_short']).':</span>');
                $html->inputCheckbox($prefix.'_'.$language['id'], $value, $this->disabledOnUpdate);
            }

        }

        return $html->html;
    }


    function getParameters($action, $prefix, $area){
        return false;
    }


    function previewValue($record, $area){
        global $stdModDb;

        $answer='';
        $values = array();

        $sql2 = "select *, AES_DECRYPT(".mysql_real_escape_string($this->translationField).", '".$this->secureKey."') AS ".$this->translationField." from `".DB_PREF.mysql_real_escape_string($this->translationTable)."` t where t.`".$this->recordIdField."` = '".(int)$record[$area->dbPrimaryKey]."' ";
        $rs2 = mysql_query($sql2);
        if (!$rs2) {
            trigger_error("Can not get language field data. ".$sql2." ".mysql_error());
        } else {
            while($lock2 = mysql_fetch_assoc($rs2)){
                $values[$lock2[$this->languageIdField]] = $lock2[$this->translationField];
            }
        }

        $languages = $stdModDb->languages();
        foreach($languages as $key => $language){
            $value = '';
            if(isset($values[$language['id']])){
                if ($values[$language['id']] == 1)
                $value = "+";
                else
                $value = "-";
            }
            $answer .= '/'.$value;
        }

        return htmlspecialchars(mb_substr($answer, 0, $this->previewLength));
    }

    function checkField($prefix, $action, $area){
        global $stdModDb;
        global $parametersMod;

        $languages = $stdModDb->languages();

        if ($this->required){
            foreach($languages as $key => $language){
                if(!isset($_REQUEST[$prefix.'_'.$language['id']]) || $_REQUEST[$prefix.'_'.$language['id']] == null)
                return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_required');
            }
        }



        return null;
    }

    function processInsert($prefix, $lastInsertId, $area){
        global $stdModDb;
        $languages = $stdModDb->languages();

        foreach($languages as $key => $language){
            if($this->visibleOnInsert && !$this->disabledOnInsert){
                if (isset($_REQUEST[$prefix.'_'.$language['id']]))
                $value = 1;
                else
                $value = 0;
            } else {
                if(isset($this->defaultValues[$language['id']]) && $this->defaultValues[$language['id']])
                $value = 1;
                else
                $value = 0;
            }

            $sql3 = "update `".DB_PREF.mysql_real_escape_string($this->translationTable)."`
      set `".mysql_real_escape_string($this->translationField)."` = AES_ENCRYPT('".(int)$value."', '".$this->secureKey."')
      where `".mysql_real_escape_string($this->languageIdField)."`  = '".(int)$language['id']."' and `".mysql_real_escape_string($this->recordIdField)."` = '".(int)$lastInsertId."' ";
            $rs3 = mysql_query($sql3);
            if ($rs3){
                if($stdModDb->updatedRowsCount() == 0){
                    $sql4 = "insert into `".DB_PREF.mysql_real_escape_string($this->translationTable)."`
          set `".mysql_real_escape_string($this->translationField)."` = AES_ENCRYPT('".(int)$value."', '".$this->secureKey."'),
          `".mysql_real_escape_string($this->languageIdField)."`  = '".(int)$language['id']."', `".mysql_real_escape_string($this->recordIdField)."` = '".(int)$lastInsertId."' ";

                    $rs4 = mysql_query($sql4);
                    if(!$rs4)
                    trigger_error($sql4." ".mysql_error());

                }
            }else{
                trigger_error("Can't insert language field values ".$sql3." ".mysql_error());
            }

        }
    }
    function processUpdate($prefix, $rowId, $area){
        global $stdModDb;

        if($this->visibleOnUpdate && !$this->disabledOnUpdate){
            $languages = $stdModDb->languages();

            foreach($languages as $key => $language){
                if (isset($_REQUEST[$prefix.'_'.$language['id']]))
                $value = 1;
                else
                $value = 0;


                $sql3 = "update `".DB_PREF.mysql_real_escape_string($this->translationTable)."`
        set `".mysql_real_escape_string($this->translationField)."` = AES_ENCRYPT('".(int)$value."', '".$this->secureKey."')
        where 
        `".mysql_real_escape_string($this->recordIdField)."` = '".(int)$rowId."' and `".mysql_real_escape_string($this->languageIdField)."` = '".(int)$language['id']." '
        ";
                $rs3 = mysql_query($sql3);
                if($rs3){
                    if($stdModDb->updatedRowsCount() == 0){
                        $sql4 = "insert into `".DB_PREF.mysql_real_escape_string($this->translationTable)."`
            set `".mysql_real_escape_string($this->translationField)."` = AES_ENCRYPT('".(int)$value."', '".$this->secureKey."'),
            `".mysql_real_escape_string($this->languageIdField)."`  = '".(int)$language['id']."', `".mysql_real_escape_string($this->recordIdField)."` = '".(int)$rowId."' ";

                        $rs4 = mysql_query($sql4);
                        if(!$rs4)
                        trigger_error($sql4." ".mysql_error());

                    }
                } else {
                    trigger_error("Can't update language field values ".$sql3." ".mysql_error());
                }
            }
        }
    }

    function processDelete($area, $id){
        $sql2 = "delete from `".DB_PREF.mysql_real_escape_string($this->translationTable)."` where `".$this->recordIdField."` = '".(int)$id."'";
        $rs2 = mysql_query($sql2);
        if(!$rs2)
        trigger_error("Can't delete language field values ".$sql2." ".mysql_error());
    }

    function printSearchField($level, $key, $area){

        global $parametersMod;
        global $stdModDb;

        $answer = '';

        $languages = $stdModDb->languages();

        foreach($languages as $language){
            $checked1 = '';
            $checked2 = '';
            if (isset($_REQUEST['search'][$level][$key][$language['id']])){
                if($_REQUEST['search'][$level][$key][$language['id']] == 1){
                    $checked1 = " checked ";
                }else{
                    $checked2 = " checked ";
                }
            }

            $answer .= '
      <span class="label">'.htmlspecialchars($language['d_short']).'</span> <br />
      <span class="label"><input class="stdModRadio" type="radio" '.$checked1.' name="search['.$level.']['.$key.']['.$language['id'].']" value="1" />'.$parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'yes').'</span>
      <span class="label"><input  class="stdModRadio" type="radio" '.$checked2.' name="search['.$level.']['.$key.']['.$language['id'].']" value="0" />'.$parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'no').'</span>
      <br />
      ';

        }

        return $answer;


    }

    function getFilterOption($value, $area){

        global $stdModDb;

        $answer = '1';
        $conditions = array();


        $languages = $stdModDb->languages();

        foreach($languages as $language){
            if (isset($value[$language['id']])){

                if($value[$language['id']] == 1){
                    $tmpValue = 1;
                } else {
                    $tmpValue = 0;
                }
                $sql = "select `".mysql_real_escape_string($this->recordIdField)."` as 'id' from `".DB_PREF.mysql_real_escape_string($this->translationTable)."`
        where `".mysql_real_escape_string($this->languageIdField)."` = '".mysql_real_escape_string($language['id'])."' 
        and AES_DECRYPT(".mysql_real_escape_string($this->translationField).", '".$this->secureKey."') = '".$tmpValue."'";

                if(isset($ids)){ //second language
                    $sql .= " and `".mysql_real_escape_string($this->recordIdField)."` in (".implode($ids, ',').") ";
                }

                $rs = mysql_query($sql);

                if ($rs){
                    $ids = array();
                    while($lock = mysql_fetch_assoc($rs)){
                        $ids[] = mysql_real_escape_string($lock['id']);
                    }
                }
            }
        }


        if(isset($ids)){
            if(sizeof($ids) > 0)
            $answer = ' `'.mysql_real_escape_string($area->dbPrimaryKey).'` in ('.implode($ids, ',').') ';
            else
            $answer = ' 0 ';
        } else {
            $answer =  ' 1 ';
        }

        return $answer;

    }





}