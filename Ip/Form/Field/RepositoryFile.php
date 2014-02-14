<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;


use Ip\Form\Field;

/**
 * Repository file browser. Meaningful only in admin interface as public visitors can't access
 * CMS files repository.
 * Class RepositoryFile
 * @package Ip\Form\Field
 */
class RepositoryFile extends Field
{

    protected $fileLimit = 1;
    public function __construct($options = array())
    {
        if (isset($options['fileLimit'])) {
            $this->fileLimit = $options['fileLimit'];
        }
        parent::__construct($options);
    }

    public function render($doctype, $environment)
    {
        $data = array (
            'attributesStr' => $this->getAttributesStr($doctype),
            'classes' => implode(' ',$this->getClasses()),
            'inputName' => $this->getName(),
            'fileLimit' => $this->fileLimit
        );

        $viewFile = 'adminView/repositoryFile.php';
        $view = ipView($viewFile, $data);

        return $view->render();
    }

    /**
     * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
     */
    public function getTypeClass()
    {
        return 'repositoryFile';
    }

    public function setFileLimit($fileLimit)
    {
        $this->fileLimit = $fileLimit;
    }

    public function getFileLimit()
    {
        return $this->fileLimit;
    }

}
