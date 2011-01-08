<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2011 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Library\Php\StandardModule;


if (!defined('BACKEND')) exit;

require_once (LIBRARY_DIR.'php/standard_module/std_mod_db.php');
require_once (LIBRARY_DIR.'php/standard_module/std_mod_html_output.php');

require_once (LIBRARY_DIR.'php/standard_module/std_mod_area.php');
require_once (LIBRARY_DIR.'php/standard_module/element.php');
require_once (LIBRARY_DIR.'php/standard_module/element_text.php');
require_once (LIBRARY_DIR.'php/standard_module/element_bool.php');
require_once (LIBRARY_DIR.'php/standard_module/element_text_lang.php');
require_once (LIBRARY_DIR.'php/standard_module/element_parameter.php');
require_once (LIBRARY_DIR.'php/standard_module/element_pass.php');
require_once (LIBRARY_DIR.'php/standard_module/element_select.php');
require_once (LIBRARY_DIR.'php/standard_module/element_select_lang.php');
require_once (LIBRARY_DIR.'php/standard_module/element_photo.php');
require_once (LIBRARY_DIR.'php/standard_module/element_file.php');
require_once (LIBRARY_DIR.'php/standard_module/element_wysiwyg.php');
require_once (LIBRARY_DIR.'php/standard_module/element_time.php');
require_once (LIBRARY_DIR.'php/standard_module/element_hidden.php');
require_once (LIBRARY_DIR.'php/standard_module/element_textarea.php');

class StandardModule {
  var $area;
  var $level;
  var $current_raea;
  var $up_area;
  var $road;
  var $errors;
  var $global_errors;
  var $before_content;
  var $after_content;

  var $tree_depth; //how deep show the tree on the left.
  function __construct($area, $tree_depth = 0) {
    $this->tree_depth = $tree_depth;
    $this->errors = array();
    $this->global_errors = array();
    $this->area = $area;
    if(isset($_GET['road']) && is_array($_GET['road']))
      $this->level = sizeof($_GET['road']);
    else
      $this->level = 0;

    // find current area
    $this->current_area =& $this->area;
    if(isset($_GET['road'])&& is_array($_GET['road']))
      for($i=0; $i<sizeof($_GET['road']); $i++) {
        if ($this->current_area) $this->current_area =& $this->current_area->get_area();
      }
    //end find current area

    //order

    if(isset($_GET['sort_field'][$this->level])) {
      $sortField = $_GET['sort_field'][$this->level];
      $sortField = str_replace('`','',$sortField);
      $this->current_area->order_by = $sortField;
    }

    if(isset($_GET['sort_dir'][$this->level]) && strtolower($_GET['sort_dir'][$this->level]) == "asc")
      $this->current_area->order_by_dir = "asc";
    if(isset($_GET['sort_dir'][$this->level]) && strtolower($_GET['sort_dir'][$this->level]) == "desc") {
      $this->current_area->order_by_dir = "desc";
    }
    //end order



    //find up_area
    $this->road = '';
    $this->up_area =& $this->area;
    if($tree_depth == 0) {
      $this->road .= '<a href="'.$this->generate_url_root().'" class="navigation">'.$this->up_area->name."</a>";
    }

    if(isset($_GET['road'])&& is_array($_GET['road']))
      for($i=0; $i<(sizeof($_GET['road'])-1); $i++) {
        if ($this->up_area) $this->up_area =& $this->up_area->get_area();
        if($i+1 >= $tree_depth) {
          if($this->road != '')
            $this->road .= '<a> / </a>';

          $this->road .= '<a href="'.$this->generate_url_level(($i+1)).'" class="navigation">'.$this->up_area->name."</a>";
        }
      }

    if(isset($_GET['road'])&&$this->current_area && !isset($_GET['road_edit'])) {
      if($this->road != '')
        $this->road .= '<a> / </a>';
      $this->road .= '<a href="'.$this->generate_url_level((sizeof($_GET['road']))).'" class="navigation">'.$this->current_area->name.'</a>';
    }

    if ($this->up_area && isset($_GET['road']) && isset($_GET['road'][(sizeof($_GET['road'])-1)]) && $_GET['road'][(sizeof($_GET['road'])-1)]!='') {
      $this->up_area->set_parent_id(str_replace('`', '', $_GET['road'][(sizeof($_GET['road'])-1)]));
    }

    //end find up_area

    if(method_exists($this->current_area, 'after_init')) {
      $this->current_area->after_init($this->up_area->parent_id);
    }

  }

  function ajax_action() {
    global $parametersMod;
    global $cms;
    if(isset($_POST['action'])) {
      switch($_POST['action']) {
        case 'new_row_number':

          $sql = "update `".DB_PREF.$this->current_area->db_table."` set `".mysql_real_escape_string($this->current_area->sort_field)."` = '".mysql_real_escape_string($_POST['new_row_number'])."'
				where `".$this->current_area->db_key."` = '".mysql_real_escape_string($_POST['key_id'])."'";
          $rs  = mysql_query($sql);
          if(!$rs)
            trigger_error($sql." ".mysql_error());
          \Db::disconnect();
          exit;
          break;
        case 'row_number_increase': {
            $sql_current = "select `".$this->current_area->db_key."`, `".mysql_real_escape_string($this->current_area->sort_field)."` from `".DB_PREF.$this->current_area->db_table."` where `".$this->current_area->db_key."` = '".mysql_real_escape_string($_POST['key_id'])."'";
            $rs_current  = mysql_query($sql_current);
            if($rs_current)
              if($lock_current = mysql_fetch_assoc($rs_current)) { //current record (need to be moved up)
                /*searching upper record*/

                if (($this->level > 0))
                  $sql_add = " and ".$this->current_area->get_db_reference()." = '".mysql_real_escape_string($this->up_area->get_parent_id())."' ";
                else
                  $sql_add = '';

                $sql_upper = "select `".$this->current_area->db_key."`, `".mysql_real_escape_string($this->current_area->sort_field)."`
						from `".DB_PREF.$this->current_area->db_table."` 
						where `".mysql_real_escape_string($this->current_area->sort_field)."` >= '".mysql_real_escape_string($lock_current[$this->current_area->sort_field])."' 
						and `".$this->current_area->db_key."` <> '".mysql_real_escape_string($lock_current[$this->current_area->db_key])."' ".$sql_add."
						order by `".mysql_real_escape_string($this->current_area->sort_field)."` asc limit 1";


                $rs_upper  = mysql_query($sql_upper);
                if($rs_upper)
                  if($lock_upper = mysql_fetch_assoc($rs_upper)) { //upper record (need to be moved down)
                    if($lock_upper[$this->current_area->sort_field] == $lock_current[$this->current_area->sort_field]) {

                      $sql_update = "update `".DB_PREF.$this->current_area->db_table."`
									set `".mysql_real_escape_string($this->current_area->sort_field)."` = `".mysql_real_escape_string($this->current_area->sort_field)."` - 1 
									where `".mysql_real_escape_string($this->current_area->sort_field)."` <= ".mysql_real_escape_string($lock_upper[$this->current_area->sort_field])." and `".$this->current_area->db_key."` <> '".mysql_real_escape_string($lock_current[$this->current_area->db_key])."' ".$sql_add." ";
                      $rs_update = mysql_query($sql_update);
                      if(!$rs_update)
                        trigger_error($sql." ".mysql_error());

                    }else {

                      $sql_update = "update `".DB_PREF.$this->current_area->db_table."`
									set `".mysql_real_escape_string($this->current_area->sort_field)."` = ".mysql_real_escape_string($lock_current[$this->current_area->sort_field])."
									where `".$this->current_area->db_key."` = '".mysql_real_escape_string($lock_upper[$this->current_area->db_key])."' ".$sql_add." limit 1";

                      $rs_update = mysql_query($sql_update);
                      if(!$rs_update)
                        trigger_error($sql_update." ".mysql_error());

                      $sql_update = "update `".DB_PREF.$this->current_area->db_table."`
									set `".mysql_real_escape_string($this->current_area->sort_field)."` = ".mysql_real_escape_string($lock_upper[$this->current_area->sort_field])." 
									where `".$this->current_area->db_key."` = '".mysql_real_escape_string($lock_current[$this->current_area->db_key])."' ".$sql_add." limit 1";

                      $rs_update = mysql_query($sql_update);
                      if(!$rs_update)
                        trigger_error($sql." ".mysql_error());
                    }

                  }
              }else trigger_error($sql." Element does not exist");
            echo "
              window.location = window.location;					   
          ";

            \Db::disconnect();
            exit;
          }
          break;
        case 'row_number_decrease': {

            $sql_current = "select `".$this->current_area->db_key."`, `".mysql_real_escape_string($this->current_area->sort_field)."`
  				from `".DB_PREF.$this->current_area->db_table."` 
  				where `".$this->current_area->db_key."` = '".mysql_real_escape_string($_POST['key_id'])."'";

            $rs_current  = mysql_query($sql_current);
            if($rs_current)
              if($lock_current = mysql_fetch_assoc($rs_current)) { //current record (need to be moved down)
                /*searching under record*/

                if (($this->level > 0))
                  $sql_add = " and ".$this->current_area->get_db_reference()." = '".mysql_real_escape_string($this->up_area->get_parent_id())."' ";
                else
                  $sql_add = '';


                $sql_under = "select `".$this->current_area->db_key."`, `".mysql_real_escape_string($this->current_area->sort_field)."`
  						from `".DB_PREF.$this->current_area->db_table."` 
  						where `".mysql_real_escape_string($this->current_area->sort_field)."` <= '".mysql_real_escape_string($lock_current[$this->current_area->sort_field])."' ".$sql_add."
  						and `".$this->current_area->db_key."` <> '".mysql_real_escape_string($lock_current[$this->current_area->db_key])."'
  						order by `".mysql_real_escape_string($this->current_area->sort_field)."` desc limit 1";

                $rs_under  = mysql_query($sql_under);
                if($rs_under)
                  if($lock_under = mysql_fetch_assoc($rs_under)) { //under record (need to be moved up)
                    if($lock_under[$this->current_area->sort_field] == $lock_current[$this->current_area->sort_field]) {

                      $sql_update = "update `".DB_PREF.$this->current_area->db_table."`
  									set `".mysql_real_escape_string($this->current_area->sort_field)."` = `".mysql_real_escape_string($this->current_area->sort_field)."` + 1
  									where `".mysql_real_escape_string($this->current_area->sort_field)."` >= ".mysql_real_escape_string($lock_under[$this->current_area->sort_field])." and `".$this->current_area->db_key."` <> '".mysql_real_escape_string($lock_current[$this->current_area->db_key])."'  ".$sql_add."";

                      $rs_update = mysql_query($sql_update);
                      if(!$rs_update)
                        trigger_error($sql_update." ".mysql_error());
                    }else {

                      $sql_update = "update `".DB_PREF.$this->current_area->db_table."`
  									set `".mysql_real_escape_string($this->current_area->sort_field)."` = ".$lock_current[$this->current_area->sort_field]." 
  									where `".$this->current_area->db_key."` = '".mysql_real_escape_string($lock_under[$this->current_area->db_key])."' ".$sql_add." limit 1";

                      $rs_update = mysql_query($sql_update);
                      if(!$rs_update)
                        trigger_error($sql_update." ".mysql_error());

                      $sql_update = "update `".DB_PREF.$this->current_area->db_table."`
  									set `".mysql_real_escape_string($this->current_area->sort_field)."` = ".$lock_under[$this->current_area->sort_field]." 
  									where `".$this->current_area->db_key."` = '".mysql_real_escape_string($lock_current[$this->current_area->db_key])."'  ".$sql_add." limit 1";

                      $rs_update = mysql_query($sql_update);
                      if(!$rs_update)
                        trigger_error($sql_update." ".mysql_error());
                    }

                  }
              }else trigger_error($sql." Element does not exist");
            echo "document.location = document.location;";
            \Db::disconnect();
            exit;
          }
          break;
        case 'delete': {
            if($this->allow_delete($this->current_area, $_REQUEST['key_id'], $this->current_area, $_REQUEST['key_id'])) {
              $this->delete($this->current_area, $_REQUEST['key_id']);
              echo "delete_row(".$_POST['key_id'].")";
            }

            \Db::disconnect();
            exit;
          }
          break;
        case 'insert':

          $parameters = array();  //parameters for main sql for current area table.
          foreach($this->current_area->get_elements() as $key => $element) {
            $new_error = $element->check_field("i_n_".$key, "insert");
            if ($new_error != null)
              $this->errors[$key] = $new_error;
          }
          if (sizeof($this->errors) == 0) {
            foreach($this->current_area->get_elements() as $key => $element) {
              $new_parameter = $element->get_parameters("insert", "i_n_".$key);
              if ($new_parameter)
                $parameters[] = $new_parameter;

            }
            $sql = "insert into `".DB_PREF."".$this->current_area->get_db_table()."` set  `".$this->current_area->db_key."`= DEFAULT ";
            $need_comma = true;
            if ($this->level > 0) {
              $sql .= ", `".$this->current_area->get_db_reference()."` = '".mysql_real_escape_string($this->up_area->parent_id)."' ";
              $need_comma = true;
            }
            foreach($parameters as $key => $parameter) {

              if ($need_comma)
                $sql .= ", `".$parameter['name']."` = '".mysql_real_escape_string($parameter['value'])."' ";
              else {
                $sql .= " `".$parameter['name']."` = '".mysql_real_escape_string($parameter['value'])."' ";
                $need_comma = true;
              }
            }
            $rs = mysql_query($sql);
            if (!$rs)
              trigger_error("Impossible to insert new data ".$sql);
            else {
              $last_insert_id = mysql_insert_id();

              /* update sort field value */
              if($this->current_area->sort_field && $this->current_area->new_record_position == 'top') {
                /* increase all sort field numbers */
                $sql = "update `".DB_PREF."".$this->current_area->get_db_table()."` set `".mysql_real_escape_string($this->current_area->sort_field)."` = `".mysql_real_escape_string($this->current_area->sort_field)."` + 1";
                $rs = mysql_query($sql);
                if(!$rs) trigger_error("Can't change sort numbers ".$sql." ".mysql_error());

                /* find lowest walue */
                if (($this->level > 0))
                  $sql = "select min(`".mysql_real_escape_string($this->current_area->sort_field)."`) as 'min_value' from `".DB_PREF."".$this->current_area->get_db_table()."` where ".$this->current_area->get_db_reference()." = '".mysql_real_escape_string($this->up_area->get_parent_id())."' and `".$this->current_area->db_key."` <> ".(int)$last_insert_id." ";
                else
                  $sql = "select min(`".mysql_real_escape_string($this->current_area->sort_field)."`) as 'min_value' from `".DB_PREF."".$this->current_area->get_db_table()."` where `".$this->current_area->db_key."` <> ".(int)$last_insert_id." ";
                $rs = mysql_query($sql);
                if($rs) {
                  if($lock = mysql_fetch_assoc($rs)) {
                    /* update inserted record to have the smallest sort field number*/
                    $sql2 = "update `".DB_PREF."".$this->current_area->get_db_table()."` set `".mysql_real_escape_string($this->current_area->sort_field)."` = (".$lock['min_value']." - 1) where `".$this->current_area->db_key."` = ".$last_insert_id." ";
                    $rs = mysql_query($sql2);
                    if(!$rs)
                      trigger_error($sql." ".mysql_error());
                  }
                }
                else trigger_error("Can't find lowest value ".$sql." ".mysql_error());
              }
              if($this->current_area->sort_field && $this->current_area->new_record_position == 'bottom') {
                /* find biggest walue */
                if (($this->level > 0))
                  $sql = "select max(`".mysql_real_escape_string($this->current_area->sort_field)."`) as 'max_value' from `".DB_PREF."".$this->current_area->get_db_table()."` where ".$this->current_area->get_db_reference()." = '".mysql_real_escape_string($this->up_area->get_parent_id())."' and `".$this->current_area->db_key."` <> ".(int)$last_insert_id."";
                else
                  $sql = "select max(`".mysql_real_escape_string($this->current_area->sort_field)."`) as 'max_value' from `".DB_PREF."".$this->current_area->get_db_table()."` where `".$this->current_area->db_key."` <> ".(int)$last_insert_id."";
                $rs = mysql_query($sql);
                if($rs) {
                  if($lock = mysql_fetch_assoc($rs)) {
                    /* update inserted record to have the smallest sort field number*/
                    $sql2 = "update `".DB_PREF."".$this->current_area->get_db_table()."` set `".mysql_real_escape_string($this->current_area->sort_field)."` = (".$lock['max_value']." + 1) where `".$this->current_area->db_key."` = ".$last_insert_id." ";
                    $rs = mysql_query($sql2);
                    if(!$rs)
                      trigger_error($sql." ".mysql_error());
                  }
                }
                else trigger_error("Can't find lowest value ".$sql." ".mysql_error());
              }

              foreach($this->current_area->get_elements() as $key => $element) {
                $new_parameter = $element->process_insert("i_n_".$key, $this->current_area, $last_insert_id);
              }
              if(method_exists($this->current_area, 'after_insert')) {
                $this->current_area->after_insert($last_insert_id);
              }

              $elements = &$this->current_area->get_elements();
              for($i=0; $i<sizeof($elements); $i++) {
                $elements[$i]->reset("i_n_".$i);
              }

            }

            $answer = "
          <html>
            <head>
              <meta http-equiv=\"Content-Type\" content=\"text/html; charset=".CHARSET."\" />
            </head>
            <body>
              <script type=\"text/javascript\">
                parent.window.location.reload(true);
                parent.window.location.href = parent.window.location.href;
              
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
            /*$elements = &$this->current_area->get_elements();
            for($i=0; $i<sizeof($elements); $i++){
              $elements[$i]->memorize("i_n_".$i);
            }*/
          }



          break;


        case 'update':
          $parameters = array();  //parameters for main sql for current area table.
          foreach($this->up_area->get_elements() as $key => $element) {
            $new_error = $element->check_field("i_".$key, "update");
            if ($new_error != null)
              $this->errors[$key] = $new_error;
          }
          if (sizeof($this->errors) == 0) {

            if(method_exists($this->up_area, 'before_update')) {
              $this->up_area->before_update(mysql_real_escape_string($this->up_area->parent_id));
            }


            foreach($this->up_area->get_elements() as $key => $element) {
              $new_parameter = $element->get_parameters("update", "i_".$key);
              if ($new_parameter)
                $parameters[] = $new_parameter;

            }

            $main_update = false;
            if(sizeof($parameters) > 0) {
              $sql = "update `".DB_PREF."".$this->up_area->get_db_table()."` set ";
              $need_comma = false;

              foreach($parameters as $key => $parameter) {
                if ($need_comma)
                  $sql .= ", `".$parameter['name']."` = '".mysql_real_escape_string($parameter['value'])."' ";
                else {
                  $sql .= " `".$parameter['name']."` = '".mysql_real_escape_string($parameter['value'])."' ";
                  $need_comma = true;
                }
              }
              $sql .= " where `".$this->up_area->get_db_key()."` = '".mysql_real_escape_string($this->up_area->parent_id)."' ";
              $rs = mysql_query($sql);
              if (!$rs) {
                trigger_error("Impossible to update ".$sql);
              }else
                $main_update = true;
            }else $main_update = true;


            if($main_update) {
              foreach($this->up_area->get_elements() as $key => $element)
                $new_parameter = $element->process_update("i_".$key, $this->up_area, mysql_real_escape_string($this->up_area->parent_id));
            }

            if(method_exists($this->up_area, 'after_update')) {
              $this->up_area->after_update(mysql_real_escape_string($this->up_area->parent_id));
            }

            $elements = &$this->up_area->get_elements();
            for($i=0; $i<sizeof($elements); $i++) {
              $elements[$i]->reset();
            }

            $answer = "
              <html>
              <head>
                <meta http-equiv=\"Content-Type\" content=\"text/html; charset=".CHARSET."\" />
              </head>
              <body>
                <script type=\"text/javascript\">
                parent.window.location.reload(true);
                parent.window.location.href = parent.window.location.href;
                
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
    global $std_mod_db;
    $std_mod_db = new std_mod_db();


    if(isset($_REQUEST['type']) && $_REQUEST['type'] == 'ajax') {
      $answer = $this->ajax_action($this->current_area, $this->up_area);
      return $answer;
    }

    if (isset($_REQUEST['action']) && $_REQUEST['action'] != null)
      $this->make_actions();


    //sort
    if(isset($_GET['sort_by']) && $_GET['sort_by'] != null) {
      if ($this->current_area->sort_by == $_GET['sort_by']) {
        if($this->current_area->sort_direction == "asc")
          $this->current_area->sort_direction = "desc";
        else
          $this->current_area->sort_direction = "asc";
      }else {
        $this->current_area->sort_by = $_GET['sort_by'];
        $this->current_area->sort_direction = "asc";
      }
    }
    //eof sort


    //pages
    if (isset($_GET['page'][$this->level]) && $_GET['page'][$this->level] != null)
      $this->current_area->current_page = (int)$_GET['page'][$this->level];

    if (isset($_GET['page_size'][$this->level]) && $_GET['page_size'][$this->level] != null) {
      $this->current_area->rows_per_page = (int)$_GET['page_size'][$this->level];
    }

    //pages

    if($this->tree_depth > 0 && !isset($_GET['road']) || isset($_GET['road']) && $this->tree_depth > sizeof($_GET['road'])) {
      header("location:".str_replace("&amp;", "&", $this->link_to_first_tree_node($this->area)));
    }

    $answer = $this->make_html();

    return $answer;
  }

  function link_to_first_tree_node($area, $depth = 0, $parent_id = null, $url = '') {

    if(!$area->name_element) {
      return;
    }
    if($this->tree_depth <= $depth) {
      return;
    }

    $answer = '';

    $sql = " select `".$area->db_key."` as current_id, `".$area->name_element->db_field."` as name_value from `".DB_PREF."".$area->get_db_table()."` where 1 ";
    if ($parent_id)
      $sql .= " and ".$area->get_db_reference()." = '".mysql_real_escape_string($parent_id)."' ";
    if($area->get_where_condition())
      $sql .= " and ".$this->current_area->get_where_condition();  //extra condition to sql where part

    if ($this->current_area->order_by != "") {
      $sql .= " order by `".mysql_real_escape_string($this->current_area->order_by)."` ";
      if($this->current_area->order_by_dir != "")
        $sql .= " ".$this->current_area->order_by_dir." ";
    }
    $rs = mysql_query($sql);
    if($rs) {
      if($this->tree_depth == $depth + 1) {
        if($lock = mysql_fetch_assoc($rs)) {
          $answer = $this->generate_url_root().$url.'&amp;road[]='.$lock['current_id'];
        }
      }else {
        while($answer == '' && $lock = mysql_fetch_assoc($rs)) {
          if($area->area) {
            $answer = $this->link_to_first_tree_node($area->area, $depth + 1, $lock['current_id'], $url.'&amp;road[]='.$lock['current_id']);
          }
        }
      }
    }

    return $answer;
  }


  function print_tree() {
    $answer = '';
    $answer .= '
  <div id="treeView">
   
		'.$this->print_tree_node($this->area).'

   </div>
	<!-- id="treeView" -->	
  <div onmousedown="getPos(event)" id="splitterBar" >
  </div>
		';
    return $answer;
  }

  function print_tree_node($area, $depth = 0, $parent_id = null, $url = '', $parent_selected = true) {

    if(!$area->name_element) {
      return;
    }
    if($this->tree_depth <= $depth)
      return;

    $answer = '';

    $sql = " select `".$area->db_key."` as current_id, `".$area->name_element->db_field."` as name_value from `".DB_PREF."".$area->get_db_table()."` where 1 ";
    if ($parent_id)
      $sql .= " and ".$area->get_db_reference()." = '".mysql_real_escape_string($parent_id)."' ";
    if($area->get_where_condition())
      $sql .= " and ".$area->get_where_condition();  //extra condition to sql where part

    if ($area->order_by != "") {
      $sql .= " order by ".mysql_real_escape_string($area->order_by)." ";
      if($area->order_by_dir != "")
        $sql .= " ".$area->order_by_dir." ";
    }
    $rs = mysql_query($sql);
    if($rs) {
      while($lock = mysql_fetch_assoc($rs)) {
        $subnodes = '';
        $this_selected = $parent_selected && isset($_GET['road'][$depth]) && $lock['current_id'] == $_GET['road'][$depth];

        if($area->area) {
          $subnodes .= $this->print_tree_node($area->area, $depth + 1, $lock['current_id'], $url.'&amp;road[]='.$lock['current_id'], $this_selected);
        }

        if($subnodes != '')
          $leaf_class = 'class="menu_tree_parent"';
        else
          $leaf_class='';

        if($this->tree_depth == $depth+1) {
          if($this_selected)
            $node_class = 'class="menu_tree menu_tree_selected"';
          else
            $node_class = 'class="menu_tree menu_tree_leaf"';
        }else
          $node_class = 'class="menu_tree menu_tree_parent"';

        if($subnodes != '')
          $answer .= '<div '.$node_class.' style="padding-left:'.($depth*15).'px;"><div '.$leaf_class.'><a>'.$area->name_element->preview_value($lock['name_value']).'</a></div></div>';
        else
          $answer .= '<div '.$node_class.' onclick="document.location = \''.$this->generate_url_root().$url.'&amp;road[]='.$lock['current_id'].'\'" style="padding-left:'.($depth*15).'px;"><div '.$leaf_class.'><a href="'.$this->generate_url_root().$url.'&amp;road[]='.$lock['current_id'].'">'.$area->name_element->preview_value($lock['name_value']).'</a></div></div>';

        $answer .= $subnodes;
      }
    }

    return $answer;
  }

  function make_html() {
    global $std_mod_db;
    global $parametersMod;
    global $cms;
    $std_mod_db = new std_mod_db();

    $answer = '';

    $answer .= '
		
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
    <title>ImpressPages</title>
  <link REL="SHORTCUT ICON" HREF="'.BASE_URL.BACKEND_DIR.'design/images/impress-pages-cms.ico" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/default.js"></script>
  <script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/tabs.js"></script>
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
      <script type="text/javascript">
      //<![CDATA[
				LibDefault.addEvent(window, \'load\', init);
	      				
	      function init(){
		      document.getElementById(\'loading\').style.display = \'none\';
	      }
      //]]>
      </script>
      <!-- display loading until page is loaded-->		
		
		<link href="'.BASE_URL.LIBRARY_DIR.'php/standard_module/design/style.css" type="text/css" rel="stylesheet" media="screen" />		
		<script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'php/standard_module/design/scripts.js"></script>
		<script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/tabs.js"></script>
		<script type="text/javascript" src="'.LIBRARY_DIR.'js/windowsize.js" ></script>
		<script type="text/javascript" src="'.LIBRARY_DIR.'js/mouse.js" ></script>
		<script type="text/javascript" src="'.LIBRARY_DIR.'js/positioning.js" ></script>
		<script type="text/javascript" src="'.LIBRARY_DIR.'js/default.js" ></script>
		
		';

    if($this->level < $this->tree_depth) {
      $content = '';
    }elseif(isset($_GET['road_edit']))
      $content = $this->print_form();
    else
      $content = $this->print_data();

    $answer .= '
		
		 <div class="all" onmousemove="setPos(event)" onmouseup="mouseButtonPos=\'up\'">';
    $answer .= '<script type="text/javascript">LibDefault.addEvent(window,\'load\',perVisaPloti);</script>';

    if($this->tree_depth > 0) {
      $answer .= $this->print_tree();
    }
    $answer .= '<div id="bodyView">';

    $answer .= '  <div id="content">
			'.$this->before_content.'
			'.$this->print_errors().'
			'.$this->print_road().'
			'.$content.'
      '.$this->after_content.'
		   </div><!-- class="content" -->
		  </div><!-- id="bodyView" -->';

    $answer .=
            '<div class="clear">
		  </div>
		 </div><!-- class="all" -->
		 
		   </body>
      </html>   
		 ';



    return $answer;
  }

  function print_road() {
    $answer = '<div id="backtrace_path">';
    if($this->level > 0 && $this->level > $this->tree_depth)
      $answer .= '<a href="'.$this->generate_url_back().'"><img class="backtrace_path_img" src="'.BASE_URL.LIBRARY_DIR.'php/standard_module/design/atgal.png" alt="" /></a>';
    else
      $answer .= '<a><img class="backtrace_path_img" src="'.BASE_URL.LIBRARY_DIR.'php/standard_module/design/atgal_disabled.png" alt="" /></a>';
    $answer .= $this->road;
    $answer .= '</div>';
    return $answer;
  }

  function make_actions() { {
      switch($_REQUEST['action']) {
        case 'delete':

          break;
        case 'resort':
          if (isset($_REQUEST['sort_field']) && $_REQUEST['sort_field'] != null) {
            foreach($_REQUEST['sort_field'] as $key => $field_number) {
              if(isset($_REQUEST['sort_field_'.$field_number]) && $_REQUEST['sort_field_'.$field_number] != null && is_numeric($_REQUEST['sort_field_'.$field_number])) {
                $sql = " update `".DB_PREF."".$this->current_area->get_db_table()."` set `".mysql_real_escape_string($this->current_area->get_sort_field())."` = '".mysql_real_escape_string($_REQUEST['sort_field_'.$field_number])."' where ".$this->current_area->get_db_key()." = '".$field_number."'";
                $rs = mysql_query($sql);
                if (!$rs)
                  trigger_error("Can't change sort order ".$sql);
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

  function print_errors() {
    $answer = '';
    foreach($this->global_errors as $key => $value) {
      $answer .= '<p class="error">'.htmlspecialchars($value).'</p>';
    }
    return $answer;
  }

  function searched($area) {
    $answer = false;
    foreach($area->get_elements() as $key => $value) {
      if ($value->get_searchable() && isset($_REQUEST['search_'.$key]) && $_REQUEST['search_'.$key] != null)
        $answer = true;
    }
    return $answer;
  }

  function allow_delete($area, $id, $deletingArea, $deletingId) {
    global $parametersMod;

    $allow_delete = true;
    if(method_exists($area, 'allow_delete')) {
      $allow_delete = $area->allow_delete($id, $deletingArea, $deletingId);
      if(!$allow_delete) {
        if(method_exists($area, 'last_error'))
          echo "alert('".addslashes($area->last_error('delete'))."');";
        else
          echo "alert('".addslashes($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'cant_delete'))."');";
        return false;
      }
    }

    //check subareas does they allow to delete
    $child = $area->get_area();
    if ($child != null) {
      $sql = " select ".$child->get_db_key()." as 'key' from `".DB_PREF."".$child->get_db_table()."` where `".$child->get_db_reference()."` = '".$id."' ";
      $rs = mysql_query($sql);
      if ($rs) {
        $limit = mysql_num_rows($rs);
        for($i=0; $i<$limit; $i++) {
          $lock = mysql_fetch_assoc($rs);

          $allow_delete = $this->allow_delete($child, $lock['key'], $deletingArea, $deletingId);
          if(!$allow_delete) {
            return false;
          }
        }
      }else trigger_error("Can't get children ".$sql);
    }
    return $allow_delete;

  }

  function delete(&$current_area, $id) {


    if(method_exists($current_area, 'before_delete')) {
      $current_area->before_delete($id);
    }


    $child =& $current_area->get_area();
    if ($child != null) {
      $sql = " select ".$child->get_db_key()." as 'key' from `".DB_PREF."".$child->get_db_table()."` where `".$child->get_db_reference()."` = '".$id."' ";
      $rs = mysql_query($sql);
      if ($rs) {
        $limit = mysql_num_rows($rs);
        for($i=0; $i<$limit; $i++) {
          $lock = mysql_fetch_assoc($rs);
          $this->delete($child, $lock['key']);
        }
      }else trigger_error("Can't get children ".$sql);
    }
    foreach($current_area->get_elements() as $key => $element)
      $new_parameter = $element->process_delete($current_area, $id);
    $sql = "delete from `".DB_PREF."".$current_area->get_db_table()."` where `".$current_area->get_db_key()."` = '".$id."' ";
    $rs = mysql_query($sql);
    if (!$rs)
      trigger_error("Unable to delete ".$sql);

    if(method_exists($current_area, 'after_delete')) {
      $current_area->after_delete($id);
    }

  }

  function print_form() {
    $area = $this->up_area;
    $level = $this->level;

    global $parametersMod;

    if (
    $this->level <= 0
            ||
            (!$this->up_area || $this->up_area->permission == "read_only")

    )
      return;


    $answer = '';
    $answer .= '<form class="stdMod" target="std_mod_update_f_iframe" action="'.$this->generate_url_level($this->level).'" method="post" enctype="multipart/form-data">';
    $answer .= '<div class="search">';
    $answer .= '<input type="hidden" name="type" value="ajax"/>';
    $answer .= '<input type="hidden" name="action" value="update"/>';
    foreach($area->get_elements() as $key => $value) {
      if ($this->errors != null && isset($this->errors[$key]))
        $tmp_error = $this->print_error($this->errors[$key]);
      else
        $tmp_error = '';
      if($value->visible)
        $answer .= '<span class="label bolder">'.$value->name.'</span><br /><p style="display: none;" id="std_mod_update_f_error_i_n_'.$key.'" class="error"></p>'.$tmp_error.$value->print_field_update('i_'.$key, mysql_real_escape_string($area->get_parent_id()), $area)."<br /><br />";
      else
        $answer .= $tmp_error.$value->print_field_update('i_'.$key, mysql_real_escape_string($area->get_parent_id()), $area);
    }
    $answer .= '
        <input class="knob bolder" type="submit" value="'.$parametersMod->getValue('developer', 'std_mod','admin_translations','save').'"/>
				</div>
      </form>
			<div class="separator"></div>
			';

    $reset_str = '';
    foreach($area->get_elements() as $key => $element) {
      if(!$element->read_only) {

        if($element->visible) {
          $reset_str .= ' document.getElementById(\'std_mod_update_f_error_i_n_'.$key.'\').innerHTML = \'\';
                ';
          $reset_str .= ' document.getElementById(\'std_mod_update_f_error_i_n_'.$key.'\').style.display = \'none\';
                ';
        }
      }
    }


    $answer .='
          <iframe onload="std_mod_update_f_answer()" name="std_mod_update_f_iframe" width="0" height="0" frameborder="0">Your browser does not support iframes.</iframe>
          <script type="text/javascript">
          //<![CDATA[ 
            function std_mod_update_f_answer(){
              '.$reset_str.'

              if(window.frames[\'std_mod_update_f_iframe\'].errors){
                var errors = window.frames[\'std_mod_update_f_iframe\'].errors;
                for(var i=0; i<errors.length; i++){
                  document.getElementById(\'std_mod_update_f_error_\' + errors[i][0]).innerHTML = errors[i][1];
                  document.getElementById(\'std_mod_update_f_error_\' + errors[i][0]).style.display = \'block\';
                }
              }
              
              if(window.frames[\'std_mod_update_f_iframe\'].script){
                eval(window.frames[\'std_mod_update_f_iframe\'].script);
              }
            }      
            //]]>    
          </script>          
          ';      

    return $answer;
  }

  function print_error($error) {
    if ($error != '')
      return '<p class="error">'.$error.'</p><br>';
  }

  function print_new() { //form for new element in current area



    global $parametersMod;

    $answer = '';
    $answer .= '<form id="std_mod_new_f" target="std_mod_new_f_iframe" action="'.$this->generate_url_level($this->level).'" method="post" enctype="multipart/form-data">';
    $answer .= '<div class="search">';
    $answer .= '<input type="hidden" name="type" value="ajax"/>';
    $answer .= '<input type="hidden" name="action" value="insert"/>';
    foreach($this->current_area->get_elements() as $key => $element) {
      if(!$element->read_only) {
        if(isset($this->errors[$key]))
          $tmp_error = '<p class="error">'.$this->errors[$key].'</p>';
        else
          $tmp_error = '';
        if(get_class($element) != "element_time")

          if($element->visible)
            $answer .= '<span class="label bolder">'.$element->name.'</span><br /><p style="display: none;" id="std_mod_new_f_error_i_n_'.$key.'" class="error"></p>'.$tmp_error.$element->print_field_new('i_n_'.$key,mysql_real_escape_string($this->up_area->get_parent_id()), $this->current_area)."<br /><br />";
          else
            $answer .= $tmp_error.$element->print_field_new('i_n_'.$key,mysql_real_escape_string($this->up_area->get_parent_id()), $this->current_area);
      }
    }
    $answer .= '
        <input style="width: 0; height:0; overflow:hidden; border: 0;" class="knob bolder" type="submit" value="'.$parametersMod->getValue('developer', 'std_mod','admin_translations','save').'"/>
				</div>
      </form>';

    $reset_str = '';
    foreach($this->current_area->get_elements() as $key => $element) {
      if(!$element->read_only) {
        if($element->visible) {
          $reset_str .= ' document.getElementById(\'std_mod_new_f_error_i_n_'.$key.'\').innerHTML = \'\';
                ';
          $reset_str .= ' document.getElementById(\'std_mod_new_f_error_i_n_'.$key.'\').style.display = \'none\';
                ';
        }
      }
    }


    $answer .='
          <iframe onload="std_mod_new_f_answer()" name="std_mod_new_f_iframe" width="0" height="0" frameborder="0">Your browser does not support iframes.</iframe>
          <script type="text/javascript">
          //<![CDATA[ 
            function std_mod_new_f_answer(){
              '.$reset_str.'

              if(window.frames[\'std_mod_new_f_iframe\'].errors){
                var errors = window.frames[\'std_mod_new_f_iframe\'].errors;
                for(var i=0; i<errors.length; i++){
                  document.getElementById(\'std_mod_new_f_error_\' + errors[i][0]).innerHTML = errors[i][1];
                  document.getElementById(\'std_mod_new_f_error_\' + errors[i][0]).style.display = \'block\';
                }
              }
              
              if(window.frames[\'std_mod_new_f_iframe\'].script){
                eval(window.frames[\'std_mod_new_f_iframe\'].script);
              }
            }      
            //]]>    
          </script>          
          ';      
    return $answer;
  }

  function print_search_fields($area, $level) {
    global $parametersMod;
    global $cms;
    $empty = true;
    $answer = '<div class="search"><form id="std_mod_search_f" method="POST" class="stdMod" action="'.$this->generate_url_level($this->level).'">';
    foreach($area->get_elements() as $key => $value) {
      if($value->get_searchable()) {
        $answer .= '<span class="label bolder">'.$value->get_name().'</span><br />'.$value->print_search_field($level, $key)."<br /><br />";
        $empty = false;
      }
    }
    $answer .= '<input style="width: 0; height:0; border: 0; overflow:hidden;" type="submit" value="'.$parametersMod->getValue('developer', 'std_mod','admin_translations','search').'"/></form></div>';

    if ($empty)
      return;
    else
      return $answer;
  }

  function calculate_pages($sql) {
    $rs_count = mysql_query($sql);

    if ($rs_count) {
      $count = mysql_num_rows($rs_count);
      $this->pages_count = ceil($count/$this->current_area->rows_per_page);
      if($this->pages_count < 1)
        $this->pages_count = 1;

      if($this->current_area->current_page > $this->pages_count - 1)
        $this->current_area->current_page = $this->pages_count -1;

      if($this->current_area->current_page < 0)
        $this->current_area->current_page = 0;

    }else
      $this->pages_count = null;
  }

  function print_pages() {
    global $parametersMod;
    $answer = '';

    $answer .= '
			<script type="text/javascript">
				function std_mod_change_page(){
					document.getElementById(\'std_mod_pages_form\').action= document.getElementById(\'std_mod_pages_form\').action + 
					\'&page['.$this->level.']=\' + 
					(document.getElementById(\'std_mod_pages_current_id\').value - 1) + 
					\'&page_size['.$this->level.']=\' + 
					document.getElementById(\'std_mod_pages_select_id\').value;				
				
					document.getElementById(\'std_mod_pages_form\').submit();				
				}
			</script>
			
			<form id="std_mod_pages_form" action="'.$this->generate_url_level($this->level, 'page').'" onsubmit="std_mod_change_page()" method="POST">
			 <div class="paging">
				<div class="clear"></div>
				 <select onchange="std_mod_change_page()" id="std_mod_pages_select_id" name="std_mod_pages_select">
					<option value="'.$this->current_area->rows_per_page.'">'.$parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'record_count_on_one_page').'</option>
					<option value="10">10</option>
					<option value="20">20</option>
					<option value="50">50</option>
					<option value="200">200</option>
					<option value="500">500</option>
					<option value="1000">1000</option>
					<option value="10000">10000</option>
					<option value="100000">100000</option>
				 </select>
				 <a href="'.$this->generate_url_page($this->current_area->current_page-1, $this->current_area->rows_per_page).'" title="'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'previous_page')).'">
					<img src="'.LIBRARY_DIR.'/php/standard_module/design/previous_page.png" title="'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'previous_page')).'" />
				 </a>
				 <input id="std_mod_pages_current_id" class="page_number" type="text" name="std_mod_pages_current" value="'.($this->current_area->current_page+1).'" />				 
				 <span class="page_number_n">/ '.$this->pages_count.'</span>
				 <a href="'.$this->generate_url_page($this->current_area->current_page+1, $this->current_area->rows_per_page).'" title="'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'next_page')).'">
					<img src="'.LIBRARY_DIR.'/php/standard_module/design/next_page.png" title="'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'next_page')).'" />
				 </a>
			 </div>
			</form>
			
			
			';


    return $answer;

    $pages = "";
    if ($rs_count) {
      $count = mysql_num_rows($rs_count);
      if ($count/$this->current_area->rows_per_page > 1) {
        for($i=0; $i<$count/$this->current_area->rows_per_page; $i++) {
          if ($this->current_area->current_page == $i)
            $pages .= ' <a class="navigation" href="'.$this->generate_url_page($i, $this->current_area->rows_per_page).'"><b><u>'.($i+1).'</u></b></a> ';
          else
            $pages .= ' <a class="navigation" href="'.$this->generate_url_page($i, $this->current_area->rows_per_page).'">'.($i+1).'</a> ';
        }
      }
    }
    if($pages)
      $pages = "<p>".$pages."</p>";
    return $pages;
  }

  function print_table($sql) {


    global $parametersMod;
    global $cms;



    $answer = '';
    $pages = $this->print_pages();

    $answer .= '
		<div id="sheet1div">
			<div class="tabs">
				'.$this->print_tabs().'

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

      if($this->current_area->permission != "read_only")
        $answer .= '<th>&nbsp;</th>';
      if($this->current_area->area !=  null && $this->current_area->area->visible)
        $answer .= '<th>&nbsp;</th>';

      if ($this->current_area->sortable && $this->current_area->get_sort_field() != null) {
        $tmp_sort_dir = "asc";
        $class="button";
        if($this->current_area->sort_field != '' && $this->current_area->sort_field == $this->current_area->order_by) {
          if($this->current_area->order_by_dir == "asc") {
            $tmp_sort_dir = "desc";
            $class="move_up";
          }else {
            $tmp_sort_dir = "asc";
            $class="down";
          }

        }

        if($this->current_area->sort_type == 'numbers')
          $answer .= '<th class="header"><a class="'.$class.'" href="'.$this->generate_url_sort($this->current_area->sort_field, $tmp_sort_dir).'"><b>'.$parametersMod->getValue('developer', 'std_mod','admin_translations','sort_field').'</b></a></th>';
        if($this->current_area->sort_type == 'pointers')
          $answer .= '<th class="header"><b>'.$parametersMod->getValue('developer', 'std_mod','admin_translations','sort_field').'</b></th>';
      }

      foreach($this->current_area->get_elements() as $key => $value) {
        if($value->get_show_on_list()) {
          if ($value->sortable) {
            $class="button";
            if($this->current_area->order_by == $value->get_db_field()) {
              if($this->current_area->order_by_dir == "asc") {
                $tmp_sort_direction = "desc";
                $class="move_up";
              }else {
                $tmp_sort_direction = "asc";
                $class="down";
              }
            }else {
              $tmp_sort_direction = "asc";
            }

            $answer .= '<th class="header"><a class="'.$class.'" href="'.$this->generate_url_sort($value->get_db_field(), $tmp_sort_direction).'"><b>'.htmlspecialchars($value->get_name()).'</b></a></th>';
          }else
            $answer .= '<th class="header"><b>'.htmlspecialchars($value->get_name()).'</b></th>';
        }
      }

      if($this->current_area->permission != "read_only" && $this->current_area->permission != "update_only")
        $answer .= '<th>&nbsp;</th>';

      $answer .= '
      </tr>';
      //end column names

      for($i=0; $i<$limit; $i++) {
        $lock = mysql_fetch_assoc($rs);
        $answer .= '<tr id="table_row_'.$lock[$this->current_area->db_key].'">';

        if($this->current_area->permission != "read_only" )
          $answer .= '<td><a class="edit" href="'.$this->generate_url_edit($lock["".$this->current_area->get_db_key()]) .'&amp;road_edit=1" title="'.$parametersMod->getValue('developer', 'std_mod','admin_translations','edit').'">&nbsp;</a></td>';
        if($this->current_area->area !=  null && $this->current_area->area->visible)
          $answer .= '<td><a class="goIn" href="'.$this->generate_url_edit($lock["".$this->current_area->get_db_key()]) .'" title="'.$parametersMod->getValue('developer', 'std_mod','admin_translations','go_in').'">&nbsp;</a></td>';


        if ($this->current_area->sortable && $this->current_area->get_sort_field() != null) {
          if($this->current_area->sort_type == 'numbers')
            $answer .= '<td><form action=""><input onblur="LibDefault.ajaxMessage(\''.$this->generate_url_level($this->level).'&amp;type=ajax\', \'action=new_row_number&amp;key_id='.$lock[$this->current_area->db_key].'&amp;new_row_number=\' + escape(this.value))" style="width:30px;" name="sort_field_'.$lock[$this->current_area->get_db_key()].'" value="'.$lock[$this->current_area->get_sort_field()].'" /></form></td>';
          if($this->current_area->sort_type == 'pointers')
            $answer .= '<td >
            <a class="move_down"
            title="'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'move_down')).'"
            onclick="
            LibDefault.ajaxMessage(\''.$this->generate_url_level($this->level).'&amp;type=ajax\', \'action=row_number_increase&amp;key_id='.$lock[$this->current_area->db_key].'\')
            "
            >&nbsp;</a>
            <a
            title="'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'move_up')).'"
            onclick="
            LibDefault.ajaxMessage(\''.$this->generate_url_level($this->level).'&amp;type=ajax\', \'action=row_number_decrease&amp;key_id='.$lock[$this->current_area->db_key].'\')
            "
            class="move_up">&nbsp;</a></td>';
        }

        foreach($this->current_area->get_elements() as $key => $value) {
          if($value->get_show_on_list()) {
            $answer .= '<td >'.$value->preview_value($lock[''.$value->get_db_field()], mysql_real_escape_string($this->current_area->get_parent_id()), $this->current_area).'&nbsp;</td>';
          }
        }


        if($this->current_area->permission != "read_only" && $this->current_area->permission != "update_only")
          $answer .= '<td><a class="delete" onclick="confirm_delete(\''.$this->generate_url_level($this->level).'&amp;type=ajax\', \'action=delete&amp;key_id='.$lock[$this->current_area->db_key].'\', \''.$parametersMod->getValue('developer', 'std_mod','admin_translations','are_you_sure_you_wish_to_delete').'\'); return false;" title="'.$parametersMod->getValue('developer', 'std_mod','admin_translations','delete').'">&nbsp;</a></td>';
        $answer .='
          </tr>';
      }
    }else trigger_error("Area not found. ".$sql);

    $answer .= '</table>
		</div>
				&nbsp;
		</div>';
    return $answer;
  }

  function print_data() {

    if (!$this->current_area)
      return;



    $sql_pages = " select * from `".DB_PREF."".$this->current_area->get_db_table()."` where 1 ";
    if (($this->level > 0))
      $sql_pages .= " and `".$this->current_area->get_db_reference()."` = '".mysql_real_escape_string($this->up_area->get_parent_id())."' ";
    if($this->current_area->get_where_condition())
      $sql_pages .= " and ".$this->current_area->get_where_condition();  //extra condition to sql where part
    foreach($this->current_area->get_elements() as $key => $value) {
      if ($value->get_searchable() && isset($_REQUEST['search'][$this->level][$key]) && $_REQUEST['search'][$this->level][$key] != null)
        $sql_pages .= " and ".$value->get_filter_option($_REQUEST['search'][$this->level][$key])." ";
    }

    $this->calculate_pages($sql_pages);

    $sql = $sql_pages;
    if(isset($_GET['sort_field'][$this->level]) && isset($_GET['sort_dir'][$this->level]) && ($_GET['sort_dir'][$this->level] == "desc" || $_GET['sort_dir'][$this->level] == "asc"))
      $sql .= " order by `".mysql_real_escape_string(str_replace('`', '', $_GET['sort_field'][$this->level]))."` ".mysql_real_escape_string($_GET['sort_dir'][$this->level])." ";
    else {
      if ($this->current_area->order_by != "") {
        $sql .= " order by `".mysql_real_escape_string($this->current_area->order_by)."` ";
        if($this->current_area->order_by_dir != "")
          $sql .= " ".$this->current_area->order_by_dir." ";
      }
    }
    if($this->current_area->get_rows_per_page() != '' && $this->current_area->get_rows_per_page() != 0) {
      $sql .= " limit ".$this->current_area->get_rows_per_page()*$this->current_area->current_page.", ".$this->current_area->get_rows_per_page()." ";
    }


    $answer = '';

    $answer .= '
    <script type="text/javascript">
      function confirm_delete(action, parameters, question){
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


    $answer .= $this->print_table($sql);

    return $answer;

  }

  function print_tabs() {
    global $parametersMod;

    $answer = '';



    $answer .= '<ul>';

    if($this->current_area->permission != "read_only" && $this->current_area->permission != "update_only")
      $answer .= '
			<li onclick="document.getElementById(\'std_mod_new_popup_body\').style.height=(LibWindow.getWindowHeight() - 130) + \'px\'; document.getElementById(\'std_mod_new_popup\').style.display = \'block\';"><span>'.$parametersMod->getValue('developer','std_mod','admin_translations','new').'</span></li>';

    if($this->current_area->searchable)
      $answer .= '<li onclick="document.getElementById(\'std_mod_search_popup_body\').style.height=(LibWindow.getWindowHeight() - 130) + \'px\'; document.getElementById(\'std_mod_search_popup\').style.display = \'block\';"><span>'.$parametersMod->getValue('developer','std_mod','admin_translations','search').'</span></li>';


    $answer .= '</ul>';



    if($this->current_area->permission != "read_only" && $this->current_area->permission != "update_only") {
      //form for new element in current area
      $answer .= '
				<div class="popup" id="std_mod_new_popup">
					<div id="std_mod_new_popup_border" class="popup_border">
						<div class="popup_head">
							<img 
								onmouseover="this.src=\''.BASE_URL.LIBRARY_DIR.'php/standard_module/design/popup_close_hover.gif\'"
								onmouseout="this.src=\''.BASE_URL.LIBRARY_DIR.'php/standard_module/design/popup_close.gif\'"								
							src="'.BASE_URL.LIBRARY_DIR.'php/standard_module/design/popup_close.gif" style="cursor: pointer; float: right;" onclick="std_mod_hide_popups()"/>						
							'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod','admin_translations','new')).'
						</div>
						<div id="std_mod_new_popup_body" class="management">'.$this->print_new($this->errors).'</div>
						<div class="moduleControlButtons">
							<a onclick="document.getElementById(\'std_mod_new_f\').submit();" class="button">'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod','admin_translations','save')).'</a>
							<a onclick="std_mod_hide_popups();" class="button">'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod','admin_translations','cancel')).'</a>
							<div class="clear"></div>
						</div>
					</div>
				</div>';
    }
    if($this->current_area->searchable) {
      $answer .= '
				<div class="popup" id="std_mod_search_popup"> 
					<div id="std_mod_search_popup_border" class="popup_border">
						<div class="popup_head">
							<img
								onmouseover="this.src=\''.BASE_URL.LIBRARY_DIR.'php/standard_module/design/popup_close_hover.gif\'"
								onmouseout="this.src=\''.BASE_URL.LIBRARY_DIR.'php/standard_module/design/popup_close.gif\'"								
							src="'.BASE_URL.LIBRARY_DIR.'php/standard_module/design/popup_close.gif" style="cursor: pointer; float: right;" onclick="std_mod_hide_popups()"/>						
							'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod','admin_translations','search')).'
						</div>
						<div id="std_mod_search_popup_body" class="management">'.$this->print_search_fields($this->current_area, $this->level).'</div>
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
			<script type="text/javascript">
				document.getElementById(\'std_mod_new_popup_body\').style.height=(LibWindow.getWindowHeight() - 130) + \'px\';
				document.getElementById(\'std_mod_new_popup\').style.display = \'block\';
			</script>
			';
    }



    $answer .= '
		
		<script type="text/javascript">

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

  function generate_url_root() {
    global $cms;
    return $cms->generateUrl($cms->curModId);

  }

  function generate_url_back() {
    return $this->generate_url_level($this->level - 1);
  }

  function generate_url_edit($id) {
    return $this->generate_url_level($this->level)."&amp;road[]=".$id;
  }

  function generate_url_level($max_level, $ignore = null) {
    global $cms;

    $tmp_url = '';

    for($i=0; $i<= $max_level;$i++) {
      if($i != $max_level) {
        if($tmp_url != '')
          $tmp_url.='&amp;';
        $tmp_url.='road[]='.$_GET['road'][$i];
      }

      if($i == $max_level && $ignore != 'sort') {
        if(isset($_GET['sort_field'][$i]))
          $tmp_url.='&amp;sort_field['.$i.']='.$_GET['sort_field'][$i];

        if(isset($_GET['sort_dir'][$i]))
          $tmp_url.='&amp;sort_dir['.$i.']='.$_GET['sort_dir'][$i];
      }


      if($i == $max_level && $ignore != 'page') {
        if(isset($_GET['page'][$i]))
          $tmp_url.='&amp;page['.$i.']='.((int)$_GET['page'][$i]);
        if(isset($_GET['page_size'][$i]))
          $tmp_url.='&amp;page_size['.$i.']='.((int)$_GET['page_size'][$i]);

      }

      if(isset($_GET['search'][$i]) && is_array($_GET['search'][$i])) {
        foreach($_GET['search'][$i] as $key => $value)
          $tmp_url .= '&amp;search['.$i.']['.$key.']='.urlencode($value);


      }
    }

    if($max_level == $this->level && isset($_GET['road_edit']))
      $tmp_url .= '&amp;road_edit=1';
    return $cms->generateurl($cms->curModId, $tmp_url);
  }

  function generate_url_sort($field, $direction) {
    $url = $this->generate_url_level($this->level, 'sort');
    $url .= '&amp;sort_field['.$this->level.']='.$field.'&amp;sort_dir['.$this->level.']='.$direction;
    return $url;
  }

  /*
	$page - page number
	$size - records count in one page
  */
  function generate_url_page($page, $size) {
    $url = $this->generate_url_level($this->level, 'page');
    $url .= '&amp;page['.$this->level.']='.$page.'&amp;page_size['.$this->level.']='.$size;
    return $url;
  }



}






