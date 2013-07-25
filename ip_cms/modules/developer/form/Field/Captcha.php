<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\developer\form\Field;


class Captcha extends Field{
    private $catpchaInit;
    
    public function __construct($options) {
        $this->captchaInit = array(
        
        // string: absolute path (with trailing slash!) to a php-writeable tempfolder which is also accessible via HTTP!
              'tempfolder'     => BASE_DIR.TMP_IMAGE_DIR,
        
        // string: absolute path (in filesystem, with trailing slash!) to folder which contain your TrueType-Fontfiles.
              'TTF_folder'     => BASE_DIR.LIBRARY_DIR.'php/hn_captcha/fonts/',
        
        // mixed (array or string): basename(s) of TrueType-Fontfiles, OR the string 'AUTO'. AUTO scanns the TTF_folder for files ending with '.ttf' and include them in an Array.
        // Attention, the names have to be written casesensitive!
        //'TTF_RANGE'    => 'NewRoman.ttf',
        //'TTF_RANGE'    => 'AUTO',
        //'TTF_RANGE'    => array('actionj.ttf','bboron.ttf','epilog.ttf','fresnel.ttf','lexo.ttf','tetanus.ttf','thisprty.ttf','tomnr.ttf'),
              'TTF_RANGE'    => 'AUTO',
        
              'chars'          => 5,       // integer: number of chars to use for ID
              'minsize'        => 25,      // integer: minimal size of chars
              'maxsize'        => 30,      // integer: maximal size of chars
              'maxrotation'    => 25,      // integer: define the maximal angle for char-rotation, good results are between 0 and 30
              'use_only_md5'   => FALSE,   // boolean: use chars from 0-9 and A-F, or 0-9 and A-Z
        
              'noise'          => TRUE,    // boolean: TRUE = noisy chars | FALSE = grid
              'websafecolors'  => FALSE,   // boolean
              'refreshlink'    => TRUE,    // boolean
              'lang'           => 'en',    // string:  ['en'|'de'|'fr'|'it'|'fi']
              'maxtry'         => 3,       // integer: [1-9]
        
              'badguys_url'    => '/',     // string: URL
              'secretstring'   => md5(DB_PASSWORD),//'A very, very secret string which is used to generate a md5-key!',
              'secretposition' => 9        // integer: [1-32]
        );
        
        $this->addValidator('Required');
        
        parent::__construct($options);
    }
    
    public function render($doctype) {
        $attributesStr = '';

        $captcha = new \Library\Php\hn_captcha\HnCaptcha($this->captchaInit, TRUE);
        
        $captcha->make_captcha();
        
        $_SESSION['developer']['form']['field']['captcha'][$this->getId()]['public_key'] = $captcha->public_key;
        return '
        <input '.$this->getAttributesStr($doctype).' class="ipmControlInput '.implode(' ',$this->getClasses()).'" name="'.htmlspecialchars($this->getName()).'[code]" '.$this->getValidationAttributesStr($doctype).' type="text" />
        <input type="hidden" name="'.htmlspecialchars($this->getName()).'[id]" value="'.$this->getId().'" />
        <img src="'.BASE_URL.$captcha->get_filename_url().'" alt="Captcha"/><br />
        ';
    }
    
    /**
    * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
    */
    public function getTypeClass() {
        return 'captcha';
    }
    
    public function getType() {
        return self::TYPE_SYSTEM;
    }    
    
    public function validate($values, $valueKey) {

        if (!isset($values[$this->getName()]['id']) || !isset($values[$this->getName()]['code'])) {
            return ''; //that means error. We just don't have the text
        }
        $code = $values[$this->getName()]['code'];
        $id = $values[$this->getName()]['id'];

        $captcha = new \Library\Php\hn_captcha\HnCaptcha($this->captchaInit, TRUE);

        if (!isset($_SESSION['developer']['form']['field']['captcha'][$id]['public_key'])) {
            return ''; //that means error. We just don't have the text
        }

        $realCode = strtolower($captcha->generate_private($_SESSION['developer']['form']['field']['captcha'][$id]['public_key']));
        if(strtolower($code)!== $realCode){
            return ''; //that means error. We just don't have the text
        }
        
        return parent::validate($values, $valueKey);
    }    

    public function getValidationInputName() {
        return $this->name.'[code]';
    }

}