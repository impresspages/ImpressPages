<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;

use Ip\Form\Field;


/**
 * Repository file browser
 *
 * Meaningful only in admin interface as public visitors can't access.
 */
class RepositoryFile extends Field
{

    protected $fileLimit = -1;
    protected $preview = 'thumbnails'; // List or thumbnails.
    protected $secure = false;
    protected $path = '';
    protected $filter = null;
    protected $filterExtensions = null;

    public function __construct($options = array())
    {
        if (isset($options['fileLimit'])) {
            $this->fileLimit = $options['fileLimit'];
        }
        if (isset($options['preview'])) {
            $this->preview = $options['preview'];
        }
        if (isset($options['secure'])) {
            $this->secure = (bool)$options['secure'];
        }
        if (isset($options['path'])) {
            $this->path = $options['path'];
        }
        if (isset($options['filter'])) {
            $this->filter = $options['filter'];
        }
        if (isset($options['filterExtensions'])) {
            $this->filterExtensions = $options['filterExtensions'];
        }
        parent::__construct($options);
    }

    /**
     * Render field
     *
     * @param string $doctype
     * @param $environment
     * @return string
     */
    public function render($doctype, $environment)
    {
        $data = array(
            'attributesStr' => $this->getAttributesStr($doctype),
            'classes' => implode(' ', $this->getClasses()),
            'inputName' => $this->getName(),
            'fileLimit' => $this->fileLimit,
            'value' => $this->getValue(),
            'preview' => $this->preview,
            'secure' => $this->secure,
            'path' => $this->path,
            'filter' => $this->filter,
            'filterExtensions' => $this->filterExtensions
        );

        $viewFile = 'adminView/repositoryFile.php';
        $view = ipView($viewFile, $data);

        return $view->render();
    }

    /**
     * Get field value
     *
     * @return string[]
     */
    public function getValue()
    {
        if (is_string($this->value)) {
            if (!empty($this->value)) {
                return array($this->value);
            } else {
                return array();
            }

        } else {
            return $this->value;
        }
    }



    /**
     * Set file limit
     *
     * @param int $fileLimit
     */
    public function setFileLimit($fileLimit)
    {
        $this->fileLimit = $fileLimit;
    }

    /**
     * Get file limit
     *
     * @return int
     */
    public function getFileLimit()
    {
        return $this->fileLimit;
    }

    /**
     * Get value as string
     *
     * @param array $values all posted form values
     * @param string $valueKey this field name
     * @return string
     */
    public function getValueAsString($values, $valueKey)
    {
        if (isset($values[$valueKey]) && is_array($values[$valueKey])) {
            return implode(', ', $values[$valueKey]);
        } else {
            return '';
        }
    }

}
