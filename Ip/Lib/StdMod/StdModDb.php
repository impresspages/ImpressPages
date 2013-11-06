<?php
/**
 * @package		Library
 *
 *
 */

namespace Ip\Lib\StdMod;



class StdModDb{


    public static function languages(){
        $answer = array();
        $sql = "select id, d_short, d_long from `".DB_PREF."language` where 1 order by  row_number  ";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            while($lock = ip_deprecated_mysql_fetch_assoc($rs)){
                $answer[] = $lock;
            }
        }else trigger_error($sql." ".ip_deprecated_mysql_error());
        return $answer;
    }

    public static function cmsLanguages(){
        $answer = array();
        $sql = "select id, d_short, d_long, def from `".DB_PREF."cms_language` where `visible` order by def, row_number  ";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            while($lock = ip_deprecated_mysql_fetch_assoc($rs)){
                $answer[] = $lock;
            }
        }else trigger_error($sql." ".ip_deprecated_mysql_error());
        return $answer;
    }


    public static function updatedRowsCount() {
        $infoStr = mysql_info();
        preg_match('/Rows matched: ([0-9]*)/', $infoStr, $matches );
        return $matches[1];
    }


}
