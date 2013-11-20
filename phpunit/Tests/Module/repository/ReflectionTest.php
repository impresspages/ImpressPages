<?php
/**
 * @package   ImpressPages
 *
 *
 */

class ReflectionTest extends \PhpUnit\GeneralTestCase
{

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->createXMLDataSet(TEST_FIXTURE_DIR.'Repository/reflection.xml');
    }

    public function testCreateRemoveReflection()
    {
        $repository = \Ip\Module\Repository\Model::instance();

        $file = ipGetConfig()->getRaw('FILE_REPOSITORY_DIR') . 'impresspages.png';

        copy(TEST_FIXTURE_DIR.'Repository/impresspages.png', ipGetConfig()->repositoryFile('impresspages.png'));

        //Bind file to module (twice)
        $repository->bindFile($file, 'modulexxx', 1);
        $repository->bindFile($file, 'modulexxx', 1);


        $reflectionService = \Ip\Module\Repository\ReflectionService::instance();

        //Create reflection
        $transformSmall = new \Ip\Module\Repository\Transform\ImageCrop(11, 12, 23, 24, 15, 16);//nearly random coordinates
        $reflection = $reflectionService->getReflection($file, null, $transformSmall);
        $this->assertEquals(ipGetConfig()->getRaw('FILE_DIR') . 'impresspages.png', $reflection);
//echo BASE_DIR.$reflection;
        $this->assertEquals(true, file_exists(ipGetConfig()->baseFile($reflection)));


        //Unbind file from repository (once)
        $repository->unbindFile($file, 'modulexxx', 1);

        //check if reflection still exists
        $this->assertEquals(true, file_exists(ipGetConfig()->baseFile($reflection)));

        //unbind next file instance
        $repository->unbindFile($file, 'modulexxx', 1);

        //Check if reflection has been removed
        $this->assertEquals(false, file_exists(ipGetConfig()->baseFile($reflection)));


    }




}
