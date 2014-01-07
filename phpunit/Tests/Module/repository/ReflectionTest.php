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
        $reflectionFile = 'file/' . $reflectionService->getReflection($repositoryFile, null, $transformSmall);
        if ($reflectionFile == 'file/') {
            $e = $reflectionService->getLastException();
            $this->fail($e->getMessage() . ' at ' . basename($e->getFile()) . ':' . $e->getLine());
        }

        $reflectionAbsolutePath = ipFile($reflectionFile);
        $this->assertEquals('file/' . date('Y/m/d/') . $repositoryFile, $reflectionFile);
        $this->assertTrue(file_exists($reflectionAbsolutePath));


        //Unbind file from repository (once)
        $repository->unbindFile($repositoryFile, 'modulexxx', 1);

        //check if reflection still exists
        $this->assertTrue(file_exists($reflectionAbsolutePath), 'Reflection should still exist.');

        //unbind next file instance
        $repository->unbindFile($repositoryFile, 'modulexxx', 1);

        //Check if reflection has been removed
        $this->assertFalse(file_exists($reflectionAbsolutePath), 'Reflection has not been removed.');


    }




}
