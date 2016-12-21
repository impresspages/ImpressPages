<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal;


class PathHelper
{
    /**
     * @ignore
     * @param int $callLevel
     * @return string
     * @throws \Ip\Exception
     */
    public static function ipRelativeDir($callLevel = 0)
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $callLevel + 1);
        if (!isset($backtrace[$callLevel]['file'])) {
            throw new \Ip\Exception("Can't find caller");
        }

        $absoluteFile = $backtrace[$callLevel]['file'];

        if (DIRECTORY_SEPARATOR == '\\') {
            // Replace windows paths
            $absoluteFile = str_replace('\\', '/', $absoluteFile);
        }

        $coreDir = ipConfig()->get('coreDir');
        $coreDir = str_replace('\\', '/', $coreDir);
        if (strpos($absoluteFile, $coreDir) === 0) {
            $relativeFile = substr($absoluteFile, strlen($coreDir) + 1);

            return substr($relativeFile, 0, strrpos($relativeFile, '/') + 1);
        }

        $baseDir = ipConfig()->get('baseDir');
        $baseDir = str_replace('\\', '/', $baseDir);
        if (strpos($absoluteFile, $baseDir) === 0) {
            $relativeFile = substr($absoluteFile, strlen($baseDir) + 1);

            return substr($relativeFile, 0, strrpos($relativeFile, '/') + 1);
        }

        throw new \Ip\Exception('Cannot find relative path for file ' . esc($absoluteFile));
    }

}
