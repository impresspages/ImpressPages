<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal\Email;
//TODOXX REFACTOR #132
require_once(__DIR__ . '/db.php');

class element_attachment extends \Library\Php\StandardModule\Element{ //data element in area
    var $default_value;
    var $mem_value;
    var $reg_expression;
    var $max_length;

    function __construct(){
        $this->visible = false;
    }

    function print_field_new($prefix, $parentId = null, $area = null){

        return '';
    }


    function print_field_update($prefix, $parentId = null, $area = null){
        return '';
    }

    function get_parameters($action, $prefix){
        return;
    }

    function print_search_field($level, $key){
        if (isset($_GET['search'][$level][$key]))
        $value = $_GET['search'][$level][$key];
        else
        $value = '';
        return '<input name="search['.$level.']['.$key.']" value="'.htmlspecialchars($value).'" />';
    }

    function get_filter_option($value){
        return " email like '%".ip_deprecated_mysql_real_escape_string($value)."%' ";
    }

    function preview_value($value){
        global $cms;

        $answer = '';
        $email = Db::getEmail($value);
        $files = explode("\n", $email['files']);
        $file_names = explode("\n", $email['file_names']);
        $file_mime_types = explode("\n", $email['file_mime_types']);
        if (sizeof($files) > 0) {
            for($i =0; $i<sizeof($files) && $i<sizeof($file_names)&& $i<sizeof($file_mime_types); $i++){
                if ($answer != '') {
                    $answer .= '<br />';
                }
                 
                $answer .= '<a target="_blank" href="'.$cms->generateWorkerUrl($cms->curModId, 'action=get_file&file_number='.$i.'&record_id='.$value).'">'.htmlspecialchars($file_names[$i]).'</a>';

            }
        }
        return '<span>'.$answer.'</span>';
    }

    function check_field($prefix, $action){
        return null;
    }



}

