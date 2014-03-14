<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;


use Ip\Form\Field;

class File extends Field
{



    public function render($doctype, $environment)
    {
        $data = array (
            'attributesStr' => $this->getAttributesStr($doctype),
            'classes' => implode(' ',$this->getClasses()),
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
    * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
    */
    public function getTypeClass()
    {
        return 'file';
    }


    /**
     * @param array $values all posted form values
     * @param string $valueKey this field name
     * @return string
     */
    public function getValueAsString($values, $valueKey)
    {
        if (isset($values[$valueKey]['file']) && is_array($values[$valueKey]['file'])) {
            return implode(', ',$values[$valueKey]['file']);
        } else {
            return '';
        }
    }


    /**
     *
     * Validate if field passes validation
     *
     */
    /**
     * Validate field
     * @param array $data usually array of string. But some elements could be null or even array (eg. password confirmation field, or multiple file upload field)
     * @param string $valueKey This value key could not exist in values array.
     * @return string return string on error or false on success
     */
    public function validate($values, $valueKey, $environment)
    {
        if (isset($values[$valueKey]['file']) && is_array($values[$valueKey]['file'])) {
            foreach($values[$valueKey]['file'] as $file) {
                $uploadModel = \Ip\Internal\Repository\UploadModel::instance();
                if (!$uploadModel->isFileUploadedByCurrentUser($file, true)) {
                    if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
                        $error = __('Session has ended. Please remove and re-upload files.', 'ipAdmin', false);
                    } else {
                        $error = __('Session has ended. Please remove and re-upload files.', 'ipPublic', false);
                    }
                    return $error;
                }
            }
        }
        return parent::validate($values, $valueKey, $environment);
    }


    /**
     * @param array $values all posted form values
     * @param string $valueKey this field name
     * @return Helper\UploadedFile[]
     */
    public function getFiles($values, $valueKey)
    {
        if (isset($values[$valueKey]['file']) && is_array($values[$valueKey]['file'])) {
            $answer = array();
            foreach($values[$valueKey]['file'] as $key => $file) {
                $originalFileName = $file;
                if (isset($values[$valueKey]['originalFileName'][$key]) && is_string($values[$valueKey]['originalFileName'][$key])) {
                    $originalFileName = $values[$valueKey]['originalFileName'][$key];
                }

                $uploadModel = \Ip\Internal\Repository\UploadModel::instance();
                if (!$uploadModel->isFileUploadedByCurrentUser($file, true)) {
                    throw new \Exception("Security risk. Current user doesn't seem to have uploaded this file");
                }
                $uploadedFile = new Helper\UploadedFile($uploadModel->getUploadedFilePath($file, true), $originalFileName);
                $answer[] = $uploadedFile;
            }
            return $answer;
        } else {
            return array();
        }
    }
}
