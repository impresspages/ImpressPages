<?php
/**
 * @package		Library
 *
 *
 */

namespace Ip\Internal\File;


class Functions{

    /**
     * Check if file is in one of publicly accessible directories.
     *
     * @param string $fileName absolute file name
     */
    public static function isFileInPublicDir($fileName)
    {
        $fileName = realpath(ipFile($fileName));
        //realpath changes slash on windows machines. So we should use the same function on public dir to get equal strings
        $publicDirs = array(
            realpath(ipFile('file/')),
            realpath(ipFile('file/tmp/')),
            realpath(ipFile('file/repository')),
        );
        foreach ($publicDirs as $publicDir) {
            if (strpos($fileName, $publicDir) === 0) {
                return true;
            }
        }
        return false;

    }

    /**
     * Checks whether file exists in specified directory.
     *
     * @param string $filename (example.php)
     * @param string $dir (/var/www/example.com/ or /var/www/example.com)
     * @return bool
     */
    public static function isFileInDir($filename, $dir)
    {
        $realDir = realpath($dir);
        $realPath = realpath($realDir . DIRECTORY_SEPARATOR . $filename);

        if (!is_file($realPath)) {
            return false;
        }

        return strpos($realPath, $realDir) === 0;
    }

    /**
     * @param string $file required file name
     * @param string $dest_dir directory where new file will be placed
     * @return string new (or the same) file name that don't collide with existing files in specified directory
     */
    public static function genUnoccupiedName($file, $dest_dir, $suffix = ''){
        $new_name = basename($file);
        $ext_pos = strrpos($new_name, ".");
        if ($ext_pos !== false){
            $new_extension = substr($new_name, $ext_pos, strlen($file));
            $new_name = substr($new_name, 0, $ext_pos);
        } else {
            $new_extension = '';
        }

        $new_name = self::cleanupFileName($new_name);

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
     * @param string $file file name
     * @return string new (or the same) file without special characters
     */
    public static function cleanupFileName($fileName){
        $fileName = \Ip\Internal\Text\Transliteration::transform($fileName);
        $fileName = utf8_decode($fileName);
        $spec = array("'", "%", "?", "-", "+", " ", "<", ">", "(", ")", "/", "\\", "&", ",", "!", ":", "\"", "?", "|");
        $fileName = str_replace($spec, "_", $fileName);
        $fileName = preg_replace('/[^\w\._]+/', '_', $fileName); //it overlaps with above replace file. But for historical reasons let it be
        $fileName = preg_replace('/_+/', '_', $fileName);
        if ($fileName == '') {
            $fileName = 'file';
        }
        return $fileName;
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

    public static function copyTemporaryFile($relativePath, $destinationDir)
    {
        $newBasename = \Ip\Internal\File\Functions::genUnoccupiedName($relativePath, $destinationDir);

        if (!copy(ipFile('file/tmp/' . $relativePath), $destinationDir . $newBasename)) {
            trigger_error("Can't copy file from " . htmlspecialchars(ipConfig()->themeFile('') . $relativePath) . " to " . htmlspecialchars($destinationDir . $newBasename));
        }

        return $newBasename;
    }

}

