<?php
/**
 * @package   ImpressPages
 */

namespace IpUpdate\Library\Migration\To3_3;


use IpUpdate\Library\UpdateException;

class Script extends \IpUpdate\Library\Migration\General{

    private $conn;
    private $dbh;
    private $dbPref;
    private $cf;

    public function process($cf)
    {
        $this->cf = $cf;
        $db = new \IpUpdate\Library\Model\Db();
        $conn = $db->connect($cf, \IpUpdate\Library\Model\Db::DRIVER_MYSQL);
        $this->conn = $conn;
        $dbh = $db->connect($cf);
        $this->dbh = $dbh;

        $this->dbPref = $cf['DB_PREF'];

        $this->remigrateWidgets($this->cf);


    }


    public function remigrateWidgets($cf)
    {
        $this->cf = $cf; //duplicates constructor as this method could be called directly from tests
        $this->dbPref = $cf['DB_PREF'];
        $db = new \IpUpdate\Library\Model\Db();
        $conn = $db->connect($cf);

        $this->conn = $db->connect($cf, \IpUpdate\Library\Model\Db::DRIVER_MYSQL);

        $widgetsToMigrate = array(
            'IpImage',
            'IpImageGallery',
            'IpLogoGallery',
            'IpTextImage',
            'IpFile'
        );

        foreach($widgetsToMigrate as $widgetName) {
            $sql = "
            SELECT
                `widgetId`, `data`
            FROM
                `".$this->dbPref."m_content_management_widget`
            where
                name = :name
            ";
            $params = array(
                'name' => $widgetName
            );
            $q = $conn->prepare($sql);
            try {
                $q->execute($params);
            } catch (\PDOException $e) {
                echo 'exception '.$e->getMessage();
            }
            $widgetsData = $q->fetchAll();
            $migrateFunction = 'migrate'.$widgetName;
            foreach($widgetsData as $widgetData) {

                $newData = $this->$migrateFunction($widgetData['widgetId'], json_decode($widgetData['data'], true));
                $sql = "
                    UPDATE
                        `".$this->dbPref."m_content_management_widget`
                    SET
                        `data` = :data
                    WHERE
                        `widgetId` = :widgetId
                ";

                $params = array (
                    'data' => json_encode($this->checkEncoding($newData)),
                    'widgetId' => $widgetData['widgetId']
                );
                $q = $conn->prepare($sql);
                $q->execute($params);
            }



        }
    }

    private function migrateIpImage($widgetId, $data)
    {
        if (!is_writable($this->cf['BASE_DIR'].$this->cf['FILE_REPOSITORY_DIR'])) {
            $errorData = array (
                'file' => $this->cf['BASE_DIR'].$this->cf['FILE_REPOSITORY_DIR']
            );
            throw new \IpUpdate\Library\UpdateException("Can't write to ".$this->cf['BASE_DIR'].$this->cf['FILE_REPOSITORY_DIR'], \IpUpdate\Library\UpdateException::WRITE_PERMISSION, $errorData);
        }

        if (!empty($image['imageOriginal']) && strpos($image['imageOriginal'], $this->cf['FILE_DIR']) === 0) {
            $newFile = $this->moveToRepository($image['imageOriginal']);
            $this->unbindFile($image['imageOriginal'], 'standard/content_management', $widgetId);
            $image['imageOriginal'] = $newFile;
            $this->bindFile($image['imageOriginal'], 'standard/content_management', $widgetId);
        }

        if (isset($data['imageBig']) && $data['imageBig']) {
            $this->unbindFile($data['imageBig'], 'standard/content_management', $widgetId);
            unset($data['imageBig']);
        }
        if (isset($data['imageSmall']) && $data['imageSmall']) {
            $this->unbindFile($data['imageSmall'], 'standard/content_management', $widgetId);
            unset($data['imageSmall']);
        }
        return $data;
    }

    private function migrateIpImageGallery($widgetId, $data)
    {
        if (!is_writable($this->cf['BASE_DIR'].$this->cf['FILE_REPOSITORY_DIR'])) {
            $errorData = array (
                'file' => $this->cf['BASE_DIR'].$this->cf['FILE_REPOSITORY_DIR']
            );

            throw new \IpUpdate\Library\UpdateException("Can't write to ".$this->cf['BASE_DIR'].$this->cf['FILE_REPOSITORY_DIR'], \IpUpdate\Library\UpdateException::WRITE_PERMISSION, $errorData);
        }

        if (isset($data['images']) && is_array($data['images'])) {
            foreach($data['images'] as $imageKey => &$image) {
                if (!is_array($image)) {
                    continue;
                }

                if (!empty($image['imageOriginal']) && strpos($image['imageOriginal'], $this->cf['FILE_DIR']) === 0) {
                    $newFile = $this->moveToRepository($image['imageOriginal']);
                    $this->unbindFile($image['imageOriginal'], 'standard/content_management', $widgetId);
                    $image['imageOriginal'] = $newFile;
                    $this->bindFile($image['imageOriginal'], 'standard/content_management', $widgetId);
                }

                if (isset($image['imageBig']) && $image['imageBig']) {
                    $this->unbindFile($image['imageBig'], 'standard/content_management', $widgetId);
                    unset($image['imageBig']);
                }
                if (isset($image['imageSmall']) && $image['imageSmall']) {
                    $this->unbindFile($image['imageSmall'], 'standard/content_management', $widgetId);
                    unset($image['imageSmall']);
                }


            };
        }

        return $data;
    }

    private function migrateIpLogoGallery($widgetId, $data)
    {
        if (!is_writable($this->cf['BASE_DIR'].$this->cf['FILE_REPOSITORY_DIR'])) {
            $errorData = array (
                'file' => $this->cf['BASE_DIR'].$this->cf['FILE_REPOSITORY_DIR']
            );
            throw new \IpUpdate\Library\UpdateException("Can't write to ".$this->cf['BASE_DIR'].$this->cf['FILE_REPOSITORY_DIR'], \IpUpdate\Library\UpdateException::WRITE_PERMISSION, $errorData);
        }


        if (isset($data['logos']) && is_array($data['logos'])) {
            foreach($data['logos'] as $logoKey => &$logo) {
                if (!is_array($logo)) {
                    continue;
                }

                if (!empty($logo['logoOriginal']) && strpos($logo['logoOriginal'], $this->cf['FILE_DIR']) === 0) {
                    $newFile = $this->moveToRepository($logo['logoOriginal']);
                    $this->unbindFile($logo['logoOriginal'], 'standard/content_management', $widgetId);
                    $logo['logoOriginal'] = $newFile;
                    $this->bindFile($logo['logoOriginal'], 'standard/content_management', $widgetId);
                }

                if (!empty($logo['logoSmall'])) {
                    $this->unbindFile($logo['logoSmall'], 'standard/content_management', $widgetId);
                    unset($logo['logoSmall']);
                }
            };
        }
        return $data;
    }


    private function migrateIpTextImage($widgetId, $data)
    {
        if (!is_writable($this->cf['BASE_DIR'].$this->cf['FILE_REPOSITORY_DIR'])) {
            $errorData = array (
                'file' => $this->cf['BASE_DIR'].$this->cf['FILE_REPOSITORY_DIR']
            );
            throw new \IpUpdate\Library\UpdateException("Can't write to ".$this->cf['BASE_DIR'].$this->cf['FILE_REPOSITORY_DIR'], \IpUpdate\Library\UpdateException::WRITE_PERMISSION, $errorData);
        }

        if (!empty($image['imageOriginal'])) {
            $newFile = $this->moveToRepository($image['imageOriginal']);
            $this->unbindFile($image['imageOriginal'], 'standard/content_management', $widgetId);
            $image['imageOriginal'] = $newFile;
            $this->bindFile($image['imageOriginal'], 'standard/content_management', $widgetId);
        }


        if (isset($data['imageBig']) && $data['imageBig']) {
            $this->unbindFile($data['imageBig'], 'standard/content_management', $widgetId);
            unset($data['imageBig']);
        }
        if (isset($data['imageSmall']) && $data['imageSmall']) {
            $this->unbindFile($data['imageSmall'], 'standard/content_management', $widgetId);
            unset($data['imageSmall']);
        }
        return $data;
    }

    private function migrateIpFile($widgetId, $data)
    {
        if (!is_writable($this->cf['BASE_DIR'].$this->cf['FILE_REPOSITORY_DIR'])) {
            $errorData = array (
                'file' => $this->cf['BASE_DIR'].$this->cf['FILE_REPOSITORY_DIR']
            );
            throw new \IpUpdate\Library\UpdateException("Can't write to ".$this->cf['BASE_DIR'].$this->cf['FILE_REPOSITORY_DIR'], \IpUpdate\Library\UpdateException::WRITE_PERMISSION, $errorData);
        }

        if (!isset($data['files']) || !is_array($data['files'])) {
            return;
        }

        foreach($data['files'] as $fileKey => &$file) {
            if (!empty($file['fileName'])) {
                $newFile = $this->moveToRepository($file['fileName']);
                $this->unbindFile($file['fileName'], 'standard/content_management', $widgetId);
                $file['fileName'] = $newFile;
                $this->bindFile($file['fileName'], 'standard/content_management', $widgetId);
            }
        };


        return $data;
    }


    private function moveToRepository($original)
    {
        if (file_exists($this->cf['BASE_DIR'].$this->cf['FILE_REPOSITORY_DIR'].basename($original))) {
            //already moved
            return $this->cf['FILE_REPOSITORY_DIR'].basename($original);
        }

        if (!file_exists($this->cf['BASE_DIR'].$original)) {
            //original doesn't exist
            return '';
        }

        $newFile = $this->cf['FILE_REPOSITORY_DIR'].basename($original);
        copy($this->cf['BASE_DIR'].$original, $this->cf['BASE_DIR'].$newFile);
        return $newFile;

    }

    private function unbindFile($file, $module, $instanceId) {

        if ($file == '') {
            return;
        }

        $sql = "
        DELETE FROM
            `".$this->dbPref."m_administrator_repository_file`
        WHERE
            `fileName` = '".mysql_real_escape_string($file)."' AND
            `module` = '".mysql_real_escape_string($module)."' AND
            `instanceId` = '".mysql_real_escape_string($instanceId)."'
        LIMIT
            1
        ";
        //delete operation limited to one, because there might exist many files bind to the same instance of the same module. For example: gallery widget adds the same photo twice.
        $rs = mysql_query($sql, $this->conn);
        if (!$rs){
            throw new \IpUpdate\Library\UpdateException('Can\'t bind new instance to the file '.$sql.' '.mysql_error(), \IpUpdate\Library\UpdateException::SQL);
        }

    }

    private function bindFile($file, $module, $instanceId) {
        $dbh = $this->dbh;
        $sql = "
        INSERT INTO
            `".$this->dbPref."m_administrator_repository_file`
        SET
            `fileName` = :file,
            `module` = :module,
            `instanceId` = :instanceId,
            `date` = :date
        ";

        $params = array(
            'file' => $file,
            'module' => $module,
            'instanceId' => $instanceId,
            'date' => time()
        );

        $q = $dbh->prepare($sql);
        $q->execute($params);


    }



    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getSourceVersion()
     */
    public function getSourceVersion()
    {
        return '3.2';
    }

    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getDestinationVersion()
     */
    public function getDestinationVersion()
    {
        return '3.3';
    }



    /**
     *
     *  Returns $data encoded in UTF8. Very useful before json_encode as it fails if some strings are not utf8 encoded
     * @param mixed $dat array or string
     * @return array
     */
    private function checkEncoding($dat)
    {
        if (is_string($dat)) {
            if (mb_check_encoding($dat, 'UTF-8')) {
                return $dat;
            } else {
                return utf8_encode($dat);
            }
        }
        if (is_array($dat)) {
            $answer = array();
            foreach ($dat as $i => $d) {
                $answer[$i] = $this->checkEncoding($d);
            }
            return $answer;
        }
        return $dat;
    }


}
