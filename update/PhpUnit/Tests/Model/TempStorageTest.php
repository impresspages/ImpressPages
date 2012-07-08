<?php

class TempStorageTest extends PHPUnit_Framework_TestCase
{
    protected function setup()
    {
        $this->cleanDir(TMP_DIR);
    }

    public function testStorage()
    {
        $tmpStorage = new IpUpdate\Library\Model\TempStorage(TMP_DIR);
        $key1 = 'key1';
        $val1 = 'val1';

        $this->assertEquals($tmpStorage->exist($key1), false);

        $tmpStorage->setValue($key1, 'val1');

        $this->assertEquals($tmpStorage->exist($key1), true);
        $this->assertEquals($tmpStorage->getValue($key1), $val1);

        $tmpStorage->remove($key1);
        $this->assertEquals($tmpStorage->exist($key1), false);
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
                    unlink($file);
                }
            }
        }
        if ($depth != 0) {
            rmdir($dirPath);
        }
    }

}
