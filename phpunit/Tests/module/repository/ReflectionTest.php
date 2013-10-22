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
        $repository = \Modules\administrator\repository\Model::instance();

        $file = FILE_REPOSITORY_DIR.'impresspages.png';
        copy(TEST_FIXTURE_DIR.'Repository/impresspages.png', BASE_DIR.FILE_REPOSITORY_DIR.'impresspages.png');

        //Bind file to module (twice)
        $repository->bindFile($file, 'modulexxx', 1);
        $repository->bindFile($file, 'modulexxx', 1);


        $reflectionService = \Modules\administrator\repository\ReflectionService::instance();

        //Create reflection
        $transformSmall = new \Modules\administrator\repository\Transform\ImageCrop(11, 12, 23, 24, 15, 16);//nearly random coordinates
        $reflection = $reflectionService->getReflection($file, null, $transformSmall);
        $this->assertEquals('phpunit/' . TEST_TMP_DIR . 'file/impresspages.png', $reflection);
//echo BASE_DIR.$reflection;
        $this->assertEquals(true, file_exists(BASE_DIR.$reflection));


        //Unbind file from repository (once)
        $repository->unbindFile($file, 'modulexxx', 1);

        //check if reflection still exists
        $this->assertEquals(true, file_exists(BASE_DIR.$reflection));

        //unbind next file instance
        $repository->unbindFile($file, 'modulexxx', 1);

        //Check if reflection has been removed
        $this->assertEquals(false, file_exists(BASE_DIR.$reflection));


    }




}
