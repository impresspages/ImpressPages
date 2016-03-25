<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip;

/**
 * Handles web page forms
 *
 * @package Ip
 */
class Form
{
    const METHOD_POST = 'post';
    const METHOD_GET = 'get';
    const ENVIRONMENT_ADMIN = 'admin';
    const ENVIRONMENT_PUBLIC = 'public';


    /**
     * @var Form\Fieldset[]
     */
    protected $fieldsets;
    protected $method;
    protected $action;
    protected $attributes;
    protected $classes;
    protected $environment;
    protected $ajaxSubmit;
    protected $validate;

    public function __construct()
    {
        $this->fieldsets = array();
        $this->method = self::METHOD_POST;
        $this->action = ipConfig()->baseUrl();
        $this->attributes = array();
        $this->classes = array();
        $this->ajaxSubmit = true;
        $this->validate = true;
        $this->addClass('ipsAjaxSubmit');
        if (ipRoute()->isAdmin()) {
            $this->setEnvironment(self::ENVIRONMENT_ADMIN);
        } else {
            $this->addClass('ipModuleForm');
            $this->setEnvironment(self::ENVIRONMENT_PUBLIC);
        }


        $this->addCsrfCheck();
        $this->addSpamCheck();

    }

    /**
     * Add securityToken field
     */
    public function addSpamCheck()
    {
        $tokenField = new \Ip\Form\Field\Antispam();
        $tokenField->setName('antispam');
        $this->addField($tokenField);
    }

    /**
     * Remove securityToken field
     */
    public function removeSpamCheck()
    {
        $this->removeField('antispam');
    }

    /**
     * Set form environment. Depending on that public or admin translations and layout will be chosen.
     * ImpressPages tries to detect environment automatically based on current controller. You can set manually the right mode if needed.
     * @param $environment
     */
    public function setEnvironment($environment)
    {
        if ($environment == self::ENVIRONMENT_ADMIN) {
            $this->addClass('ipsModuleFormAdmin');
            $this->removeClass('ipModuleForm');
            $this->removeClass('ipsModuleFormPublic');
        } else {
            $this->addClass('ipsModuleFormPublic');
            $this->addClass('ipModuleForm');
            $this->removeClass('ipsModuleFormAdmin');
        }

        $this->environment = $environment;
    }

    /**
     * Get form environment. Depending on that public or admin translations and layout will be chosen.
     * @return mixed
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Add securityToken field
     */
    public function addCsrfCheck()
    {
        $tokenField = new \Ip\Form\Field\Csrf();
        $tokenField->setName('securityToken');
        $this->addField($tokenField);
    }

    /**
     * Remove securityToken field
     */
    public function removeCsrfCheck()
    {
        $this->removeField('securityToken');
    }

    /**
     * Remove field from fieldset
     * @param string $fieldName
     * @return int Removed fields count
     */
    public function removeField($fieldName)
    {
        $count = 0;
        foreach ($this->fieldsets as $fieldset) {
            $count += $fieldset->removeField($fieldName);
        }
        return $count;
    }

    /**
     *
     * Check if data passes form validation rules
     * @param array $data - post data from user or other source.
     * @return array Error list. Array key - error field name, value - error message. Empty array means no errors.
     */
    public function validate($data)
    {
        $fields = $this->getFields();
        $errors = array();
        foreach ($fields as $field) {
            $error = $field->validate($data, $field->getName(), $this->getEnvironment());
            if ($error !== false) {
                $errors[$field->getValidationInputName()] = $error;
            }
        }
        return $errors;
    }

    /**
     *
     * Filter data array. Return only those records that are expected according to form field names.
     * @param array $data
     * @return array
     */
    public function     filterValues($data)
    {
        $answer = array();
        $fields = $this->getFields();
        foreach ($fields as $field) {
            if (array_key_exists($field->getName(), $data)) {
                $answer[$field->getName()] = $data[$field->getName()];
            }
        }
        return $answer;
    }

    /**
     * Add a fieldset to a form
     * @param \Ip\Form\Fieldset | string $fieldset
     */
    public function addFieldset($fieldset)
    {
        if (is_string($fieldset)) {
            $fieldset = new \Ip\Form\Fieldset($fieldset);
        }
        $this->fieldsets[] = $fieldset;
    }


    /**
     * Add field to last fieldset. Create fieldset if does not exist.
     * @param Form\Field $field
     */
    public function addField(\Ip\Form\Field $field)
    {
        if (count($this->fieldsets) == 0) {
            $this->addFieldset(new Form\Fieldset());
        }
        end($this->fieldsets)->addField($field);
    }


    /**
     *
     * Set post method.
     * @param string $method Use \Ip\Form::METHOD_POST or \Ip\Form::METHOD_GET
     * @throws Exception
     */
    public function setMethod($method)
    {
        switch ($method) {
            case self::METHOD_POST:
            case self::METHOD_GET:
                $this->method = $method;
                break;
            default:
                throw new Exception ('Unknown method "' . $method . '"');
        }
    }

    /**
     * Get HTML form method attribute value
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }


    /**
     * Set HTML form action attribute
     *
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Get HTML form action attribute
     *
     * @return string Attribute
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get rendered form
     * @param View $view
     * @return string HTML form
     */
    public function render(\Ip\View $view = null)
    {
        if (!$view) {
            if ($this->getEnvironment() == self::ENVIRONMENT_ADMIN) {
                $view = ipView('Form/adminView/form.php');
            } else {
                $view = ipView('Form/publicView/form.php');
            }
        }
        $view->setVariables(
            array(
                'form' => $this
            )
        );

        return $view->render();
    }

    /**
     * Return all fieldsets
     * @return array|Form\Fieldset[]
     */
    public function getFieldsets()
    {
        return $this->fieldsets;
    }


    /**
     * Get all form fields
     *
     * @return Form\Field[]
     */
    public function getFields()
    {
        $fieldsets = $this->getFieldsets();
        $fields = array();
        foreach ($fieldsets as $fieldset) {
            $fields = array_merge($fields, $fieldset->getFields());
        }
        return $fields;
    }

    /**
     * Get form field
     *
     * @param $name
     * @return \Ip\Form\Field
     */
    public function getField($name)
    {
        $allFields = $this->getFields();
        foreach ($allFields as $field) {
            if ($field->getName() == $name) {
                return $field;
            }
        }
        return false;
    }

    /**
     * Add HTML attribute to the form
     * @param string $name Attribute name
     * @param string $value Attribute value
     */
    public function addAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Remove HTML attribute from the form
     * @param string $name
     */
    public function removeAttribute($name)
    {
        unset($this->attributes[$name]);
    }

    /**
     * Get all form attributes
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get all form attributes as HTML
     * @return string Attributes, provided in attribute="value" style
     */
    public function getAttributesStr()
    {
        $answer = '';
        foreach ($this->getAttributes() as $attributeKey => $attributeValue) {
            $answer .= ' ' . htmlspecialchars($attributeKey) . '="' . htmlspecialchars($attributeValue) . '"';
        }
        return $answer;
    }

    /**
     * Add CSS class to the form
     *
     * @param string $cssClass
     */
    public function addClass($cssClass)
    {
        $this->classes[$cssClass] = 1;
    }

    /**
     * Remove CSS class from the form
     * @param string $cssClass
     */
    public function removeClass($cssClass)
    {
        unset($this->classes[$cssClass]);
    }

    /**
     * Get a list of classes used in the form
     *
     * @return array An array containing class names
     */
    public function getClasses()
    {
        return array_keys($this->classes);
    }

    /**
     * Get a list of classes used in the form as HTML string
     *
     * @return string Attributes, provided in attribute="value" style
     */
    public function getClassesStr()
    {
        $answer = '';
        foreach ($this->getClasses() as $class) {
            $answer .= ' ' . $class;
        }
        return 'class="' . $answer . '"';
    }

    /**
     * Set ajaxSubmit attribute. If true, form will be automatically
     * @param $ajaxSubmit
     */
    public function setAjaxSubmit($ajaxSubmit)
    {
        if ($ajaxSubmit) {
            $this->addClass('ipsAjaxSubmit');
        } else {
            $this->removeClass('ipsAjaxSubmit');
        }
        $this->ajaxSubmit = $ajaxSubmit;
    }

    /**
     * Get ajaxSubmit property
     * @return bool
     */
    public function getAjaxSubmit()
    {
        return (bool)$this->ajaxSubmit;
    }

    /**
     * Set validate attribute. If true, form will be automatically validated by javascript on submit
     * @param $validate
     */
    public function setValidate($validate)
    {
        if ($validate) {
            $this->addClass('ipsValidate');
        } else {
            $this->removeClass('ipsValidate');
        }
        $this->validate = $validate;
    }


    /**
     * Get validate property
     * @param $validate
     * @return bool
     */
    public function getValidate($validate)
    {
        return (bool)$this->validate;
    }

    /**
     * @ignore
     */
    public function __toString()
    {
        return $this->render();
    }


}
