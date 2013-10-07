<?php
/**
 * @package		Library
 *
 *
 */

namespace Library\Php\StandardModule;



class std_mod_db{

    function __construct(){
         
    }

    function languages(){
        $answer = array();
        $sql = "select id, d_short, d_long from `".DB_PREF."language` where 1 order by  row_number  ";
        $rs = mysql_query($sql);
        if($rs){
            while($lock = mysql_fetch_assoc($rs)){
                $answer[] = $lock;
            }
        }else trigger_error($sql." ".mysql_error());
        return $answer;
    }

    function cms_languages(){
        $answer = array();
        $sql = "select id, d_short, d_long, def from `".DB_PREF."cms_language` where visible order by def, row_number  ";
        $rs = mysql_query($sql);
        if($rs){
            while($lock = mysql_fetch_assoc($rs)){
                $answer[] = $lock;
            }
        }else trigger_error($sql." ".mysql_error());
        return $answer;
    }


}
