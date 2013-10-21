<?php
/**
 * Implements language negotiation
 * @package		Library
 *
 *
 */
namespace Library\Php\BrowserDetection;


class Language {

    public static function getLanguages(){
        $answer = Array() ;
        if(isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])){
            $lang_list = explode(",",$_SERVER["HTTP_ACCEPT_LANGUAGE"]) ;
            for ($i=0;$i<count($lang_list);$i++) {
                if (strpos($lang_list[$i],";") === false){
                    $answer[] = $lang_list[$i];
                }else{
                    $tmp_array = explode(";",$lang_list[$i]) ;
                    $answer[] = $tmp_array[0] ;
                }
            }
        }
        return $answer;
    }
}
