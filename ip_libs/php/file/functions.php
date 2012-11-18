<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Library\Php\File;


class Functions{

    /**
     * Check if file is in one of publicly accessible directories.
     */
    public static function isFileInPublicDir($fileName)
    {
        $fileName = realpath($fileName);
        $publicDirs = array(
            FILE_DIR,
            TMP_FILE_DIR,
            FILE_REPOSITORY_DIR,
            IMAGE_DIR,
            TMP_IMAGE_DIR,
            IMAGE_REPOSITORY_DIR,
            AUDIO_DIR,
            TMP_AUDIO_DIR,
            AUDIO_REPOSITORY_DIR,
            VIDEO_DIR,
            TMP_VIDEO_DIR,
            VIDEO_REPOSITORY_DIR
        );

        foreach ($publicDirs as $publicDir) {
            //realpath changes slash on windows machines. So we should use the same function on public dir to get equal strings
            $tmpPath = realpath($publicDir);
            if (strpos($fileName, $tmpPath) === 0) {
                return true;
            }
        }
        return false;

    }

    /**
     * @param string $file required file name
     * @param string $dest_dir directory where new file will be placed
     * @return string new (or the same) file name that don't colide with existing files in specified directory
     */
    public static function genUnoccupiedName($file, $dest_dir, $suffix = ''){
        require_once (LIBRARY_DIR.'php/text/transliteration.php');
        $new_name = basename($file);
        $ext_pos = strrpos($new_name, ".");
        $new_extension = substr($new_name, $ext_pos, strlen($file));
        $new_name = substr($new_name, 0, $ext_pos);

        $new_name = \Library\Php\Text\Transliteration::transform($new_name);
        $new_name = utf8_decode($new_name);
        $spec = array("'", "%", "?", "-", "+", " ", "<", ">", "(", ")", "/", "\\", "&", ".", ",", "!", ":", "\"", "?", "|");
        $new_name = str_replace($spec, "_", $new_name);

        if($new_name == "") {
            $new_name = "file_";
        }
        if (file_exists($dest_dir.$new_name.$new_extension)){
            $i = 1;
            while(file_exists($dest_dir.$new_name.'_'.$i.$suffix.$new_extension)){
                $i++;
            }
            $new_name = $new_name.'_'.$i.$suffix;
        }
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

