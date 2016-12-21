<?php


namespace Ip\Internal\Install;


class TestService
{
    public static function setupTestDatabase($database, $tablePrefix)
    {
        Model::setInstallationDir(ipFile(''));
        Model::createDatabaseStructure($database, $tablePrefix);
        Model::importData($tablePrefix);
        OptionHelper::import(__DIR__ . '/options.json');
        Model::insertAdmin('test', 'test@example.com', 'test');
        ipSetOptionLang('Config.websiteTitle', 'TestSite', 'en');
        ipSetOptionLang('Config.websiteEmail', 'test@example.com', 'en');

    }
}
