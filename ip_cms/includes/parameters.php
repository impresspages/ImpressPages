<?php
/**
 * @package ImpressPages
 *
 *
 */


if (!defined('FRONTEND')&&!defined('BACKEND')) exit;


/**
 * @access private
 * @package ImpressPages
 */
class ParInteger {
    var $value = '';
    var $type = '';

    function __construct($value, $type) {
        $this->value = $value;
        $this->type = $type;
    }
}



/**
 * @access private
 * @package ImpressPages
 */
class ParString {
    var $value = '';
    var $type = '';

    function __construct($value, $type) {
        $this->value = $value;
        $this->type = $type;
    }
}


/**
 * @access private
 * @package ImpressPages
 */
class ParLangWord {
    var $value = '';
    var $type = '';

    function __construct($value, $type) {
        $this->value = $value;
        $this->type = $type;
    }

}


/**
 * @access private
 * @package ImpressPages
 */
class Parameters {


    function loadParameters($id, $reference, $languageId = null) {
        global $cms; //exists only in backend
        global $site; //exists only in frontend
        $parameters = array();


        $pTemp = \Db::getParString($id, $reference);
        foreach($pTemp as $type => $categories) {
            foreach($categories as $key => $category) {
                foreach($category as $key2 => $value) {
                    $parameters[$key][$key2] = new ParString($value, $type);
                }
            }
        }
        $pTemp = \Db::getParInteger($id, $reference);
        foreach($pTemp as $type => $categories) {
            foreach($categories as $key => $category) {
                foreach($category as $key2 => $value) {
                    $parameters[$key][$key2] = new ParInteger($value, $type);
                }
            }
        }

        $pTemp = \Db::getParBool($id, $reference);
        foreach($pTemp as $type => $categories) {
            foreach($categories as $key => $category) {
                foreach($category as $key2 => $value) {
                    $parameters[$key][$key2] = new ParInteger($value, $type);
                }
            }
        }

        if($languageId) {
            $pTemp = \Db::getParLang($id, $reference, $languageId);
            foreach($pTemp as $type => $categories) {
                foreach($categories as $key => $category) {
                    foreach($category as $key2 => $value) {
                        $parameters[$key][$key2] = new ParLangWord($value, $type);
                    }
                }
            }
        }



        return $parameters;
    }


}

/**
 * Class to store all website parameters.
 * @package ImpressPages
 */
class ParametersMod {
    /** @var array all used website parameters */
    var $parameters;
    /** @access private */
    var $parClass;


    /**
     * Initializes common variables
     * @return void
     */
    function __construct() {
        $this->parameters = array();
        $this->parClass = new Parameters();
    }


    /**
     * Finds type of specified parameter. All parameters ar joined into parameters groups. Each parameters group belongs to some module. Each module belongs to some module group.
     * @param string $modGroup
     * @param string $module
     * @param string $parGroup
     * @param string $parameter
     * @return string value
     */
    function getType($modGroup, $module, $parGroup, $parameter) {
        global $site;
        $languageId = $site->currentLanguage['id'];

        if(isset($this->parameters[$languageId][$modGroup][$module][$parGroup][$parameter]))
        return $this->parameters[$languageId][$modGroup][$module][$parGroup][$parameter]->type;
        elseif(!isset($this->parameters[$languageId][$modGroup][$module])) {
            $tmpModule = \Db::getModule(null, $modGroup, $module);
            $this->parameters[$languageId][$modGroup][$module] = $this->parClass->loadParameters($tmpModule['id'], 'module_id', $languageId);
            if(isset($this->parameters[$languageId][$modGroup][$module][$parGroup][$parameter]))
            return($this->parameters[$languageId][$modGroup][$module][$parGroup][$parameter]->type);
            else {
                $backtrace = debug_backtrace();
                if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
                trigger_error("Parameter doesn't exists (".$modGroup.", ".$module.", ".$parGroup.", ".$parameter.")  (Error source: ".($backtrace[0]['file'])." line: ".($backtrace[0]['line'])." ) ");
                else
                trigger_error("Parameter doesn't exists (".$modGroup.", ".$module.", ".$parGroup.", ".$parameter.")");
            }
        }else {
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
            trigger_error("Parameter doesn't exists (".$modGroup.", ".$module.", ".$parGroup.", ".$parameter.")  (Error source: ".($backtrace[0]['file'])." line: ".($backtrace[0]['line'])." ) ");
            else
            trigger_error("Parameter doesn't exists (".$modGroup.", ".$module.", ".$parGroup.", ".$parameter.")");
        }
    }



    /**
     * Finds value of specified parameter. All parameters ar joined into parameters groups. Each parameters group belongs to some module. Each module belongs to some module group.
     * @param string $modGroup
     * @param string $module
     * @param string $parGroup
     * @param string $parameter
     * @param int $languageId Language id if you wish to get parameters for specified language
     * @return mixed value
     */
    function getValue($modGroup, $module, $parGroup, $parameter, $languageId = null) {
        global $site;
        if($languageId == null && $site) //some parameters are accessed until site class is created. So, no language is specified.
        $languageId = $site->currentLanguage['id'];
        if(isset($this->parameters[$languageId][$modGroup][$module][$parGroup][$parameter]))
        return $this->parameters[$languageId][$modGroup][$module][$parGroup][$parameter]->value;
        elseif(!isset($this->parameters[$languageId][$modGroup][$module])) {
            $tmpModule = \Db::getModule(null, $modGroup, $module);
            $this->parameters[$languageId][$modGroup][$module] = $this->parClass->loadParameters($tmpModule['id'], 'module_id', $languageId);
            if(isset($this->parameters[$languageId][$modGroup][$module][$parGroup][$parameter]))
            return($this->parameters[$languageId][$modGroup][$module][$parGroup][$parameter]->value);
            else {
                $backtrace = debug_backtrace();
                if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
                trigger_error("Parameter doesn't exists (".$modGroup.", ".$module.", ".$parGroup.", ".$parameter.")  (Error source: ".($backtrace[0]['file'])." line: ".($backtrace[0]['line'])." ) ");
                else
                trigger_error("Parameter doesn't exists (".$modGroup.", ".$module.", ".$parGroup.", ".$parameter.")");
            }
        }else {
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
            trigger_error("Parameter doesn't exists (".$modGroup.", ".$module.", ".$parGroup.", ".$parameter.")  (Error source: ".($backtrace[0]['file'])." line: ".($backtrace[0]['line'])." ) ");
            else
            trigger_error("Parameter doesn't exists (".$modGroup.", ".$module.", ".$parGroup.", ".$parameter.")");
        }
    }


    /**
     * Finds parameters of specified module.
     * @param string $modGroup
     * @param string $module
     * @param int $languageId Language id if you wish to get parameters for specified language
     * @return array array parameters group. Each of which have array of parameters
     */
    function getGroups($modGroup, $module, $languageId = null) {
        global $site;

        if($languageId == null && $site)//some parameters are accessed until site class is not created. So, no language is specified.
        $languageId = $site->currentLanguage['id'];

        if(isset($this->parameters[$languageId][$modGroup][$module]))
        return $this->parameters[$languageId][$modGroup][$module];
        else {
            $tmpModule = \Db::getModule(null, $modGroup, $module);
            $this->parameters[$languageId][$modGroup][$module] = $this->parClass->loadParameters($tmpModule['id'], 'module_id', $languageId);
            if(isset($this->parameters[$languageId][$modGroup][$module]))
            return($this->parameters[$languageId][$modGroup][$module]);
            else
            trigger_error("Parameter doesn't exists ".$modGroup." ".$module." ".$parGroup." ".$parameter);
        }
    }


    /**
     * Set value of specified parameter. All parameters ar joined into parameters groups. Each parameters group belongs to some module. Each module belongs to some module group.
     * @param string $modGroup
     * @param string $module
     * @param string $parGroup
     * @param string $parameter
     * @param mixed $value value to set
     * @param int $languageId required if the parameter depends on language
     * @return mixed value
     */
    function setValue($modGroup, $module, $parGroup, $parameter, $value, $languageId = null) {
        $tmpModule = \Db::getModule(null, $modGroup, $module);
        $parameter = \Db::getParameter($tmpModule['id'], 'module_id', $parGroup, $parameter);



        if($parameter) {
            switch($parameter['type']) {
                case 'string':
                case 'textarea':
                case 'string_wysiwyg':
                    $parameter = \Db::setParString($parameter['id'], $value);
                    break;
                case 'integer':
                    $parameter = \Db::setParInteger($parameter['id'], $value);
                case 'bool':
                    $parameter = \Db::setParBool($parameter['id'], $value);
                    break;
                case 'lang':
                case 'lang_textarea':
                case 'lang_wysiwyg':
                    if($languageId)
                    $parameter = \Db::setParLang($parameter['id'], $value, $languageId);
                    else {
                        $backtrace = debug_backtrace();
                        if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
                        trigger_error('Can\'t set language related parameter without language id. '.$modGroup.' '.$module.' '.$parGroup.' '.$parameter.' '.$value.' (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
                        else
                        trigger_error('Can\'t set language related parameter without language id. '.$modGroup.' '.$module.' '.$parGroup.' '.$parameter.' '.$value);
                    }
                    break;
                default:
                    $backtrace = debug_backtrace();
                    if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
                    trigger_error('Unknown paramter type '.$parameter['type'].' (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
                    else
                    trigger_error('Unknown paramter type '.$parameter['type']);
                    break;
            }
        }
    }

    /**
     *
     * @return true if parameter exists
     */
    function exist($modGroup, $module, $parGroup, $parameter) {
        $tmpModule = \Db::getModule(null, $modGroup, $module);
        if($tmpModule) {
            $parameter = \Db::getParameter($tmpModule['id'], 'module_id', $parGroup, $parameter);
            if($parameter) {
                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }

    }


}





?>
