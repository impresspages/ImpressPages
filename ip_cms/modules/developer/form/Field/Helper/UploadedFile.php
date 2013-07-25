<?php
    /**
     * @package ImpressPages
         *
     */

namespace Modules\developer\form\Field\Helper;


/**
 * Entity of file that has been uploaded using file field
 *
 */
class UploadedFile{
    private $file;
    protected $originalFileName;

    public function __construct($file, $originalFileName)
    {
        $this->file = $file;
        $this->originalFileName = $originalFileName;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getOriginalFileName()
    {
        return $this->originalFileName;
    }

}