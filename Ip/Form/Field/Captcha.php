<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;

use Ip\Form\Field;


class Captcha extends Field
{

    private $captchaInit;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->captchaInit = array(
            // string: absolute path (with trailing slash!) to a php-writeable tempfolder which is also accessible via HTTP!
            'tempfolder' => ipFile('file/tmp/'),
            // string: absolute path (in filesystem, with trailing slash!) to folder which contain your TrueType-Fontfiles.
            'TTF_folder' => ipFile('Ip/Lib/HnCaptcha/fonts/'),
            // mixed (array or string): basename(s) of TrueType-Fontfiles, OR the string 'AUTO'. AUTO scanns the TTF_folder for files ending with '.ttf' and include them in an Array.
            // Attention, the names have to be written casesensitive!
            //'TTF_RANGE'      => 'NewRoman.ttf',
            //'TTF_RANGE'      => 'AUTO',
            //'TTF_RANGE'      => array('actionj.ttf', 'bboron.ttf', 'epilog.ttf', 'fresnel.ttf', 'lexo.ttf', 'tetanus.ttf', 'thisprty.ttf', 'tomnr.ttf'),
            'TTF_RANGE' => 'AUTO',
            'chars' => 5,
            // integer: number of chars to use for ID
            'minsize' => 25,
            // integer: minimal size of chars
            'maxsize' => 30,
            // integer: maximal size of chars
            'maxrotation' => 25,
            // integer: define the maximal angle for char-rotation, good results are between 0 and 30
            'use_only_md5' => false,
            // boolean: use chars from 0-9 and A-F, or 0-9 and A-Z

            'noise' => true,
            // boolean: TRUE = noisy chars | FALSE = grid
            'websafecolors' => false,
            // boolean
            'refreshlink' => true,
            // boolean
            'lang' => 'en',
            // string:  ['en'|'de'|'fr'|'it'|'fi']
            'maxtry' => 3,
            // integer: [1-9]

            'badguys_url' => '/',
            // string: URL
            'secretstring' => md5(ipConfig()->get('sessionName')),
            // A very, very secret string which is used to generate a md5-key!
            'secretposition' => 9
            // integer: [1-32]
        );

        $this->addValidator('Required');

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
        $captcha = new \Ip\Lib\HnCaptcha\HnCaptcha($this->captchaInit, true);

        $captcha->make_captcha();

        $_SESSION['developer']['form']['field']['captcha'][$this->getId()]['public_key'] = $captcha->public_key;

        return '
        <div class="captcha">
        <input ' . $this->getAttributesStr($doctype) . ' class="form-control ' . implode(
            ' ',
            $this->getClasses()
        ) . '" name="' . htmlspecialchars($this->getName()) . '[code]" ' . $this->getValidationAttributesStr($doctype) . ' type="text" />
        <input type="hidden" name="' . htmlspecialchars($this->getName()) . '[id]" value="' . $this->getId() . '" />
        <img src="' . ipFileUrl($captcha->get_filename_url()) . '" alt="Captcha"/>
        </div>
        ';
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE_SYSTEM;
    }

    /**
     * Validate input value
     *
     * @param $values
     * @param $valueKey
     * @param $environment
     * @return string
     */
    public function validate($values, $valueKey, $environment)
    {
        if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
            $errorText = __('The characters you entered didn\'t match', 'Ip-admin', false);
        } else {
            $errorText = __('The characters you entered didn\'t match', 'Ip', false);
        }

        if (!isset($values[$this->getName()]['id']) || !isset($values[$this->getName()]['code'])) {
            return $errorText;
        }
        $code = $values[$this->getName()]['code'];
        $id = $values[$this->getName()]['id'];

        $captcha = new \Ip\Lib\HnCaptcha\HnCaptcha($this->captchaInit, true);

        if (!isset($_SESSION['developer']['form']['field']['captcha'][$id]['public_key'])) {
            return $errorText;
        }

        $realCode = strtolower(
            $captcha->generate_private($_SESSION['developer']['form']['field']['captcha'][$id]['public_key'])
        );
        if (strtolower($code) !== $realCode) {
            return $errorText;
        }

        return parent::validate($values, $valueKey, $environment);
    }

    /**
     * Get validation input name
     *
     * @return string
     */
    public function getValidationInputName()
    {
        return $this->name . '[code]';
    }

}
