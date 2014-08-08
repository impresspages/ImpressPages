<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\File;


class Functions
{

    /**
     * Check if file is in one of publicly accessible directories
     *
     * @param string $fileName Absolute file name.
     * @return bool
     */
    public static function isFileInPublicDir($fileName)
    {
        $fileName = realpath(ipFile($fileName));
        // Realpath changes slash on windows machines. So we should use the same function on public dir to get equal strings.
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
     * Checks whether file exists in specified directory
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
     * @param string $file Requested file name.
     * @param string $destDir Directory where new file will be placed.
     * @param string $suffix
     * @return string
     */
    public static function genUnoccupiedName($file, $destDir, $suffix = '', $cleanup = true)
    {
        if (substr($destDir, -1) != '/' && substr($destDir, -1) != '\\') {
            $destDir .= '/';
        }

        if ($cleanup) {
            $file = self::cleanupFileName($file);
        }

        $newName = basename($file);
        $extPos = strrpos($newName, '.');

        if ($extPos !== false) {
            $newExtension = substr($newName, $extPos, strlen($file));
            $newName = substr($newName, 0, $extPos);
        } else {
            $newExtension = '';
        }



        if ($newName == '') {
            $newName = 'file_';
        }
        if (file_exists($destDir . $newName . $newExtension)) {
            $i = 1;
            while (file_exists($destDir . $newName . '_' . $i . $suffix . $newExtension)) {
                $i++;
            }
            $newName = $newName . '_' . $i . $suffix;
        }

        $newName .= $newExtension;

        return $newName;
    }

    /**
     * @param $fileName
     * @internal param string $file File name.
     * @return string New (or the same) file without special characters.
     */
    public static function cleanupFileName($fileName)
    {
        $fileName = \Ip\Internal\Text\Transliteration::transform($fileName);
        $fileName = utf8_decode($fileName);
        $spec = array("'", "%", "?", "-", "+", " ", "<", ">", "(", ")", "/", "\\", "&", ",", "!", ":", "\"", "?", "|");

        $fileName = str_replace($spec, '_', $fileName);
        $fileName = preg_replace(
            '/[^\w\._]+/',
            '_',
            $fileName
        ); // It overlaps with above replace file. But for historical reasons let it be.
        $fileName = preg_replace('/_+/', '_', $fileName);


        //leave only the last dot in filenames. Files with double extensions might be executed on most of the servers. Eg. hack.php.jpgx
        $pathParts = pathinfo($fileName);
        $fileName = str_replace('.', '_', $pathParts['filename']);
        if (!empty($pathParts['extension'])) {
            $fileName .= '.' . $pathParts['extension'];
        }


        if ($fileName == '') {
            $fileName = 'file';
        }

        return $fileName;
    }

    /**
     * PHP5 have no 100% reliable way to get mime type.
     * This function tries few ways to get mime type.
     *
     * @param string $file_path
     * @return string Mime type or null string on failure.
     */
    public static function getMimeType($file_path)
    {
        $mtype = null;
        if (function_exists('finfo_file')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mtype = finfo_file($finfo, $file_path);
            finfo_close($finfo);
        } elseif (function_exists('mime_content_type')) {
            $mtype = mime_content_type($file_path);
        } else {
            // Any other ideas?
        }

        return $mtype;
    }

    /**
     * @param string $relativePath
     * @param string $destinationDir
     * @return string
     */
    public static function copyTemporaryFile($relativePath, $destinationDir)
    {
        $newBasename = \Ip\Internal\File\Functions::genUnoccupiedName($relativePath, $destinationDir);

        if (!copy(ipFile('file/tmp/' . $relativePath), $destinationDir . $newBasename)) {
            trigger_error(
                "Can't copy file from " . htmlspecialchars(ipThemeFile('') . $relativePath) . ' to ' . htmlspecialchars(
                    $destinationDir . $newBasename
                )
            );
        }

        return $newBasename;
    }

}
