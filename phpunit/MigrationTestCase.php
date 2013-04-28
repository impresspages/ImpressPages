<?php

namespace PhpUnit;

/**
 * Test case to test Update script migration files
 * Class MigrationTestCase
 * @package PhpUnit
 */
class MigrationTestCase extends GeneralTestCase
{


    /**
     * Get configuration values that mimic real installation required for migration script
     * @return array
     */
    protected function getInstallationConfig()
    {
        return array(
            'DB_PREF' => 'ip_',
            'DB_SERVER' => TEST_DB_HOST,
            'DB_USERNAME' => TEST_DB_USER,
            'DB_PASSWORD' => TEST_DB_PASS,
            'DB_DATABASE' => TEST_DB_NAME,
            'MYSQL_CHARSET' => 'utf8',
            'BASE_DIR' => TEST_CODEBASE_DIR,
            'FILE_DIR' => 'phpunit/Tmp/file/',
            'IMAGE_DIR' => 'phpunit/Tmp/image/',
            'AUDIO_DIR' => 'phpunit/Tmp/audio/',
            'VIDEO_DIR' => 'phpunit/Tmp/video/',

            'SECURE_DIR' => 'phpunit/Tmp/securefile/',
            'TMP_SECURE_DIR' => 'phpunit/Tmp/securefile/tmp',
            'MANUAL_DIR' => 'phpunit/Tmp/manual'
            //Add others when needed
        );
    }

    protected function setup()
    {
        parent::setup();
        $configMock = $this->getInstallationConfig();
        mkdir(TEST_CODEBASE_DIR.$configMock['FILE_DIR']);
        mkdir(TEST_CODEBASE_DIR.$configMock['AUDIO_DIR']);
        mkdir(TEST_CODEBASE_DIR.$configMock['IMAGE_DIR']);
        mkdir(TEST_CODEBASE_DIR.$configMock['VIDEO_DIR']);
        mkdir(TEST_CODEBASE_DIR.$configMock['SECURE_DIR']);
        mkdir(TEST_CODEBASE_DIR.$configMock['TMP_SECURE_DIR']);
        mkdir(TEST_CODEBASE_DIR.$configMock['MANUAL_DIR']);
    }

}