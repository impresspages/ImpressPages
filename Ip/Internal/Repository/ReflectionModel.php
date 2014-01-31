<?php
/**
 * @package ImpressPages

 *
 */

namespace Ip\Internal\Repository;



class ReflectionModel
{
    protected static $instance;

    protected function __construct()
    {

    }

    protected function __clone()
    {

    }

    /**
     * Get singleton instance
     * @return ReflectionModel
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new ReflectionModel();
        }

        return self::$instance;
    }


    /**
     * @param string $file relative path from BASE_DIR
     * @param strong $desiredName - desired file name. If reflection is missing, service will try to create new one with name as possible similar to desired
     * @param Transform\Base $transform - file transformation class
     * @return string file name relative to BASE_DIR
     */
    public function getReflection($file, $desiredName = null, Transform\Base $transform = null)
    {
        if (!$transform) {
            $transform = new Transform\None();
        }

        $fingerprint = $transform->getFingerprint();
        $reflection = $this->getReflectionRecord($file, $fingerprint);

        if (!$reflection) {
            $reflection = $this->createReflection($file, $desiredName, $transform);
        }

        return $reflection;
    }

    public function removeReflections($file)
    {
        $reflections = $this->getReflections($file);
        $this->removeReflectionRecords($file);
        foreach ($reflections as $reflection) {
            $absoluteFilename = ipFile('file/' . $reflection['reflection']);
            if (file_exists($absoluteFilename)) {
                unlink($absoluteFilename);
            }
        }

    }

    /**
     * @param string $source
     * @param string $desiredName
     * @param Transform\Base $transform
     * @return string
     * @throws \Exception
     */

    private function createReflection($source, $desiredName, Transform\Base $transform)
    {
        $absoluteSource = realpath(ipFile('file/repository/' . $source));
        if (!$absoluteSource || !is_file($absoluteSource)) {
            throw new TransformException("File doesn't exist", TransformException::MISSING_FILE);
        }



        /* todox: breaks on Windows
        if (strpos($absoluteSource, ipFile('file/repository/')) !== 0) {
            throw new \Exception("Requested file (".$source.") is outside repository dir");
        }
*/


        //if desired name ends with .jpg, .gif, etc., remove extension
        $desiredPathInfo = pathinfo($desiredName);
        if (!empty($desiredPathInfo['filename']) && isset($desiredPathInfo['extension']) && strlen($desiredPathInfo['extension']) <= 4) {
            $desiredName = $desiredPathInfo['filename'];
        }


        //update destination file extension
        $pathInfo = pathinfo($absoluteSource);
        if (isset($pathInfo['extension'])) {
            $ext = $transform->getNewExtension($absoluteSource, $pathInfo['extension']);
        } else {
            $ext = '';
        }
        if ($desiredName == '') {
            $desiredName = $pathInfo['filename'];
        }
        if ($ext != '') {
            $desiredName = $desiredName.'.'.$ext;
        }



        $relativeDestinationPath = date('Y/m/d/') . $desiredName;
        $relativeDestinationPath = ipFilter('ipRepositoryNewReflectionFileName', $relativeDestinationPath, array('originalFile' => $source, 'transform' => $transform, 'desiredName' => $desiredName));

        $absoluteDestinationDir = dirname(ipFile('file/' . $relativeDestinationPath));
        if (!is_dir($absoluteDestinationDir)) {
            mkdir($absoluteDestinationDir, 0777, $recursive = true);
        }
        $destinationFileName = basename($relativeDestinationPath);
        $relativeDestinationDir = substr($relativeDestinationPath, 0, -strlen($destinationFileName));
        $destinationFileName = \Ip\Internal\File\Functions::genUnoccupiedName($destinationFileName, $absoluteDestinationDir . '/');
        $transform->transform($absoluteSource, $absoluteDestinationDir . '/' . $destinationFileName);
        $transformFingerprint = $transform->getFingerprint();
        $this->storeReflectionRecord($source, $relativeDestinationDir . $destinationFileName, $transformFingerprint);

        return $relativeDestinationDir . $destinationFileName;
    }


    private function storeReflectionRecord($file, $reflection, $transformFingerprint)
    {
        $dbh = ipDb()->getConnection();
        $sql = "
        INSERT INTO
          " . ipTable('repository_reflection') . "
        SET
          original = :original,
          reflection = :reflection,
          transformFingerprint = :transformFingerprint,
          created = :created
        ";

        $params = array(
            'original' => $file,
            'reflection' => $reflection,
            'transformFingerprint' => $transformFingerprint,
            'created' => time()
        );

        $q = $dbh->prepare($sql);
        $q->execute($params);

    }

    private function getReflectionRecord($file, $transformFingerprint)
    {
        $dbh = ipDb()->getConnection();
        $sql = "
        SELECT
          reflection
        FROM
          " . ipTable('repository_reflection') . "
        WHERE
          original = :original
          AND
          transformFingerprint = :transformFingerprint
        ";

        $params = array(
            'original' => $file,
            'transformFingerprint' => $transformFingerprint
        );

        $q = $dbh->prepare($sql);
        $q->execute($params);

        if ($lock = $q->fetch(\PDO::FETCH_ASSOC)) {
            return $lock['reflection'];
        }
    }


    private function getReflections($file)
    {
        $dbh = ipDb()->getConnection();
        $sql = "
        SELECT
          reflection
        FROM
          " . ipTable('repository_reflection') . "
        WHERE
          original = :original
        ";

        $params = array(
            'original' => $file
        );

        $q = $dbh->prepare($sql);
        $q->execute($params);

        $answer = $q->fetchAll(\PDO::FETCH_ASSOC);
        return $answer;
    }

    private function removeReflectionRecords($file)
    {
        $dbh = ipDb()->getConnection();
        $sql = "
        DELETE FROM
          " . ipTable('repository_reflection') . "
        WHERE
          original = :original
        ";

        $params = array(
            'original' => $file
        );

        $q = $dbh->prepare($sql);
        $q->execute($params);
    }

}
