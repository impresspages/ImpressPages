<?php


namespace Plugin\Install;


class TestService
{
    public static function setupTestDatabase($database, $tablePrefix)
    {
        Model::createDatabaseStructure($database, $tablePrefix);
        Model::importData($tablePrefix);
        Model::insertAdmin('test', 'test@example.com', 'test');
    }
} 
