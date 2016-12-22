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

            return dirname($relativeFile) . '/';
        }

        $baseDir = ipConfig()->get('baseDir');
        $baseDir = str_replace('\\', '/', $baseDir);
        if (strpos($absoluteFile, $baseDir) === 0) {
            $relativeFile = substr($absoluteFile, strlen($baseDir) + 1);

            return dirname($relativeFile) . '/';
        }


        if (ipConfig()->isComposerCore()) {
            //this must be a composer installed plugin
            $rootDir = dirname(ipConfig()->get('baseDir'));
            $coreDir = ipConfig()->get('coreDir');
            $vendorDepth = count(explode('/', substr($coreDir, strlen($rootDir)))) - 1;
            if (strpos($absoluteFile, $rootDir) === 0) {
                $rootRelativeFile = substr($absoluteFile, strlen($rootDir) + 1);
                $parts = explode('/', $rootRelativeFile);
                if (count($parts) >= $vendorDepth) {
                    $composerPluginPaths = ipConfig()->get('composerPluginPaths');
                    $pluginComposerPath = implode('/', array_slice($parts, 0, $vendorDepth));
                    if (!empty($composerPluginPaths[$pluginComposerPath])) {
                        $relativeFile = 'Plugin/' . $composerPluginPaths[$pluginComposerPath] . '/' . substr($rootRelativeFile, strlen($pluginComposerPath) + 1);
                        return dirname($relativeFile) . '/';
                    }
                }
            }
        }

        throw new \Ip\Exception('Cannot find relative path for file ' . esc($absoluteFile));
    }

}
