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
        $repository = \Ip\Internal\Repository\Model::instance();

        $repositoryFile = 'impresspages.png';
        $file = 'file/repository/' . $repositoryFile;

        copy(TEST_FIXTURE_DIR.'Repository/impresspages.png', ipFile($file));

        //Bind file to module (twice)
        $repository->bindFile($repositoryFile, 'modulexxx', 1);
        $repository->bindFile($repositoryFile, 'modulexxx', 1);

        $reflectionService = \Ip\Internal\Repository\ReflectionService::instance();

        //Create reflection
        $transformSmall = new \Ip\Internal\Repository\Transform\ImageCrop(11, 12, 23, 24, 15, 16);//nearly random coordinates
        $reflection = $reflectionService->getReflection($repositoryFile, null, $transformSmall);
        $this->assertEquals('file/impresspages.png', $reflection);
//echo BASE_DIR.$reflection;
        $this->assertEquals(true, file_exists(ipFile($reflection)));


        //Unbind file from repository (once)
        $repository->unbindFile($file, 'modulexxx', 1);

        //check if reflection still exists
        $this->assertEquals(true, file_exists(ipFile($reflection)));

        //unbind next file instance
        $repository->unbindFile($file, 'modulexxx', 1);

        //Check if reflection has been removed
        $this->assertEquals(false, file_exists(ipFile($reflection)));


    }




}
