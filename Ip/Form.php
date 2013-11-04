<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip;




class Form{
    const METHOD_POST = 'post';
    const METHOD_GET = 'get';

    /**
     * @var $pages Page[]
     */
    protected $pages;
    protected $method;
    protected $action;
    protected $attributes;
    protected $classes;

    public function __construct() {
        $site = \Ip\ServiceLocator::getSite();
        $this->fieldsets = array();
        $this->method = self::METHOD_POST;
        $this->action = $site->getCurrentUrl();
        $this->pages = array();
        $this->attributes = array();
        $this->classes = array('ipModuleForm' => 1, 'ipsModuleForm' => 1);

        $this->addXssCheck();
    }

    /**
     * Add securityToken field
     */
    public function addXssCheck()
    {
        $tokenField = new \Ip\Form\Field\XSS();
        $tokenField->setName('securityToken');
        $this->addField($tokenField);
    }

    /**
     * Remove securityToken field
     */
    public function removeXssCheck()
    {
        $this->removeField('securityToken');
    }

    /**
     * Remove field from form
     * @param string $fieldName
     * @return int removed fields count
     */
    public function removeField($fieldName) {
        $count = 0;
        foreach($this->pages as $key => $page) {
            $count += $page->removeField($fieldName);
        }
        return $count;
    }

    /**
     *
     * Check if data passes form validation rules
     * @param array $data - post data from user or other source.
     * @return array errors. Array key - error field name. Value - error message. Empty array means there are no errors.
     */
    public function validate($data) {
        $fields = $this->getFields();
        $errors = array();
        foreach($fields as $field) {
            $error = $field->validate($data, $field->getName());
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
    public function filterValues($data) {
        $answer = array();
        $fields = $this->getFields();
        foreach($fields as $field) {
            if (isset($data[$field->getName()])) {
                $answer[$field->getName()] = $data[$field->getName()];
            }
        }
        return $answer;
    }

    public function addPage(\Ip\Form\Page $page) {
        $this->pages[] = $page;
    }

    public function addFieldset(\Ip\Form\Fieldset $fieldset) {
        if (count($this->pages) == 0) {
            $this->addPage(new Page());
        }
        end($this->pages)->addFieldset($fieldset);
    }

    /**
     *
     * Add field to last fielset. Create fieldset if does not exist.
     * @param Field $field
     */
    public function addField(\Ip\Form\Field\Field $field) {
        if (count($this->pages) == 0) {
            $this->addPage(new \Ip\Form\Page());
        }
        end($this->pages)->addField($field);
    }

    /**
     * Return all pages
     */
    public function getPages() {
        return $this->pages;
    }

    /**
     *
     * Set post method.
     * @param string $method Use \Ip\Form::METHOD_POST or \Ip\Form::METHOD_GET
     * @throws Exception
     */
    public function setMethod($method) {
        switch($method) {
            case self::METHOD_POST:
            case self::METHOD_GET:
                $this->method = $method;
                break;
            default:
                throw new Exception ('Unknown method "'.$method.'"', Exception::INCORRECT_METHOD_TYPE);
        }
    }

    public function getMethod() {
        return $this->method;
    }


    public function setAction($action) {
        $this->action = $action;
    }

    public function getAction() {
        return $this->action;
    }

    public function render(\Ip\View $view = null) {
        if (!$view) {
            $view = \Ip\View::create('Form/view/form.php');
        }
        $view->setData(array('form' => $this));
        return $view->render();
    }

    public function getFields() {
        $pages = $this->getPages();
        $fields = array();
        foreach ($pages as $page) {
            $fields = array_merge($fields, $page->getFields());
        }
        return $fields;
    }

    /**
     * @param $name
     * @return \Ip\Form\Field\Field
     */
    public function getField($name) {
        $allFields = $this->getFields();
        foreach($allFields as $key => $field) {
            if ($field->getName() == $name) {
                return $field;
            }
        }
        return false;
    }

    /**
     *
     * Add attribute to the form
     * @param string $name
     * @param string $value
     */
    public function addAttribute($name, $value) {
        $this->attributes[$name] = $value;
    }
    
    public function removeAttribute($name) {
        unset($this->attributes[$name]);
    }    

    public function getAttributes() {
        return $this->attributes;
    }

    public function getAttributesStr() {
        $answer = '';
        foreach ($this->getAttributes() as $attributeKey => $attributeValue) {
            $answer .= ' '.htmlspecialchars($attributeKey).'="'.htmlspecialchars($attributeValue).'"';
        }
        return $answer;
    }

    /**
    *
    * Add CSS class to the form
    * @param string $cssClass
    */
    public function addClass($cssClass) {
        $this->classes[$cssClass] = 1;
    }
    
    public function removeClass($cssClass) {
        unset($this->classes[$cssClass]);
    }
    
    public function getClasses() {
        return array_keys($this->classes);
    }
    
    public function getClassesStr() {
        $answer = '';
        foreach ($this->getClasses() as $class) {
            $answer .= ' '.$class;
        }
        return 'class="'.$answer.'"';
    }    
    
    /**
     * 
     * Store form data to the database
     * Keep notice, that this method does not do the validation of data.
     * So please validate submited data before writing to the database.* 
     * @param string $table where data should be stored
     * @param array $data posted or in the other way collected data
     * @param array $additionalData additional data that hasn't been posted, but is required to be inserted
     * @return bool|int
     * @throws \Exception
     */
    public function writeToDatabase($table, $data, $additionalData) {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            throw new \Exception("Data does not validate");
        }
        
        
        if(count($this->getFields()) == 0 && count($additionalValues) == 0){
            return false;
        }
        
        $sql = 'INSERT INTO `'.mysql_real_escape_string($table).'` SET ';
        $first = true;
        foreach($this->getFields() as $field){
            if($field->getDbField()){
                if(!$first) {
                    $sql .= ', ';
                }
                if (!isset($data[$field->getDbField()])) {
                    $sqlValue = 'NULL';
                } else {
                    $sqlValue = "'".mysql_real_escape_string($data[$field->getDbField()])."'";
                }
                $sql .= "`".mysql_real_escape_string($field->getDbField())."` = ".$sqlValue." ";
                $first = false;
            }
        }

        
        if($additionalData) {
            foreach($additionalData as $key => $additionalValue){
                if(!$first) {
                    $sql .= ', ';
                }
                if ($additionalValue === null) {
                    $sqlValue = 'NULL';
                } else {
                    $sqlValue = "'".mysql_real_escape_string($additionalValue)."'";
                }
                $sql .= "`".mysql_real_escape_string($key)."` = ".$sqlValue." ";
                $first = false;
                
            }
        }
        
        
        if ($first) { //if exist fields
            return false;
        }
        
        $rs = mysql_query($sql);
        if(!$rs){
            throw new \Exception($sql." ".mysql_error());
        }else{
            return mysql_insert_id();
        }
        
    }
    
    
    /**
     * 
     * Update record in the database.
     * Keep notice, that this method does not do the validation of data.
     * So please validate submited data before writing to the database.
     *  
     * @param string $table table name
     * @param string $id_field primary key field in the table
     * @param mixed $id id of record that needs to be updated
     * @param array $data posted data
     * @param array $additionalData additional data you would like to store in the same row
     * @return bool
     * @throws \Exception
     */
    public function updateDatabase($table, $id_field, $id, $data, $additionalData = array()){
        
        if(count($this->getFields()) == 0 && count($additionalData) == 0){
            return false;
        }
        
        
        $sql = 'UPDATE `'.mysql_real_escape_string($table).'` SET ';

        $first = true;
        foreach($this->getFields() as $field){
            if($field->getDbField() && isset($data[$field->getDbField()])){
                if(!$first) {
                    $sql .= ', ';
                }
                $sql .= "`".mysql_real_escape_string($field->getDbField())."` = '".mysql_real_escape_string($data[$field->getDbField()])."' ";
                $first = false;
            }
        }

        
        if($additionalData) {
            foreach($additionalData as $key => $additionalValue){
                if(!$first) {
                    $sql .= ', ';
                }
                if ($additionalValue === null) {
                    $sqlValue = 'NULL';
                } else {
                    $sqlValue = "'".mysql_real_escape_string($additionalValue)."'";
                }
                $sql .= "`".mysql_real_escape_string($key)."` = ".$sqlValue." ";
                $first = false;
                
            }
        }
            
        $sql .= " WHERE `".mysql_real_escape_string($id_field)."` = '".mysql_real_escape_string($id)."' ";

        if($first){
            return false;
        }        
        
        $rs = mysql_query($sql);
        if(!$rs) {
            throw new \Exception($sql." ".mysql_error());
        }
    }

    public function __toString() {
        return $this->render();
    }


}