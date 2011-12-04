<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Library\Php\File;


class Functions{
    /**
     * @param string $file required file name
     * @param string $dest_dir directory where new file will be placed
     * @return string new (or the same) file name that don't colide with existing files in specified directory
     */
    public static function genUnocupiedName($file, $dest_dir){
        require_once (LIBRARY_DIR.'php/text/transliteration.php');
        $new_name = basename($file);
        $ext_pos = strrpos($new_name, ".");
        $new_extension = substr($new_name, $ext_pos, strlen($file));
        $new_name = substr($new_name, 0, $ext_pos);
        global $log;
        $log->log('test', 'filename0', $file.' '.basename($file).' '.$new_name.' '.$ext_pos.' '.$new_extension);

        $new_name = \Library\Php\Text\Transliteration::transform($new_name);
        $new_name = utf8_decode($new_name);
        $spec = array("'", "%", "?", "-", "+", " ", "<", ">", "(", ")", "/", "\\", "&", ".", ",", "!", ":", "\"", "?", "|");
        $new_name = str_replace($spec, "_", $new_name);

        if($new_name == "")
        $new_name = "file_";
        if (file_exists($dest_dir.$new_name.$new_extension)){
            $i = 1;
            while(file_exists($dest_dir.$new_name.'_'.$i.$new_extension)){
                $i++;
            }
            $new_name = $new_name.'_'.$i;
        }
        global $log;
        $log->log('test', 'filename1', $new_name.' '.$new_extension);
        $new_name .= $new_extension;
        return $new_name;
    }


    /**
     * PHP5 have no 100% reliable way to get mime type.
     * This function tries few ways to get mime type.
     *
     * @return string mime type or null string on failure.
     */
    public static function getMimeType($file_path)
    {
        $mtype = null;
        if (function_exists('finfo_file')){
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mtype = finfo_file($finfo, $file_path);
            finfo_close($finfo);
        } elseif (function_exists('mime_content_type')){
            $mtype = mime_content_type($file_path);
        } elseif (class_exists('finfo')){
            $fi = new finfo(FILEINFO_MIME);
            $mtype = $fi->buffer(file_get_contents($files[$i]));
        } else {
            //any other ideas?
        }
        return $mtype;
    }

}

