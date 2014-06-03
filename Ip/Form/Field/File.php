<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;

use Ip\Form\Field;


class File extends Field
{

    /**
     * Render field
     *
     * @param string $doctype
     * @param $environment
     * @return string
     */
    public function render($doctype, $environment) {
        $data = array (
            'attributesStr' => $this->getAttributesStr($doctype),
            'classes' => implode(' ', $this->getClasses()),
            'inputName' => $this->getName()
        );

        if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
            $viewFile = 'adminView/file.php';
        } else {
            $viewFile = 'publicView/file.php';
        }
        $view = ipView($viewFile, $data);

        return $view->render();
    }

    /**
     * Get class type
     *
     * CSS class that should be applied to surrounding element of this field.
     * By default empty. Extending classes should specify their value.
     * @return string
     */
    public function getTypeClass() {
        return 'file';
    }

    /**
     * Get values as string
     *
     * @param array $values All posted form values.
     * @param string $value Key this field name.
     * @return string
     */
    public function getValueAsString($values, $valueKey) {
        if (isset($values[$valueKey]['file']) && is_array($values[$valueKey]['file'])) {
            return implode(', ', $values[$valueKey]['file']);
        } else {
            return '';
        }
    }

    /**
     * Validate if field passes validation
     *
     * @param array $data usually array of string. But some elements could be null or even array (eg. password confirmation field, or multiple file upload field).
     * @param string $valueKey This value key could not exist in values array.
     * @return string Return string on error or false on success.
     */
    public function validate($values, $valueKey, $environment)
    {
        if (isset($values[$valueKey]['file']) && is_array($values[$valueKey]['file'])) {
            foreach ($values[$valueKey]['file'] as $file) {
                $uploadModel = \Ip\Internal\Repository\UploadModel::instance();
                if (!$uploadModel->isFileUploadedByCurrentUser($file, true)) {
                    if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
                        $error = __('Session has ended. Please remove and re-upload files.', 'Ip-admin', false);
                    } else {
                        $error = __('Session has ended. Please remove and re-upload files.', 'Ip', false);
                    }

                    return $error;
                }
            }
        }

        return parent::validate($values, $valueKey, $environment);
    }

    /**
     * Get files
     *
     * @param $values
     * @param $valueKey
     * @return array
     * @throws \Exception
     */
    public static function getFiles($values, $valueKey) {
        if (isset($values[$valueKey]['file']) && is_array($values[$valueKey]['file'])) {
            $answer = array();
            foreach ($values[$valueKey]['file'] as $file) {
                $uploadModel = \Ip\Internal\Repository\UploadModel::instance();
                if (!$uploadModel->isFileUploadedByCurrentUser($file, true)) {
                    ipLog()->alert('Core.tryToAccessNotUploadedFile', array('file' => $file));
                    continue;
                }
                $answer[] = $uploadModel->getUploadedFilePath($file, true);
            }

            return $answer;
        } else {
            return array();
        }
    }

    /**
     * Original file names
     *
     * @param $values
     * @param $valueKey
     * @return array
     * @throws \Exception
     */
    public static function originalFileNames($values, $valueKey) {
        if (isset($values[$valueKey]['file']) && is_array($values[$valueKey]['file'])) {
            $answer = array();
            foreach ($values[$valueKey]['file'] as $key => $file) {
                $uploadModel = \Ip\Internal\Repository\UploadModel::instance();
                if (!$uploadModel->isFileUploadedByCurrentUser($file, true)) {
                    ipLog()->alert('Core.tryToAccessNotUploadedFile', array('file' => $file));
                    continue;
                }
                $originalFileName = $file;
                if (isset($values[$valueKey]['originalFileName'][$key]) && is_string($values[$valueKey]['originalFileName'][$key])) {
                    $originalFileName = $values[$valueKey]['originalFileName'][$key];
                }

                $answer[] = $originalFileName;
            }

            return $answer;
        } else {
            return array();
        }
    }

}
