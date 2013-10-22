<?php
/**
 * @package   ImpressPages
 */

namespace PhpUnit\Helper;


class Cleanup
{
    public static function cleanupFiles()
    {
        $fs = new \PhpUnit\Helper\FileSystem();
        $fs->chmod(TEST_TMP_DIR, 0755);
        $fs->cleanDir(TEST_TMP_DIR);
        $fs->chmod(TEST_TMP_DIR . '.gitignore', 0664);
        $fs->chmod(TEST_TMP_DIR . 'readme.txt', 0664);
    }
}