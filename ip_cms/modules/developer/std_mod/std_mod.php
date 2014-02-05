<?php
/**
 * @package		Library
 *
 *
 */

namespace Modules\developer\std_mod;



require_once (__dir__.'/std_mod_db.php');
require_once (__dir__.'/std_mod_html_output.php');
require_once (__dir__.'/std_mod_area.php');
require_once (__dir__.'/elements/element.php');
require_once (__dir__.'/elements/element_date_time.php');
require_once (__dir__.'/elements/element_date.php');
require_once (__dir__.'/elements/element_text.php');
require_once (__dir__.'/elements/element_bool.php');
require_once (__dir__.'/elements/element_bool_lang.php');
require_once (__dir__.'/elements/element_text_lang.php');
require_once (__dir__.'/elements/element_number.php');
require_once (__dir__.'/elements/element_pswd.php');
require_once (__dir__.'/elements/element_select.php');
require_once (__dir__.'/elements/element_select_lang.php');
require_once (__dir__.'/elements/element_image.php');
require_once (__dir__.'/elements/element_file.php');
require_once (__dir__.'/elements/element_wysiwyg.php');
require_once (__dir__.'/elements/element_wysiwyg_lang.php');
require_once (__dir__.'/elements/element_textarea.php');
require_once (__dir__.'/elements/element_textarea_lang.php');

class StandardModule {
    var $childArea;
    var $level;
    var $currentArea;
    var $upArea;
    var $road;
    var $errors;
    var $globalErrors;
    var $beforeContent;
    var $afterContent;
    var $currentId;

    var $treeDepth; //how deep show the tree on the left.
    function __construct($area, $treeDepth = 0) {
        $this->treeDepth = $treeDepth;
        $this->errors = array();
        $this->globalErrors = array();
        $this->childArea = $area;
        if(isset($_GET['road']) && is_array($_GET['road']))
        $this->level = sizeof($_GET['road']);
        else
        $this->level = 0;

        // find current area
        $this->currentArea =& $this->childArea;
        if(isset($_GET['road'])&& is_array($_GET['road']))
        if(isset($_GET['road_edit']) && $_GET['road_edit'] == 1) {
            for($i=0; $i<sizeof($_GET['road'])-1; $i++) {
                $this->currentArea =& $this->currentArea->getArea();
            }
            if ($this->currentArea && isset($_GET['road']) && isset($_GET['road'][(sizeof($_GET['road'])-2)]) && $_GET['road'][(sizeof($_GET['road'])-2)]!='') {
                $this->currentArea->parentId = $_GET['road'][(sizeof($_GET['road'])-2)];
            }

            if ($this->currentArea && isset($_GET['road']) && isset($_GET['road'][(sizeof($_GET['road'])-1)]) && $_GET['road'][(sizeof($_GET['road'])-1)]!='') {
                $this->currentArea->currentId = (int)$_GET['road'][(sizeof($_GET['road'])-1)];
            }

        } else {
            for($i=0; $i<sizeof($_GET['road']); $i++) {
                $this->currentArea =& $this->currentArea->getArea();
            }
            if ($this->currentArea && isset($_GET['road']) && isset($_GET['road'][(sizeof($_GET['road'])-1)]) && $_GET['road'][(sizeof($_GET['road'])-1)]!='') {
                $this->currentArea->parentId = (int)$_GET['road'][(sizeof($_GET['road'])-1)];
            }

        }


        //end find current area

        //order

        if(isset($_GET['sortField'][$this->level]))
        $this->currentArea->orderBy = $_GET['sortField'][$this->level];

        if(isset($_GET['sortDir'][$this->level]) && strtolower($_GET['sortDir'][$this->level]) == "asc")
        $this->currentArea->orderDirection = "asc";
        if(isset($_GET['sortDir'][$this->level]) && strtolower($_GET['sortDir'][$this->level]) == "desc") {
            $this->currentArea->orderDirection = "desc";
        }
        //end order



        //find upArea
        $this->road = '';
        $this->upArea =& $this->childArea;
        if($treeDepth == 0) {
            $this->road .= '<a href="'.$this->generateUrlRoot().'" class="navigation">'.$this->upArea->title."</a>";
        }

        if(isset($_GET['road'])&& is_array($_GET['road']))
        if(isset($_GET['road_edit']) && $_GET['road_edit'] == 1) {
            for($i=0; $i<(sizeof($_GET['road'])-2); $i++) {
                if ($this->upArea) $this->upArea =& $this->upArea->getArea();
            }
            if ($this->upArea && isset($_GET['road']) && isset($_GET['road'][(sizeof($_GET['road'])-2)]) && $_GET['road'][(sizeof($_GET['road'])-2)]!='') {
                $this->upArea->parentId = (int)$_GET['road'][(sizeof($_GET['road'])-2)];
            }


        } else {
            for($i=0; $i<(sizeof($_GET['road'])-1); $i++) {
                if ($this->upArea) $this->upArea =& $this->upArea->getArea();
            }
            if ($this->upArea && isset($_GET['road']) && isset($_GET['road'][(sizeof($_GET['road'])-1)]) && $_GET['road'][(sizeof($_GET['road'])-1)]!='') {
                $this->upArea->parentId = (int)$_GET['road'][(sizeof($_GET['road'])-1)];
            }



        }


        if(isset($_GET['road'])&& is_array($_GET['road']))
        for($i=0; $i<(sizeof($_GET['road'])); $i++) {
            if($i+1 >= $treeDepth) {
                if($this->road != '')
                $this->road .= '<span> -> </span>';

                if(isset($_GET['title'][$i]) && $_GET['title'][$i] != '') {
                    $this->road .= '<a href="'.$this->generateUrlLevel(($i+1)).'" class="navigation">'.htmlspecialchars($_GET['title'][$i])."</a>";
                } else {
                    if($i < sizeof($_GET['road']) - 1) {
                        $this->road .= '<a href="'.$this->generateUrlLevel(($i+1)).'" class="navigation">'.htmlspecialchars($this->upArea->title)."</a>";
                    }
                }
            }
        }

        /*    if(isset($_GET['road'])&&$this->currentArea){
         if($this->road != '')
         $this->road .= '<a> / </a>';

         if(isset($_GET['title'][sizeof($_GET['road'])-1]) && $_GET['title'][sizeof($_GET['road'])-1] != ''){
         $this->road .= '<a href="'.$this->generateUrlLevel((sizeof($_GET['road']))).'" class="navigation">'.htmlspecialchars($_GET['title'][sizeof($_GET['road'])-1]).'c</a>';
         } else {
         $this->road .= '<a href="'.$this->generateUrlLevel((sizeof($_GET['road']))).'" class="navigation">'.htmlspecialchars($this->currentArea->title).'c</a>';
         }
         }*/

        //end find upArea

        if(method_exists($this->currentArea, 'afterInit')) {
            $this->currentArea->afterInit($this->upArea->parentId);
        }

    }

    function ajaxAction() {
        global $parametersMod;
        global $cms;
        if(isset($_POST['action'])) {
            switch($_POST['action']) {
                case 'new_row_number':
                    if(method_exists($this->currentArea, 'beforeSort')) {
                        $this->currentArea->beforeSort();
                    }

                    $sql = "update `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."` set `".mysql_real_escape_string($this->currentArea->sortField)."` = '".mysql_real_escape_string($_POST['new_row_number'])."'
				where `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."` = '".mysql_real_escape_string($_POST['key_id'])."'";
                    $rs  = mysql_query($sql);
                    if(!$rs)
                    trigger_error($sql." ".mysql_error());

                    if(method_exists($this->currentArea, 'afterSort')) {
                        $this->currentArea->afterSort();
                    }

                    \Db::disconnect();
                    exit;
                    break;
                case 'row_number_increase': {

                    if(method_exists($this->currentArea, 'beforeSort')) {
                        $this->currentArea->beforeSort();
                    }

                    $sql_current = "select `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."`, `".mysql_real_escape_string($this->currentArea->sortField)."` from `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."` where `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."` = '".mysql_real_escape_string($_POST['key_id'])."'";
                    $rs_current  = mysql_query($sql_current);
                    if($rs_current)
                    if($lock_current = mysql_fetch_assoc($rs_current)) { //current record (need to be moved up)
                        /*searching upper record*/

                        if (($this->level > 0))
                        $sql_add = " and `".mysql_real_escape_string($this->currentArea->dbReference)."` = '".mysql_real_escape_string($this->upArea->parentId)."' ";
                        else
                        $sql_add = '';

                        $sql_upper = "select `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."`, `".mysql_real_escape_string($this->currentArea->sortField)."`
                              from `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."`
                              where `".mysql_real_escape_string($this->currentArea->sortField)."` >= '".mysql_real_escape_string($lock_current[$this->currentArea->sortField])."'
                              and `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."` <> '".mysql_real_escape_string($lock_current[$this->currentArea->dbPrimaryKey])."' ".$sql_add."
                              order by `".$this->currentArea->sortField."` asc limit 1";


                        $rs_upper  = mysql_query($sql_upper);
                        if($rs_upper)
                        if($lock_upper = mysql_fetch_assoc($rs_upper)) { //upper record (need to be moved down)
                            if($lock_upper[$this->currentArea->sortField] == $lock_current[$this->currentArea->sortField]) {

                                $sql_update = "update `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."`
                                    set `".mysql_real_escape_string($this->currentArea->sortField)."` = `".mysql_real_escape_string($this->currentArea->sortField)."` - 1
                                    where `".mysql_real_escape_string($this->currentArea->sortField)."` <= ".mysql_real_escape_string($lock_upper[$this->currentArea->sortField])." and `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."` <> '".mysql_real_escape_string($lock_current[$this->currentArea->dbPrimaryKey])."' ".$sql_add." ";
                                $rs_update = mysql_query($sql_update);
                                if(!$rs_update)
                                trigger_error($sql." ".mysql_error());

                            }else {

                                $sql_update = "update `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."`
                                    set `".mysql_real_escape_string($this->currentArea->sortField)."` = ".(int)$lock_current[$this->currentArea->sortField]."
                                    where `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."` = '".mysql_real_escape_string($lock_upper[$this->currentArea->dbPrimaryKey])."' ".$sql_add." limit 1";


                                $rs_update = mysql_query($sql_update);
                                if(!$rs_update)
                                trigger_error($sql_update." ".mysql_error());

                                $sql_update = "update `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."`
									set `".mysql_real_escape_string($this->currentArea->sortField)."` = ".(int)$lock_upper[$this->currentArea->sortField]."
									where `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."` = '".mysql_real_escape_string($lock_current[$this->currentArea->dbPrimaryKey])."' ".$sql_add." limit 1";

                                $rs_update = mysql_query($sql_update);
                                if(!$rs_update)
                                trigger_error($sql." ".mysql_error());
                            }

                        }
                    }else trigger_error($sql." Element does not exist");
                    echo "
              window.location = window.location;					   
          ";

                    if(method_exists($this->currentArea, 'afterSort')) {
                        $this->currentArea->afterSort();
                    }


                    \Db::disconnect();
                    exit;
                }
                break;
                case 'row_number_decrease': {
                    if(method_exists($this->currentArea, 'beforeSort')) {
                        $this->currentArea->beforeSort();
                    }

                    $sql_current = "select `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."`, `".mysql_real_escape_string($this->currentArea->sortField)."`
                            from `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."`
                            where `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."` = '".mysql_real_escape_string($_POST['key_id'])."'";

                    $rs_current  = mysql_query($sql_current);
                    if($rs_current)
                    if($lock_current = mysql_fetch_assoc($rs_current)) { //current record (need to be moved down)
                        /*searching under record*/

                        if (($this->level > 0))
                        $sql_add = " and `".mysql_real_escape_string($this->currentArea->dbReference)."` = '".mysql_real_escape_string($this->upArea->parentId)."' ";
                        else
                        $sql_add = '';


                        $sql_under = "select `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."`, `".mysql_real_escape_string($this->currentArea->sortField)."`
                              from `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."`
                              where `".mysql_real_escape_string($this->currentArea->sortField)."` <= '".mysql_real_escape_string($lock_current[$this->currentArea->sortField])."' ".$sql_add."
                              and `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."` <> '".mysql_real_escape_string($lock_current[$this->currentArea->dbPrimaryKey])."'
                              order by `".mysql_real_escape_string($this->currentArea->sortField)."` desc limit 1";

                        $rs_under  = mysql_query($sql_under);
                        if($rs_under)
                        if($lock_under = mysql_fetch_assoc($rs_under)) { //under record (need to be moved up)
                            if($lock_under[$this->currentArea->sortField] == $lock_current[$this->currentArea->sortField]) {

                                $sql_update = "update `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."`
                                    set `".mysql_real_escape_string($this->currentArea->sortField)."` = `".mysql_real_escape_string($this->currentArea->sortField)."` + 1
                                    where `".mysql_real_escape_string($this->currentArea->sortField)."` >= ".mysql_real_escape_string($lock_under[$this->currentArea->sortField])."
                                    and `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."` <> '".mysql_real_escape_string($lock_current[$this->currentArea->dbPrimaryKey])."'  ".$sql_add."";

                                $rs_update = mysql_query($sql_update);
                                if(!$rs_update) {
                                    trigger_error($sql_update." ".mysql_error());
                                }
                            }else {

                                $sql_update = "update `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."`
                                    set `".mysql_real_escape_string($this->currentArea->sortField)."` = ".(int)$lock_current[$this->currentArea->sortField]."
                                    where `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."` = '".mysql_real_escape_string($lock_under[$this->currentArea->dbPrimaryKey])."' ".$sql_add." limit 1";

                                $rs_update = mysql_query($sql_update);
                                if(!$rs_update)
                                trigger_error($sql_update." ".mysql_error());

                                $sql_update = "update `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."`
                                    set `".mysql_real_escape_string($this->currentArea->sortField)."` = ".(int)$lock_under[$this->currentArea->sortField]."
                                    where `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."` = '".mysql_real_escape_string($lock_current[$this->currentArea->dbPrimaryKey])."'  ".$sql_add." limit 1";

                                $rs_update = mysql_query($sql_update);
                                if(!$rs_update)
                                trigger_error($sql_update." ".mysql_error());
                            }

                        }
                    }else trigger_error($sql." Element does not exist");
                    echo "document.location = document.location;";

                    if(method_exists($this->currentArea, 'afterSort')) {
                        $this->currentArea->afterSort();
                    }


                    \Db::disconnect();
                    exit;
                }
                break;
                case 'delete': {
                    if($this->allowDelete($this->currentArea, $_REQUEST['key_id'])) {
                        $this->delete($this->currentArea, $_REQUEST['key_id']);
                        echo "delete_row(".$_POST['key_id'].")";
                    }
                    \Db::disconnect();
                    exit;
                }
                break;
                case 'insert':
                    $allowInsert = true;
                    $parameters = array();  //parameters for main sql for current area table.
                    foreach($this->currentArea->elements as $key => $element) {
                        $new_error = $element->checkField("i_n_".$key, "insert", $this->currentArea);
                        if ($new_error != null)
                        $this->errors[$key] = $new_error;
                    }
                    if (sizeof($this->errors) == 0) {

                        //allow insert
                        if(method_exists($this->currentArea, 'allowInsert')) {
                            $allowInsert = $this->currentArea->allowInsert($this->currentArea->currentId);
                            if(!$allowInsert) {
                                if(method_exists($this->currentArea, 'lastError')) {
                                    echo "
              <html>
                <head>
                  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=".CHARSET."\" />
                </head>
                <body>
                  <script type=\"text/javascript\">                  
                    alert('".addslashes($this->currentArea->lastError('insert'))."');
                  </script>
                 </body>
               </html>
                    ";
                                }else {
                                    echo "
              <html>
                <head>
                  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=".CHARSET."\" />
                </head>
                <body>
                  <script type=\"text/javascript\">                  
                    alert('".addslashes($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'cant_insert'))."');
                  </script>
                 </body>
               </html>
                    
                    ";
                                }
                                return false;
                            }
                        }
                        //allow insert



                        if(method_exists($this->currentArea, 'beforeInsert')) {
                            $this->currentArea->beforeInsert();
                        }


                        foreach($this->currentArea->elements as $key => $element) {
                            $new_parameter = $element->getParameters("insert", "i_n_".$key, $this->currentArea);
                            if ($new_parameter) {
                                $parameters[$key] = $new_parameter;
                                $parameters[$key]['secure'] = $element->secure;
                                $parameters[$key]['secureKey'] = $element->secureKey;
                            }

                        }
                        $sql = "insert into `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."` set  `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."`= DEFAULT ";
                        $need_comma = true;
                        if ($this->level > 0) {
                            $sql .= ", `".mysql_real_escape_string($this->currentArea->dbReference)."` = '".mysql_real_escape_string($this->upArea->parentId)."' ";
                            $need_comma = true;
                        }

                        $sortFieldDefined = false;

                        foreach($parameters as $key => $parameter) {
                            if($parameter['name'] == $this->currentArea->sortField){
                                $sortFieldDefined = true;
                            }

                            if($parameter['value'] === null)
                                $value =  " NULL ";
                            else
                                $value =  "'".mysql_real_escape_string($parameter['value'])."'";

                            if($parameter['secure']){
                                $value = "AES_ENCRYPT(".$value.", '".$parameter['secureKey']."')";
                            }

                            if ($need_comma)
                            $sql .= ", `".mysql_real_escape_string($parameter['name'])."` = ".$value." ";
                            else {
                                $sql .= " `".mysql_real_escape_string($parameter['name'])."` = ".$value." ";
                                $need_comma = true;
                            }
                        }

            if(!$sortFieldDefined && $this->currentArea->sortField){
                            if ($need_comma)
                            $sql .= ", `".mysql_real_escape_string($this->currentArea->sortField)."` = 0 ";
                            else {
                                $sql .= " `".mysql_real_escape_string($this->currentArea->sortField)."` = 0 ";
                                $need_comma = true;
                            }
                        }


                        $rs = mysql_query($sql);
                        if (!$rs) {
                            trigger_error("Impossible to insert new data ".$sql." ".mysql_error());
                        }else {
                            $lastInsertId = mysql_insert_id();

                            /* update sort field value */
                            if($this->currentArea->sortable && $this->currentArea->sortField && $this->currentArea->newRecordPosition == 'top') {
                                /* increase all sort field numbers */
                                $sql = "update `".mysql_real_escape_string(DB_PREF."".$this->currentArea->dbTable)."` set `".mysql_real_escape_string($this->currentArea->sortField)."` = `".mysql_real_escape_string($this->currentArea->sortField)."` + 1";
                                $rs = mysql_query($sql);
                                if(!$rs) trigger_error("Can't change sort numbers ".$sql." ".mysql_error());

                                /* find lowest walue */
                                if (($this->level > 0))
                                $sql = "select min(`".mysql_real_escape_string($this->currentArea->sortField)."`) as 'min_value' from `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."` where `".mysql_real_escape_string($this->currentArea->dbReference)."` = '".mysql_real_escape_string($this->upArea->parentId)."' and `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."` <> ".(int)$lastInsertId." ";
                                else
                                $sql = "select min(`".mysql_real_escape_string($this->currentArea->sortField)."`) as 'min_value' from `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."` where `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."` <> ".(int)$lastInsertId." ";
                                $rs = mysql_query($sql);
                                if($rs) {
                                    if($lock = mysql_fetch_assoc($rs)) {
                                        /* update inserted record to have the smallest sort field number*/
                                        $sql2 = "update `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."` set `".mysql_real_escape_string($this->currentArea->sortField)."` = (".(int)$lock['min_value']." - 1) where `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."` = '".mysql_real_escape_string($lastInsertId)."' ";
                                        $rs = mysql_query($sql2);
                                        if(!$rs)
                                        trigger_error($sql." ".mysql_error());
                                    }
                                }
                                else trigger_error("Can't find lowest value ".$sql." ".mysql_error());
                            }
                            if($this->currentArea->sortable && $this->currentArea->sortField && $this->currentArea->newRecordPosition == 'bottom') {
                                /* find biggest walue */
                                if (($this->level > 0))
                                $sql = "select max(`".mysql_real_escape_string($this->currentArea->sortField)."`) as 'max_value' from `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."` where `".mysql_real_escape_string($this->currentArea->dbReference)."` = '".mysql_real_escape_string($this->upArea->parentId)."' and `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."` <> ".(int)$lastInsertId."";
                                else
                                $sql = "select max(`".mysql_real_escape_string($this->currentArea->sortField)."`) as 'max_value' from `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."` where `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."` <> ".(int)$lastInsertId."";
                                $rs = mysql_query($sql);
                                if($rs) {
                                    if($lock = mysql_fetch_assoc($rs)) {
                                        /* update inserted record to have the smallest sort field number*/
                                        $sql2 = "update `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."` set `".mysql_real_escape_string($this->currentArea->sortField)."` = (".(int)$lock['max_value']." + 1) where `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."` = '".mysql_real_escape_string($lastInsertId)."' ";
                                        $rs = mysql_query($sql2);
                                        if(!$rs)
                                        trigger_error($sql." ".mysql_error());
                                    }
                                }
                                else trigger_error("Can't find lowest value ".$sql." ".mysql_error());
                            }

                            foreach($this->currentArea->elements as $key => $element) {
                                $new_parameter = $element->processInsert("i_n_".$key, $lastInsertId, $this->currentArea);
                            }
                            if(method_exists($this->currentArea, 'afterInsert')) {
                                $this->currentArea->afterInsert($lastInsertId);
                                //$this->upArea->afterInsert($lastInsertId);
                            }

                        }

                        $answer = "
            <html>
              <head>
                <meta http-equiv=\"Content-Type\" content=\"text/html; charset=".CHARSET."\" />
              </head>
              <body>
                <script type=\"text/javascript\">
                  //parent.window.location.reload(true); throws browser alert to post data again if there was a search before insert.
                  
                  //parent.window.location.href = parent.window.location.href; don't work with #xxx
                                   
                  var ipUrl = parent.window.location.href.split('#');
                
                  parent.window.location = ipUrl[0] + '&anticache=' + Math.floor(Math.random()*1000); //Firefox5 don't reload if the same url.
                </script>
              </body></html>
          ";              
                        echo $answer;



                        \Db::disconnect();
                        exit;
                    }else {

                        $answer = "
          <html>
            <head>
              <meta http-equiv=\"Content-Type\" content=\"text/html; charset=".CHARSET."\" />
            </head>
            <body>
              <script type=\"text/javascript\">
                var errors = new Array();
                var new_fields = new Array();
                ";

                        foreach($this->errors as $key => $error) {
                            $answer .= "
                 var error = ['i_n_".addslashes($key)."', '".addslashes($error)."'];
                 errors.push(error);
                 ";
                        }


                        $answer .=  "
            </script>
            </body></html>
        ";		          
                        echo $answer;
                        \Db::disconnect();
                        exit;
                    }



                    break;


                case 'update':
                    $parameters = array();  //parameters for main sql for current area table.
                    foreach($this->currentArea->elements as $key => $element) {
                        $new_error = $element->checkField("i_n_".$key, "update", $this->currentArea);
                        if ($new_error != null)
                        $this->errors[$key] = $new_error;
                    }
                    if (sizeof($this->errors) == 0) {


                        if(method_exists($this->currentArea, 'allowUpdate')) {
                            $allowUpdate = $this->currentArea->allowUpdate($this->currentArea->currentId);
                            if(!$allowUpdate) {
                                if(method_exists($this->currentArea, 'lastError')) {
                                    echo "
            <html>
              <head>
                <meta http-equiv=\"Content-Type\" content=\"text/html; charset=".CHARSET."\" />
              </head>
              <body>
                <script type=\"text/javascript\">                  
                  alert('".addslashes($this->currentArea->lastError('update'))."');
                </script>
               </body>
             </html>
                  ";
                                }else {
                                    echo "
            <html>
              <head>
                <meta http-equiv=\"Content-Type\" content=\"text/html; charset=".CHARSET."\" />
              </head>
              <body>
                <script type=\"text/javascript\">                  
                  alert('".addslashes($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'cant_update'))."');
                </script>
               </body>
             </html>
                  
                  ";
                                }
                                return false;
                            }
                        }



                        if(method_exists($this->currentArea, 'beforeUpdate')) {
                            $this->currentArea->beforeUpdate($this->currentArea->currentId);
                        }


                        foreach($this->currentArea->elements as $key => $element) {
                            $new_parameter = $element->getParameters("update", "i_n_".$key, $this->currentArea);
                            if ($new_parameter){
                                $parameters[$key] = $new_parameter;
                                $parameters[$key]['secure'] = $element->secure;
                                $parameters[$key]['secureKey'] = $element->secureKey;
                            }

                        }

                        $main_update = false;
                        if(sizeof($parameters) > 0) {
                            $sql = "update `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."` set ";
                            $need_comma = false;

                            foreach($parameters as $key => $parameter) {
                                if($parameter['value'] === null)
                                $value =  " NULL ";
                                else
                                $value =  "'".mysql_real_escape_string($parameter['value'])."'";

                                if($parameter['secure']){
                                    $value = "AES_ENCRYPT(".$value.", '".$parameter['secureKey']."')";
                                }

                                if ($need_comma)
                                $sql .= ", `".mysql_real_escape_string($parameter['name'])."` = ".$value." ";
                                else {
                                    $sql .= " `".mysql_real_escape_string($parameter['name'])."` = ".$value." ";
                                    $need_comma = true;
                                }
                            }
                            $sql .= " where `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."` = '".mysql_real_escape_string($this->currentArea->currentId)."' ";
                            $rs = mysql_query($sql);
                            if (!$rs) {
                                trigger_error("Impossible to update ".$sql);
                            }else
                            $main_update = true;
                        }else $main_update = true;


                        if($main_update) {
                            foreach($this->currentArea->elements as $key => $element)
                            $new_parameter = $element->processUpdate("i_n_".$key, $this->currentArea->currentId, $this->currentArea);
                        }

                        if(method_exists($this->currentArea, 'afterUpdate')) {
                            $this->currentArea->afterUpdate($this->currentArea->currentId);
                        }


                        $answer = "
              <html>
              <head>
                <meta http-equiv=\"Content-Type\" content=\"text/html; charset=".CHARSET."\" />
              </head>
              <body>
                <script type=\"text/javascript\">
                //parent.window.location.reload(true);
                //parent.window.location.href = parent.window.location.href;
                //parent.window.location.href = '".str_replace('&amp;', '&',$this->generateUrlBack())."';
                parent.window.location.href = '".str_replace('&amp;', '&',$_POST['back_url'])."';
                </script>
              </body></html>
            ";		          
                        echo $answer;
                        \Db::disconnect();
                        exit;
                    }else {
                        $answer = "
               <html>
               <head>
                 <meta http-equiv=\"Content-Type\" content=\"text/html; charset=".CHARSET."\" />
               </head>
               <body>
                 <script type=\"text/javascript\">
                   var errors = new Array();
                   var new_fields = new Array();
                   ";

                        foreach($this->errors as $key => $error) {
                            $answer .= "
                  var error = ['i_n_".addslashes($key)."', '".addslashes($error)."'];
                  errors.push(error);
                  ";
                        }


                        $answer .=  "
               </script>
               </body></html>
              ";		          
                        echo $answer;
                        \Db::disconnect();
                        exit;
                    }






                    break;
            }
        }
    }

    function manage() {
        global $stdModDb;
        $stdModDb = new StdModDb();


        if(isset($_REQUEST['type']) && $_REQUEST['type'] == 'ajax') {
            $answer = $this->ajaxAction($this->currentArea, $this->upArea);
            return $answer;
        }

        if (isset($_REQUEST['action']) && $_REQUEST['action'] != null)
        $this->makeActions();


        //sort
        if(isset($_GET['sortBy']) && $_GET['sortBy'] != null) {
            if ($this->currentArea->sortBy == $_GET['sortBy']) {
                if($this->currentArea->sortDirection == "asc")
                $this->currentArea->sortDirection = "desc";
                else
                $this->currentArea->sortDirection = "asc";
            }else {
                $this->currentArea->sortBy = $_GET['sortBy'];
                $this->currentArea->sortDirection = "asc";
            }
        }
        //eof sort


        //pages
        if (isset($_GET['page'][$this->level]) && $_GET['page'][$this->level] != null)
        $this->currentArea->currentPage = (int)$_GET['page'][$this->level];

        if (isset($_GET['pageSize'][$this->level]) && $_GET['pageSize'][$this->level] != null) {
            $this->currentArea->rowsPerPage = (int)$_GET['pageSize'][$this->level];
        }

        //pages

        if($this->treeDepth > 0 && !isset($_GET['road']) || isset($_GET['road']) && $this->treeDepth > sizeof($_GET['road'])) {
            //echo $this->linkToFirstTreeNode($this->childArea);
            header("location:".str_replace("&amp;", "&", $this->linkToFirstTreeNode($this->childArea)));
        }


        $answer = $this->makeHtml();
        


        return $answer;
    }

    function linkToFirstTreeNode($area, $depth = 0, $parentId = null, $url = '') {

        if(!$area->nameElement) {
            return;
        }
        if($this->treeDepth <= $depth) {
            return;
        }

        $answer = '';

        $sql = " select * from `".mysql_real_escape_string(DB_PREF.$area->dbTable)."` where 1 ";
        if ($parentId)
        $sql .= " and `".mysql_real_escape_string($area->dbReference)."` = '".mysql_real_escape_string($parentId)."' ";
        if($area->whereCondition)
        $sql .= " and ".$this->currentArea->whereCondition;  //extra condition to sql where part

        if ($this->currentArea->orderBy != "") {
            $sql .= " order by `".mysql_real_escape_string($this->currentArea->orderBy)."` ";
            if($this->currentArea->orderDirection != "")
            $sql .= " ".$this->currentArea->orderDirection." ";
        }
        $rs = mysql_query($sql);
        if($rs) {
            if($this->treeDepth == $depth + 1) {
                if($lock = mysql_fetch_assoc($rs)) {
                    $answer = $this->generateUrlRoot().$url.'&amp;road[]='.$lock[$area->dbPrimaryKey].'&amp;title[]='.$area->nameElement->previewValue($lock, $area);
                }
            }else {
                while($answer == '' && $lock = mysql_fetch_assoc($rs)) {
                    if($area->childArea) {
                        $answer = $this->linkToFirstTreeNode($area->childArea, $depth + 1, $lock[$area->dbPrimaryKey], $url.'&amp;road[]='.$lock[$area->dbPrimaryKey].'&amp;title[]='.$area->nameElement->previewValue($lock, $area));
                    }
                }
            }
        }

        return $answer;
    }


    function printTree() {
        $answer = '';
        $answer .= '
  <div id="treeView">
   
		'.$this->printTreeNode($this->childArea).'

   </div>
	<!-- id="treeView" -->	
  <div onmousedown="getPos(event)" id="splitterBar" >
  </div>
		';
        return $answer;
    }

    function printTreeNode($area, $depth = 0, $parentId = null, $url = '', $parent_selected = true) {

        if(!$area->nameElement) {
            return;
        }
        if($this->treeDepth <= $depth)
        return;

        $answer = '';

        $sql = " select *
    from `".mysql_real_escape_string(DB_PREF.mysql_real_escape_string($area->dbTable))."` where 1 ";
        if ($parentId)
        $sql .= " and `".mysql_real_escape_string($area->dbReference)."` = '".mysql_real_escape_string($parentId)."' ";
        if($area->whereCondition)
        $sql .= " and ".$area->whereCondition;  //extra condition to sql where part

        if ($area->orderBy != "") {
            $sql .= " order by `".mysql_real_escape_string($area->orderBy)."` ";
            if($area->orderDirection != "")
            $sql .= " ".$area->orderDirection." ";
        }
        //`".mysql_real_escape_string($area->dbPrimaryKey)."` as current_id, `".mysql_real_escape_string($area->nameElement->dbField)."` as name_value
        $rs = mysql_query($sql);
        if($rs) {
            while($lock = mysql_fetch_assoc($rs)) {
                $subnodes = '';
                $thisSelected = $parent_selected && isset($_GET['road'][$depth]) && $lock[$area->dbPrimaryKey] == $_GET['road'][$depth];

                if($area->childArea) {
                    $subnodes .= $this->printTreeNode($area->childArea, $depth + 1, $lock[$area->dbPrimaryKey], $url.'&amp;road[]='.$lock[$area->dbPrimaryKey].'&amp;title[]='.$area->nameElement->previewValue($lock, $area), $thisSelected);
                }

                if($subnodes != '')
                $leafClass = 'class="menu_tree_parent"';
                else
                $leafClass='';

                if($this->treeDepth == $depth+1) {
                    if($thisSelected)
                    $nodeClass = 'class="menu_tree menu_tree_selected"';
                    else
                    $nodeClass = 'class="menu_tree menu_tree_leaf"';
                }else
                $nodeClass = 'class="menu_tree menu_tree_parent"';

                if($subnodes != '')
                $answer .= '<div '.$nodeClass.' style="padding-left:'.($depth*15).'px;"><div '.$leafClass.'><a>'.$area->nameElement->previewValue($lock, $area).'</a></div></div>';
                else
                $answer .= '<div '.$nodeClass.' onclick="document.location = \''.$this->generateUrlRoot().$url.'&amp;road[]='.$lock[$area->dbPrimaryKey].'&amp;title[]='.$area->nameElement->previewValue($lock, $area).'\'" style="padding-left:'.($depth*15).'px;"><div '.$leafClass.'><a href="'.$this->generateUrlRoot().$url.'&amp;road[]='.$lock[$area->dbPrimaryKey].'&amp;title[]='.$area->nameElement->previewValue($lock, $area).'">'.$area->nameElement->previewValue($lock, $area).'</a></div></div>';

                $answer .= $subnodes;
            }
        }
        return $answer;
    }

    function makeHtml() {
        global $stdModDb;
        global $parametersMod;
        global $cms;
        $stdModDb = new StdModDb();

        $answer = '';

        $answer .= '
		
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ImpressPages</title>
    
    <script type="text/javascript">
        var ip = {
            baseUrl : '.json_encode(BASE_URL).',
            libraryDir : '.json_encode(LIBRARY_DIR).',
            themeDir : '.json_encode(THEME_DIR).',
            moduleDir : '.json_encode(MODULE_DIR).',
            theme : '. json_encode(THEME) .',
            zoneName : '.json_encode(null).',
            pageId : '.json_encode(null).',
            revisionId : '.json_encode(null).',
        };
    </script>
    
    <script src="'.BASE_URL.LIBRARY_DIR.'js/default.js"></script>
    <script src="'.BASE_URL.LIBRARY_DIR.'js/tabs.js"></script>
    <script src="' . BASE_URL . LIBRARY_DIR . 'js/jquery/jquery.js"></script>
    <script src="' . BASE_URL . LIBRARY_DIR . 'js/tiny_mce/jquery.tinymce.js"></script>
    <script src="' . BASE_URL . '?g=standard&amp;m=configuration&amp;a=tinymceConfig"></script>    
</head>
	 
<body> <!-- display loading until page is loaded-->
			
      <!-- display loading util page is loaded-->
      <div id="loading" style="height: 60px; z-index: 1001; width: 100%; position: fixed; left:0px; top: 180px;">
				<table style="margin-left: auto; margin-right: auto;"><tr>
					<td style="font-family: Verdana, Tahoma, Arial; font-size: 14px; color: #505050; padding: 30px 33px; background-color: #d9d9d9; border: 1px solid #bcbdbf;">
						'.htmlspecialchars($parametersMod->getValue('standard', 'configuration', 'system_translations', 'loading')).'
					</td>
				</tr></table>
			</div>
      <script>
      //<![CDATA[
				LibDefault.addEvent(window, \'load\', init);
	      				
	      function init(){
		      document.getElementById(\'loading\').style.display = \'none\';
	      }
      //]]>
      </script>
      <!-- display loading until page is loaded-->		
		
		<link href="'.BASE_URL.MODULE_DIR.'developer/std_mod/design/style.css" type="text/css" rel="stylesheet" media="screen">
		<script src="'.BASE_URL.MODULE_DIR.'developer/std_mod/design/scripts.js"></script>
		<script src="'.BASE_URL.LIBRARY_DIR.'js/tabs.js"></script>
		<script src="'.LIBRARY_DIR.'js/windowsize.js" ></script>
		<script src="'.LIBRARY_DIR.'js/mouse.js" ></script>
		<script src="'.LIBRARY_DIR.'js/positioning.js" ></script>
		<script src="'.LIBRARY_DIR.'js/default.js" ></script>
		
		';


        if($this->level < $this->treeDepth) {
            $content = '';
        }elseif(isset($_GET['road_edit'])) {
            $content = $this->printForm();
        }else {
            $content = $this->printData();
        }

        $answer .= '
		
		 <div class="all" onmousemove="setPos(event)" onmouseup="mouseButtonPos=\'up\'">';
        $answer .= '<script>LibDefault.addEvent(window,\'load\',perVisaPloti);</script>';

        if($this->treeDepth > 0) {
            $answer .= $this->printTree();
        }
        $answer .= '<div id="bodyView">';

        $answer .= '  <div id="content">
			'.$this->beforeContent.'
			'.$this->printErrors().'
			'.$this->printRoad().'
			'.$content.'
      '.$this->afterContent.'
		   </div><!-- class="content" -->
		  </div><!-- id="bodyView" -->';

        $answer .=
            '<div class="clear">
		  </div>
		 </div><!-- class="all" -->
		 

<script type="text/javascript">
  //<![CDATA[
  $(\'.mceEditor\').tinymce(ipTinyMceConfigMed);
  //]]>
</script>		 
		   </body>
      </html>   
		 ';



        return $answer;
    }

    function printRoad() {
        $answer = '<div id="backtrace_path">';
        if($this->level > 0 && $this->level > $this->treeDepth)
        $answer .= '<a href="'.$this->generateUrlBack().'"><img class="backtrace_path_img" src="'.BASE_URL.MODULE_DIR.'developer/std_mod/design/atgal.png" alt=""></a>';
        else
        $answer .= '<a><img class="backtrace_path_img" src="'.BASE_URL.MODULE_DIR.'developer/std_mod/design/atgal_disabled.png" alt=""></a>';
        $answer .= $this->road;
        $answer .= '</div>';
        return $answer;
    }

    function makeActions() { {
        switch($_REQUEST['action']) {
            case 'delete':

                break;
            case 'resort':
                if (isset($_REQUEST['sortField']) && $_REQUEST['sortField'] != null) {
                    foreach($_REQUEST['sortField'] as $key => $field_number) {
                        if(isset($_REQUEST['sortField_'.$field_number]) && $_REQUEST['sortField_'.$field_number] != null && is_numeric($_REQUEST['sortField_'.$field_number])) {
                            $sql = " update `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."` set `".mysql_real_escape_string($this->currentArea->getSortField)."` = '".(int)$_REQUEST['sortField_'.$field_number]."' where `".mysql_real_escape_string($this->currentArea->dbPrimaryKey)."` = '".mysql_real_escape_string($field_number)."'";
                            $rs = mysql_query($sql);
                            if (!$rs) {
                                trigger_error("Can't change sort order ".$sql." ".mysql_error());
                            }
                        }
                    }
                }
                break;
            case 'insert':

                break;
            case 'update':
                break;
        }
    }



    }

    function printErrors() {
        $answer = '';
        foreach($this->globalErrors as $key => $value) {
            $answer .= '<p class="error">'.htmlspecialchars($value).'</p>';
        }
        return $answer;
    }

    function searched($area) {
        $answer = false;
        foreach($area->elements as $key => $value) {
            if ($value->searchable && isset($_REQUEST['search_'.$key]) && $_REQUEST['search_'.$key] != null)
            $answer = true;
        }
        return $answer;
    }


    function allowDelete($area, $id) {
        global $parametersMod;
        $allowDelete = true;
        if(method_exists($area, 'allowDelete')) {
            $allowDelete = $area->allowDelete($id);
            if(!$allowDelete) {
                if(method_exists($area, 'lastError')) {
                    echo "alert('".addslashes($area->lastError('delete'))."');";
                }else {
                    echo "alert('".addslashes($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'cant_delete'))."');";
                }
                return false;
            }

            $child = $area->getArea();
            if ($child != null) {
                $sql = " select `".mysql_real_escape_string($child->dbPrimaryKey)."` as 'key' from `".mysql_real_escape_string(DB_PREF.$child->dbTable)."` where `".mysql_real_escape_string($child->dbReference)."` = '".mysql_real_escape_string($id)."' ";
                $rs = mysql_query($sql);
                if ($rs) {
                    $limit = mysql_num_rows($rs);
                    for($i=0; $i<$limit; $i++) {
                        $lock = mysql_fetch_assoc($rs);
                        $allowDelete = $this->allowDelete($child, $lock['key']);
                        if(!$allowDelete) {
                            return false;
                        }
                    }
                }else {
                    trigger_error("Can't get children ".$sql." ".mysql_error());
                }
            }
        }
        return $allowDelete;
    }


    function delete(&$currentArea, $id) {
        if(method_exists($currentArea, 'beforeDelete')) {
            $currentArea->beforeDelete($id);
        }


        $child =& $currentArea->getArea();
        if ($child != null) {
            $sql = " select `".mysql_real_escape_string($child->dbPrimaryKey)."` as 'key' from `".mysql_real_escape_string(DB_PREF.$child->dbTable)."` where `".mysql_real_escape_string($child->dbReference)."` = '".mysql_real_escape_string($id)."' ";
            $rs = mysql_query($sql);
            if ($rs) {
                $limit = mysql_num_rows($rs);
                for($i=0; $i<$limit; $i++) {
                    $lock = mysql_fetch_assoc($rs);
                    $this->delete($child, $lock['key']);
                }
            }else {
                trigger_error("Can't get children ".$sql." ".mysql_error());
            }
        }
        foreach($currentArea->elements as $key => $element)
        $new_parameter = $element->processDelete($currentArea, $id);
        $sql = "delete from `".mysql_real_escape_string(DB_PREF.$currentArea->dbTable)."` where `".mysql_real_escape_string($currentArea->dbPrimaryKey)."` = '".mysql_real_escape_string($id)."' ";
        $rs = mysql_query($sql);
        if (!$rs) {
            trigger_error("Unable to delete ".$sql." ".mysql_error());
        }

        if(method_exists($currentArea, 'afterDelete')) {
            $currentArea->afterDelete($id);
        }



    }

    function printForm() {
        $area = $this->currentArea;
        $level = $this->level;

        global $parametersMod;

        if (
        $this->level <= 0
        ||
        (!$this->currentArea || !$this->currentArea->allowUpdate)

        )
        return;

        $elements = $area->elements;

        $select_expr = '*, ';
        foreach($elements AS $element){
            if (empty($element->dbField) && get_class($element) !== 'Modules\developer\std_mod\ElementImage')
                continue;
            elseif ($element->secure){
                if(get_class($element) == 'Modules\developer\std_mod\ElementImage'){
                    foreach($element->copies AS $copy){
                        $select_expr .= "AES_DECRYPT(".$copy['dbField'].", '".$element->secureKey."') AS ".$copy['dbField'].", ";
                    }
                }else{
                    $select_expr .= "AES_DECRYPT(".$element->dbField.", '".$element->secureKey."') AS ".$element->dbField.", ";
                }
            }
        }
        $select_expr = substr($select_expr, 0, -2);

        $sqlCurrent = " select ".$select_expr." from `".mysql_real_escape_string(DB_PREF.$area->dbTable)."` where `".mysql_real_escape_string($area->dbPrimaryKey)."` = '".mysql_real_escape_string($area->currentId)."'";

        $rs = mysql_query($sqlCurrent);
        if($rs) {
            if(!$lock = mysql_fetch_assoc($rs)) {
                trigger_error('Record does not exist.');
            }
        } else {
            trigger_error($sql.' '.mysql_error());
        }


        $answer = '';
        $answer .= '<form class="stdMod" target="std_mod_update_f_iframe" action="'.$this->generateUrlLevel($this->level).'" method="post" enctype="multipart/form-data">';
        $answer .= '<div class="search">';
        $answer .= '<input type="hidden" name="type" value="ajax">';
        $answer .= '<input type="hidden" name="back_url" value="'.$this->generateUrlBack().'">';
        $answer .= '<input type="hidden" name="action" value="update">';
        foreach($area->elements as $key => $value) {
            if ($this->errors != null && isset($this->errors[$key]))
            $tmpError = $this->printError($this->errors[$key]);
            else
            $tmpError = '';
            if($value->visibleOnUpdate)
            $answer .= '<a name="std_mod_anchor_i_n_'.$key.'"></a><span class="label bolder">'.$value->title.$value->description.'</span><br /><p style="display: none;" id="std_mod_update_f_error_i_n_'.$key.'" class="error"></p>'.$tmpError.$value->printFieldUpdate('i_n_'.$key, $lock, $area)."<br><br>";
            /*else
             $answer .= $tmpError.$value->printFieldUpdate('i_n_'.$key, $lock, $area);*/
        }
        $answer .= '
        <input class="knob bolder" type="submit" value="'.$parametersMod->getValue('developer', 'std_mod','admin_translations','save').'">
				</div>
      </form>
			<div class="separator"></div>
			';

        $resetStr = '';
        foreach($area->elements as $key => $element) {
            if($element->visibleOnUpdate) {
                if($element->visibleOnUpdate) {
                    $resetStr .= ' document.getElementById(\'std_mod_update_f_error_i_n_'.$key.'\').innerHTML = \'\';
                ';
                    $resetStr .= ' document.getElementById(\'std_mod_update_f_error_i_n_'.$key.'\').style.display = \'none\';
                ';
                }
            }
        }


        $answer .='
          <script>
          //<![CDATA[ 
            function std_mod_update_f_answer(){
              '.$resetStr.'

              if(window.frames[\'std_mod_update_f_iframe\'].errors){
                var errors = window.frames[\'std_mod_update_f_iframe\'].errors;
                for(var i=0; i<errors.length; i++){
                  document.getElementById(\'std_mod_update_f_error_\' + errors[i][0]).innerHTML = errors[i][1];
                  document.getElementById(\'std_mod_update_f_error_\' + errors[i][0]).style.display = \'block\';

                  if(i == 0){            
                      document.location = \'#std_mod_anchor_\' + errors[i][0];                  
                  }
                  
                }
                
              }
              
              if(window.frames[\'std_mod_update_f_iframe\'].script){
                eval(window.frames[\'std_mod_update_f_iframe\'].script);
              }
            }      
            //]]>    
          </script>          
          <iframe onload="std_mod_update_f_answer()" name="std_mod_update_f_iframe" width="0" height="0" frameborder="0">Your browser does not support iframes.</iframe>
          ';      

        return $answer;
    }

    function printError($error) {
        if ($error != '')
        return '<p class="error">'.$error.'</p><br>';
    }

    function printNew() { //form for new element in current area



        global $parametersMod;

        $answer = '';
        $answer .= '<form id="std_mod_new_f" target="std_mod_new_f_iframe" action="'.$this->generateUrlLevel($this->level).'" method="post" enctype="multipart/form-data">';
        $answer .= '<div class="search">';
        $answer .= '<input type="hidden" name="type" value="ajax">';
        $answer .= '<input type="hidden" name="action" value="insert">';
        foreach($this->currentArea->elements as $key => $element) {
            if($element->visibleOnInsert) {
                if(isset($this->errors[$key]))
                $tmpError = '<p class="error">'.$this->errors[$key].'</p>';
                else
                $tmpError = '';

                if($element->visibleOnInsert)
                $answer .= '<a name="std_mod_anchor_i_n_'.$key.'"></a><span class="label bolder">'.$element->title.$element->description.'</span><br /><p style="display: none;" id="std_mod_new_f_error_i_n_'.$key.'" class="error"></p>'.$tmpError.$element->printFieldNew('i_n_'.$key,$this->upArea->parentId, $this->currentArea)."<br><br>";
                else
                $answer .= $tmpError.$element->printFieldNew('i_n_'.$key,$this->upArea->parentId, $this->currentArea);
            }
        }
        $answer .= '
        <input style="width: 0; height:0; overflow:hidden; border: 0;" class="knob bolder" type="submit" value="'.$parametersMod->getValue('developer', 'std_mod','admin_translations','save').'">
				</div>
      </form>';

        $resetStr = '';
        foreach($this->currentArea->elements as $key => $element) {
            if($element->visibleOnInsert) {
                if($element->visibleOnInsert) {
                    $resetStr .= ' document.getElementById(\'std_mod_new_f_error_i_n_'.$key.'\').innerHTML = \'\';
                ';
                    $resetStr .= ' document.getElementById(\'std_mod_new_f_error_i_n_'.$key.'\').style.display = \'none\';
                ';
                }
            }
        }


        $answer .='
          <script>
          //<![CDATA[ 
            function std_mod_new_f_answer(){
              '.$resetStr.'

              if(window.frames[\'std_mod_new_f_iframe\'].errors){
                var errors = window.frames[\'std_mod_new_f_iframe\'].errors;
                for(var i=0; i<errors.length; i++){
                  document.getElementById(\'std_mod_new_f_error_\' + errors[i][0]).innerHTML = errors[i][1];
                  document.getElementById(\'std_mod_new_f_error_\' + errors[i][0]).style.display = \'block\';
                  if(i == 0){     
                      document.location = \'#std_mod_anchor_\' + errors[i][0];                  
                  }
                }
              }
              
              if(window.frames[\'std_mod_new_f_iframe\'].script){
                eval(window.frames[\'std_mod_new_f_iframe\'].script);
              }
            }      
            //]]>    
          </script>          
          <iframe onload="std_mod_new_f_answer()" name="std_mod_new_f_iframe" width="0" height="0" frameborder="0">Your browser does not support iframes.</iframe>
          ';      
        return $answer;
    }

    function printSearchFields($area, $level) {
        global $parametersMod;
        global $cms;
        $empty = true;
        $answer = '<div class="search"><form id="std_mod_search_f" method="POST" class="stdMod" action="'.$this->generateUrlLevel($this->level).'">';
        foreach($area->elements as $key => $value) {
            if($value->searchable) {
                $answer .= '<span class="label bolder">'.$value->title.'</span><br />'.$value->printSearchField($level, $key, $area)."<br><br>";
                $empty = false;
            }
        }
        $answer .= '<input style="width: 0; height:0; border: 0; overflow:hidden;" type="submit" value="'.$parametersMod->getValue('developer', 'std_mod','admin_translations','search').'"></form></div>';

        if ($empty)
        return;
        else
        return $answer;
    }

    function calculatePages($sql) {
        $rs_count = mysql_query($sql);

        if ($rs_count) {
            $count = mysql_num_rows($rs_count);
            $this->pagesCount = ceil($count/$this->currentArea->rowsPerPage);
            if($this->pagesCount < 1)
            $this->pagesCount = 1;

            if($this->currentArea->currentPage > $this->pagesCount - 1)
            $this->currentArea->currentPage = $this->pagesCount -1;

            if($this->currentArea->currentPage < 0)
            $this->currentArea->currentPage = 0;

        }else
        $this->pagesCount = null;
    }

    function printPages() {
        global $parametersMod;
        $answer = '';

        $answer .= '
			<script>
				function std_mod_change_page(){
					document.getElementById(\'std_mod_pages_form\').action= document.getElementById(\'std_mod_pages_form\').action + 
					\'&page['.$this->level.']=\' + 
					(document.getElementById(\'std_mod_pages_current_id\').value - 1) + 
					\'&pageSize['.$this->level.']=\' +
					document.getElementById(\'std_mod_pages_select_id\').value;				
				
					document.getElementById(\'std_mod_pages_form\').submit();				
				}
			</script>
			
			<form id="std_mod_pages_form" action="'.$this->generateUrlLevel($this->level, 'page').'" onsubmit="std_mod_change_page()" method="POST">
			 <div class="paging">
				<div class="clear"></div>
				 <select onchange="std_mod_change_page()" id="std_mod_pages_select_id" name="std_mod_pages_select">
					<option value="'.$this->currentArea->rowsPerPage.'">'.$parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'record_count_on_one_page').'</option>
					<option value="10">10</option>
					<option value="20">20</option>
					<option value="50">50</option>
					<option value="200">200</option>
					<option value="500">500</option>
					<option value="1000">1000</option>
					<option value="10000">10000</option>
					<option value="100000">100000</option>
				 </select>
				 <a href="'.$this->generateUrlPage($this->currentArea->currentPage-1, $this->currentArea->rowsPerPage).'" title="'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'previous_page')).'">
					<img src="'.LIBRARY_DIR.'/php/standard_module/design/previous_page.png" title="'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'previous_page')).'">
				 </a>
				 <input id="std_mod_pages_current_id" class="page_number" type="text" name="std_mod_pages_current" value="'.($this->currentArea->currentPage+1).'" />
				 <span class="page_number_n">/ '.$this->pagesCount.'</span>
				 <a href="'.$this->generateUrlPage($this->currentArea->currentPage+1, $this->currentArea->rowsPerPage).'" title="'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'next_page')).'">
					<img src="'.LIBRARY_DIR.'/php/standard_module/design/next_page.png" title="'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'next_page')).'">
				 </a>
			 </div>
			</form>
			
			
			';


        return $answer;

        $pages = "";
        if ($rs_count) {
            $count = mysql_num_rows($rs_count);
            if ($count/$this->currentArea->rowsPerPage > 1) {
                for($i=0; $i<$count/$this->currentArea->rowsPerPage; $i++) {
                    if ($this->currentArea->currentPage == $i)
                    $pages .= ' <a class="navigation" href="'.$this->generateUrlPage($i, $this->currentArea->rowsPerPage).'"><b><u>'.($i+1).'</u></b></a> ';
                    else
                    $pages .= ' <a class="navigation" href="'.$this->generateUrlPage($i, $this->currentArea->rowsPerPage).'">'.($i+1).'</a> ';
                }
            }
        }
        if($pages)
        $pages = "<p>".$pages."</p>";
        return $pages;
    }

    function printTable($sql) {

        global $parametersMod;
        global $cms;



        $answer = '';
        $pages = $this->printPages();



        $answer .= '
		<div id="sheet1div">
			<div class="tabs">
				'.$this->printTabs().'

				'.$pages.'
			</div>
	<div class="fake_table">
    <table cellspacing="0" cellpadding="0" id="sheet1">
        ';


        $rs = mysql_query($sql);
        if ($rs) {
            $limit = mysql_num_rows($rs);





            //column names
            $answer .= '<tr>';

            if($this->currentArea->allowUpdate)
            $answer .= '<th>&nbsp;</th>';
            if($this->currentArea->childArea !=  null && $this->currentArea->childArea->visible)
            $answer .= '<th>&nbsp;</th>';
            if ($this->currentArea->sortable && $this->currentArea->sortField != null) {
                $tmpSortDir = "asc";
                $class="button";
                if($this->currentArea->sortField != '' && $this->currentArea->sortField == $this->currentArea->orderBy) {
                    if($this->currentArea->orderDirection == "asc") {
                        $tmpSortDir = "desc";
                        $class="move_up";
                    }else {
                        $tmpSortDir = "asc";
                        $class="down";
                    }

                }

                if($this->currentArea->sortType == 'numbers')
                $answer .= '<th class="header"><a class="'.$class.'" href="'.$this->generateUrlSort($this->currentArea->sortField, $tmpSortDir).'"><b>'.$parametersMod->getValue('developer', 'std_mod','admin_translations','sort_field').'</b></a></th>';
                if($this->currentArea->sortType == 'pointers')
                $answer .= '<th class="header"><b>'.$parametersMod->getValue('developer', 'std_mod','admin_translations','sort_field').'</b></th>';
            }

            foreach($this->currentArea->elements as $key => $value) {
                if($value->showOnList) {
                    if ($value->order) {
                        $class="button";
                        if($this->currentArea->orderBy == $value->dbField) {
                            if($this->currentArea->orderDirection == "asc") {
                                $tmpSortDirection = "desc";
                                $class="move_up";
                            }else {
                                $tmpSortDirection = "asc";
                                $class="down";
                            }
                        }else {
                            $tmpSortDirection = "asc";
                        }

                        $answer .= '<th class="header"><a class="'.$class.'" href="'.$this->generateUrlSort($value->dbField, $tmpSortDirection).'"><b>'.htmlspecialchars($value->title).'</b></a></th>';
                    }else
                    $answer .= '<th class="header"><b>'.htmlspecialchars($value->title).'</b></th>';
                }
            }

            if($this->currentArea->allowDelete)
            $answer .= '<th>&nbsp;</th>';

            $answer .= '
      </tr>';
            //end column names

            for($i=0; $i<$limit; $i++) {
                $lock = mysql_fetch_assoc($rs);
                $answer .= '<tr id="table_row_'.$lock[$this->currentArea->dbPrimaryKey].'">';

                if(isset($this->currentArea->nameElement)) {
                    $tmpTitle = $this->currentArea->nameElement->previewValue($lock, $this->currentArea);
                } else {
                    if(isset($this->currentArea->childArea))
                    $tmpTitle = $this->currentArea->childArea->title;
                    else
                    $tmpTitle = '';
                }


                if($this->currentArea->allowUpdate )
                $answer .= '<td><a class="edit" href="'.$this->generateUrlEdit($lock["".$this->currentArea->dbPrimaryKey], $tmpTitle) .'&amp;road_edit=1" title="'.$parametersMod->getValue('developer', 'std_mod','admin_translations','edit').'">&nbsp;</a></td>';
                if($this->currentArea->childArea !=  null && $this->currentArea->childArea->visible)
                $answer .= '<td><a class="goIn" href="'.$this->generateUrlEdit($lock["".$this->currentArea->dbPrimaryKey], $tmpTitle) .'" title="'.$parametersMod->getValue('developer', 'std_mod','admin_translations','go_in').'">&nbsp;</a></td>';


                if ($this->currentArea->sortable && $this->currentArea->sortField != null) {
                    if($this->currentArea->sortType == 'numbers')
                    $answer .= '<td ><form onSubmit="return false;" action=""><input onblur="LibDefault.ajaxMessage(\''.$this->generateUrlLevel($this->level).'&amp;type=ajax\', \'action=new_row_number&amp;key_id='.$lock[$this->currentArea->dbPrimaryKey].'&amp;new_row_number=\' + encodeURIComponent(this.value))" style="width:30px;" name="sortField_'.$lock[$this->currentArea->dbPrimaryKey].'" value="'.$lock[$this->currentArea->sortField].'" /></form></td>';
                    if($this->currentArea->sortType == 'pointers')
                    $answer .= '<td >
                        <a class="move_down"
                        title="'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'move_down')).'"
                        onclick="
                        LibDefault.ajaxMessage(\''.$this->generateUrlLevel($this->level).'&amp;type=ajax\', \'action=row_number_increase&amp;key_id='.$lock[$this->currentArea->dbPrimaryKey].'\')
                        "
                        >&nbsp;</a>
                        <a
                        title="'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'move_up')).'"
                        onclick="
                        LibDefault.ajaxMessage(\''.$this->generateUrlLevel($this->level).'&amp;type=ajax\', \'action=row_number_decrease&amp;key_id='.$lock[$this->currentArea->dbPrimaryKey].'\')
                        "
                        class="move_up">&nbsp;</a></td>';
                }

                foreach($this->currentArea->elements as $key => $value) {
                    if($value->showOnList) {
                        $answer .= '<td >'.$value->previewValue($lock, $this->currentArea).'&nbsp;</td>';
                    }
                }


                if($this->currentArea->allowDelete)
                $answer .= '<td><a class="delete" onclick="confirmDelete(\''.$this->generateUrlLevel($this->level).'&amp;type=ajax\', \'action=delete&amp;key_id='.$lock[$this->currentArea->dbPrimaryKey].'\', \''.$parametersMod->getValue('developer', 'std_mod','admin_translations','are_you_sure_you_wish_to_delete').'\'); return false;" title="'.$parametersMod->getValue('developer', 'std_mod','admin_translations','delete').'">&nbsp;</a></td>';
                $answer .='
          </tr>';
            }
        }else {
            trigger_error("Area not found. ".$sql." ".mysql_error());
        }

        $answer .= '</table>
		</div>
				&nbsp;
		</div>';
        return $answer;
    }

    function printData() {

        if (!$this->currentArea)
        return;



        $elements = $this->currentArea->elements;

        $select_expr = '*, ';
        foreach($elements AS $element){
            if (empty($element->dbField) && get_class($element) !== 'Modules\developer\std_mod\ElementImage')
                continue;
            elseif ($element->secure){
                if(get_class($element) == 'Modules\developer\std_mod\ElementImage'){
                    foreach($element->copies AS $copy){
                        $select_expr .= "AES_DECRYPT(".$copy['dbField'].", '".$element->secureKey."') AS ".$copy['dbField'].", ";
                    }
                }else{
                    $select_expr .= "AES_DECRYPT(".$element->dbField.", '".$element->secureKey."') AS ".$element->dbField.", ";
                }
            }
        }
        $select_expr = substr($select_expr, 0, -2);

        $sql_pages = " select ".$select_expr." from `".mysql_real_escape_string(DB_PREF.$this->currentArea->dbTable)."` where 1 ";

        if (($this->level > 0))
        $sql_pages .= " and `".mysql_real_escape_string($this->currentArea->dbReference)."` = '".mysql_real_escape_string($this->upArea->parentId)."' ";
        if($this->currentArea->whereCondition)
        $sql_pages .= " and ".$this->currentArea->whereCondition;  //extra condition to sql where part
        foreach($this->currentArea->elements as $key => $value) {
            if ($value->searchable && isset($_REQUEST['search'][$this->level][$key]) && $_REQUEST['search'][$this->level][$key] != null && $_REQUEST['search'][$this->level][$key] != '') {
                $filterOption = $value->getFilterOption($_REQUEST['search'][$this->level][$key], $this->currentArea);
                if ($filterOption != '') {
                    $sql_pages .= " and ".$value->getFilterOption($_REQUEST['search'][$this->level][$key], $this->currentArea);
                }
            }

        }

        $this->calculatePages($sql_pages);

        $sql = $sql_pages;
        if(isset($_GET['sortField'][$this->level]) && isset($_GET['sortDir'][$this->level]) && ($_GET['sortDir'][$this->level] == "desc" || $_GET['sortDir'][$this->level] == "asc"))
        $sql .= " order by `".mysql_real_escape_string($_GET['sortField'][$this->level])."` ".mysql_real_escape_string($_GET['sortDir'][$this->level])." ";
        else {
            if ($this->currentArea->orderBy != "") {
                $sql .= " order by `".mysql_real_escape_string($this->currentArea->orderBy)."` ";
                if($this->currentArea->orderDirection != "")
                $sql .= " ".$this->currentArea->orderDirection." ";
            }
        }
        if($this->currentArea->rowsPerPage != '' && $this->currentArea->rowsPerPage!= 0) {
            $sql .= " limit ".(int)$this->currentArea->rowsPerPage*$this->currentArea->currentPage.", ".(int)$this->currentArea->rowsPerPage." ";
        }


        $answer = '';

        $answer .= '
    <script>
      function confirmDelete(action, parameters, question){
        var answer = confirm(question); 
        if (answer)
		
			LibDefault.ajaxMessage(action, parameters);
           //document.location.href = action;
      }
	  
			function delete_row(id){
				var tmp_el = document.getElementById(\'table_row_\' + id);
				tmp_el.parentNode.removeChild(tmp_el);	  
			}
    </script> 		
		';


        $answer .= $this->printTable($sql);

        return $answer;

    }

    function printTabs() {
        global $parametersMod;

        $answer = '';



        $answer .= '<ul>';

        if($this->currentArea->allowInsert)
        $answer .= '
			<li onclick="document.getElementById(\'std_mod_new_popup_body\').style.height=(LibWindow.getWindowHeight() - 160) + \'px\'; document.getElementById(\'std_mod_new_popup\').style.display = \'block\';"><span>'.$parametersMod->getValue('developer','std_mod','admin_translations','new').'</span></li>';

        if($this->currentArea->searchable)
        $answer .= '<li onclick="document.getElementById(\'std_mod_search_popup_body\').style.height=(LibWindow.getWindowHeight() - 160) + \'px\'; document.getElementById(\'std_mod_search_popup\').style.display = \'block\';"><span>'.$parametersMod->getValue('developer','std_mod','admin_translations','search').'</span></li>';


        $answer .= '</ul>';



        if($this->currentArea->allowInsert) {
            //form for new element in current area
            $answer .= '
				<div class="popup" id="std_mod_new_popup">
					<div id="std_mod_new_popup_border" class="popup_border">
						<div class="popup_head">
							<img 
								onmouseover="this.src=\''.BASE_URL.MODULE_DIR.'developer/std_mod/design/popup_close_hover.gif\'"
								onmouseout="this.src=\''.BASE_URL.MODULE_DIR.'developer/std_mod/design/popup_close.gif\'"
							src="'.BASE_URL.MODULE_DIR.'developer/std_mod/design/popup_close.gif" style="cursor: pointer; float: right;" onclick="std_mod_hide_popups()">
							'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod','admin_translations','new')).'
						</div>
						<div id="std_mod_new_popup_body" class="management">'.$this->printNew($this->errors).'</div>
						<div class="moduleControlButtons">
							<a onclick="document.getElementById(\'std_mod_new_f\').submit();" class="button">'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod','admin_translations','save')).'</a>
							<a onclick="std_mod_hide_popups();" class="button">'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod','admin_translations','cancel')).'</a>
							<div class="clear"></div>
						</div>
					</div>
				</div>';
        }
        if($this->currentArea->searchable) {
            $answer .= '
				<div class="popup" id="std_mod_search_popup"> 
					<div id="std_mod_search_popup_border" class="popup_border">
						<div class="popup_head">
							<img
								onmouseover="this.src=\''.BASE_URL.MODULE_DIR.'developer/std_mod/design/popup_close_hover.gif\'"
								onmouseout="this.src=\''.BASE_URL.MODULE_DIR.'developer/std_mod/design/popup_close.gif\'"
							src="'.BASE_URL.MODULE_DIR.'developer/std_mod/design/popup_close.gif" style="cursor: pointer; float: right;" onclick="std_mod_hide_popups()">
							'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod','admin_translations','search')).'
						</div>
						<div id="std_mod_search_popup_body" class="management">'.$this->printSearchFields($this->currentArea, $this->level).'</div>
						<div class="moduleControlButtons">
							<a onclick="document.getElementById(\'std_mod_search_f\').submit();" class="button">'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod','admin_translations','search')).'</a>
							<a onclick="std_mod_hide_popups();" class="button">'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod','admin_translations','cancel')).'</a>
							<div class="clear"></div>
						</div>
					</div>
				</div>';
        }

        if(sizeof($this->errors) != 0) {
            $answer .= '
			<script>
				document.getElementById(\'std_mod_new_popup_body\').style.height=(LibWindow.getWindowHeight() - 190) + \'px\';
				document.getElementById(\'std_mod_new_popup\').style.display = \'block\';
			</script>
			';
        }



        $answer .= '
		
		<script>

		function std_mod_new_popup_click(e){ 
			var border = document.getElementById(\'std_mod_new_popup_border\');
			var mouseY = LibMouse.getMouseY(e);
			var mouseX = LibMouse.getMouseX(e);
			if(mouseY < LibPositioning.getY(border) || mouseY > LibPositioning.getY(border) + border.offsetHeight)
				std_mod_hide_popups();
			if(mouseX < LibPositioning.getX(border) || mouseX > LibPositioning.getX(border) + border.offsetWidth)
				std_mod_hide_popups();							
		}
		
		function std_mod_search_popup_click(e){
			var border = document.getElementById(\'std_mod_search_popup_border\');
			var mouseY = LibMouse.getMouseY(e);
			var mouseX = LibMouse.getMouseX(e);
			if(mouseY < LibPositioning.getY(border) || mouseY > LibPositioning.getY(border) + border.offsetHeight)
				std_mod_hide_popups();
			if(mouseX < LibPositioning.getX(border) || mouseX > LibPositioning.getX(border) + border.offsetWidth)
				std_mod_hide_popups();							
		}


		if(document.getElementById(\'std_mod_search_popup\'))
			LibDefault.addEvent(document.getElementById(\'std_mod_search_popup\'), \'mousedown\', std_mod_search_popup_click);

		if(document.getElementById(\'std_mod_new_popup\'))
			LibDefault.addEvent(document.getElementById(\'std_mod_new_popup\'), \'mousedown\', std_mod_new_popup_click);

		function std_mod_hide_popups(){
				if(document.getElementById(\'std_mod_new_popup\'))
					document.getElementById(\'std_mod_new_popup\').style.display = \'none\';
				if(document.getElementById(\'std_mod_search_popup\'))
					document.getElementById(\'std_mod_search_popup\').style.display = \'none\';
			}
		</script>
		
		';

        $answer .= '<div id="stdModTab1"></div>';


        return $answer;
    }

    function generateUrlRoot() {
        global $cms;
        return $cms->generateUrl($cms->curModId);

    }

    function generateUrlBack() {
        return $this->generateUrlLevel($this->level - 1);
    }

    function generateUrlEdit($id, $title) {
        return $this->generateUrlLevel($this->level)."&amp;road[]=".(int)$id."&amp;title[]=".urlencode($title);
    }

    function generateUrlLevel($max_level, $ignore = null) {
        global $cms;

        $tmp_url = '';
        for($i=0; $i<= $max_level;$i++) {
            if($i != $max_level) {
                if($tmp_url != '')
                $tmp_url.='&amp;';
                $tmp_url.='road[]='.(int)$_GET['road'][$i];
                $tmp_url.='&amp;';
                $tmp_url.='title[]='.urlencode($_GET['title'][$i]);
            }

            if($i == $max_level && $ignore != 'sort') {
                if(isset($_GET['sortField'][$i]))
                $tmp_url.='&amp;sortField['.$i.']='.$_GET['sortField'][$i];

                if(isset($_GET['sortDir'][$i]))
                $tmp_url.='&amp;sortDir['.$i.']='.$_GET['sortDir'][$i];
            }


            if($i == $max_level && $ignore != 'page') {
                if(isset($_GET['page'][$i]))
                $tmp_url.='&amp;page['.$i.']='.$_GET['page'][$i];
                if(isset($_GET['pageSize'][$i]))
                $tmp_url.='&amp;pageSize['.$i.']='.(int)$_GET['pageSize'][$i];

            }

            if(isset($_REQUEST['search'][$i]) && is_array($_REQUEST['search'][$i])) {
                foreach($_REQUEST['search'][$i] as $key => $value) {
                    if(is_array($value)) {
                        foreach($value as $key2 => $value2) { //bool_field have an array of values for one field
                            $tmp_url .= '&amp;search['.$i.']['.$key.']['.$key2.']='.urlencode($value2);
                        }
                    } else {
                        $tmp_url .= '&amp;search['.$i.']['.$key.']='.urlencode($value);
                    }
                }
            }
        }

        if($max_level == $this->level && isset($_GET['road_edit']))
        $tmp_url .= '&amp;road_edit=1';
        return $cms->generateurl($cms->curModId, $tmp_url);
    }

    function generateUrlSort($field, $direction) {
        $url = $this->generateUrlLevel($this->level, 'sort');
        $url .= '&amp;sortField['.$this->level.']='.$field.'&amp;sortDir['.$this->level.']='.$direction;
        return $url;
    }

    /*
     $page - page number
     $size - records count in one page
     */
    function generateUrlPage($page, $size) {
        $url = $this->generateUrlLevel($this->level, 'page');
        $url .= '&amp;page['.$this->level.']='.$page.'&amp;pageSize['.$this->level.']='.(int)$size;
        return $url;
    }



}


