<?php

namespace IpUpdate\PhpUnit;

class UpdateTestCase extends \PHPUnit_Framework_TestCase
{
    
    protected function setup()
    {
        $this->cleanDir(TMP_DIR);
    }
    
    private function cleanDir($dirPath, $depth = 0) {
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException('$dirPath must be a directory');
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if ($file !== '.' && $file != '..') {
                if (is_dir($file)) {
                    $this->cleanDir($file, $depth + 1);
                } else {
                    if ($file != TMP_DIR.'readme.txt') {
                        unlink($file);
                    }
                }
            }
        }
        if ($depth != 0) {
            rmdir($dirPath);
        }
    }
    
}