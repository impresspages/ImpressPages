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

    public function getReflectionByReflection($reflection)
    {
        $reflectionRecord = ipDb()->selectRow('repository_reflection', '*', array('reflection' => $reflection));
        if (!empty($reflectionRecord)) { //because selectRow may return empty array
            return $reflectionRecord;
        } else {
            return false;
        }
    }

    /**
     * @param string $file relative path from BASE_DIR
     * @param array $options image transform options
     * @param string $desiredName - desired file name. If reflection is missing, service will try to create new one with name as possible similar to desired
     * @param bool $onDemand transformation will be create on the fly when image accessed for the first time
     * @return string file name relative to BASE_DIR
     */
    public function getReflection($file, $options, $desiredName = null, $onDemand = true)
    {
        $fingerprint = md5(json_encode($options));
        $reflection = $this->getReflectionRecord($file, $fingerprint);

        if (!$reflection) {
            $reflection = $this->createReflectionRecord($file, $options, $desiredName);
        }

        if (!$onDemand && !is_file($reflection)) {
            $this->createReflection($file, $reflection, $options);
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

    private function createReflectionRecord($source, $options, $desiredName)
    {
        $absoluteSource = realpath(ipFile('file/repository/' . $source));
        if (!$absoluteSource || !is_file($absoluteSource)) {
            throw new \Ip\Exception\Repository\Transform("File doesn't exist", array('filename' => $absoluteSource));
        }

        if (strpos($absoluteSource, realpath(ipFile('file/repository/'))) !== 0) {
            throw new \Exception("Requested file (" . $source . ") is outside repository dir");
        }

        //if desired name ends with .jpg, .gif, etc., remove extension
        $desiredPathInfo = pathinfo($desiredName);
        if (!empty($desiredPathInfo['filename']) && isset($desiredPathInfo['extension']) && strlen(
                $desiredPathInfo['extension']
            ) <= 4
        ) {
            $desiredName = $desiredPathInfo['filename'];
        }


        //update destination file extension
        $pathInfo = pathinfo($absoluteSource);
        if (isset($pathInfo['extension'])) {
            $ext = $pathInfo['extension'];
        } else {
            $ext = '';
        }

        $ext = ipFilter('ipReflectionExtension', $ext, array('source' => $absoluteSource, 'options' => $options));

        if ($desiredName == '') {
            $pathInfo = pathinfo($absoluteSource);
            $desiredName = $pathInfo['filename'];
        }
        if ($ext != '') {
            $desiredName = $desiredName . '.' . $ext;
        }
        $desiredName = \Ip\Internal\File\Functions::cleanupFileName(
            $desiredName
        ); //remove double dots if file name. For security reasons.

        $relativeDestinationPath = date('Y/m/d/');
        $relativeDestinationPath = ipFilter(
            'ipRepositoryNewReflectionFileName',
            $relativeDestinationPath,
            array('originalFile' => $source, 'options' => $options, 'desiredName' => $desiredName)
        );

        $destinationFileName = $this->getUnocupiedName($desiredName, $relativeDestinationPath);
        $reflection = $relativeDestinationPath . $destinationFileName;

        $this->storeReflectionRecord($source, $reflection, $options);

        return $reflection;
    }

    private function getUnocupiedName($file, $destDir, $suffix = '')
    {
        $newName = basename($file);
        $extPos = strrpos($newName, ".");
        if ($extPos !== false) {
            $newExtension = substr($newName, $extPos, strlen($file));
            $newName = substr($newName, 0, $extPos);
        } else {
            $newExtension = '';
        }

        if ($newName == "") {
            $newName = "file_";
        }
        if (!$this->availableFile($destDir . $newName . $newExtension)) {
            $i = 1;
            while (!$this->availableFile($destDir . $newName . '_' . $i . $suffix . $newExtension)) {
                $i++;
            }
            $newName = $newName . '_' . $i . $suffix;
        }
        $newName .= $newExtension;
        return $newName;
    }

    /**
     * Check if such file doesn't exist and is not reserved for reflection
     * @param $file
     */
    private function availableFile($file)
    {
        if (is_file(ipFile('file/' . $file))) {
            return false;
        };

        $exists = ipDb()->selectRow('repository_reflection', '*', array('reflection' => $file));
        if (!empty($exists)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $source
     * @param string $destination
     * @param array $options
     * @return string
     */
    public function createReflection($source, $destination, $options)
    {
        $absoluteSource = str_replace('\\', '/', realpath(ipFile('file/repository/' . $source)));

        $absoluteDestinationDir = dirname(ipFile('file/' . $destination));
        $destinationFileName = basename($destination);
        if (!is_dir($absoluteDestinationDir)) {
            mkdir($absoluteDestinationDir, 0777, $recursive = true);
        }

        if (!is_file($absoluteSource)) {
            return false;
        }

        $data = array(
            'source' => $absoluteSource,
            'destination' => $absoluteDestinationDir . '/' . $destinationFileName,
            'options' => $options
        );

        ipJob('ipCreateReflection', $data);
        ipEvent('ipReflectionCreated', $data);

        if (is_file($absoluteDestinationDir . '/' . $destinationFileName)) {
            return true;
        } else {
            return false;
        }
    }


    private function storeReflectionRecord($file, $reflection, $options)
    {
        $jsonOptions = json_encode($options);

        $params = array(
            'original' => $file,
            'reflection' => $reflection,
            'options' => $jsonOptions,
            'optionsFingerprint' => md5($jsonOptions),
            'createdAt' => time()
        );

        ipDb()->insert('repository_reflection', $params);

    }

    private function getReflectionRecord($file, $optionsFingerprint)
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
          optionsFingerprint = :optionsFingerprint
        ";

        $params = array(
            'original' => $file,
            'optionsFingerprint' => $optionsFingerprint
        );

        $q = $dbh->prepare($sql);
        $q->execute($params);

        if ($lock = $q->fetch(\PDO::FETCH_ASSOC)) {
            return $lock['reflection'];
        }
        return null;
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
