<?php

namespace Ip\Lib\HnCaptcha;

/**
  * PHP-Class hn_captcha Version 1.5.1, released 28-Jan-2008
  *
  * Version for PHP 5 !
  *
  * Author: Horst Nogajski, coding@nogajski.de
  *
  * $Id: hn_captcha.class.php5,v 1.7 2008/01/28 09:16:44 horst Exp $
  *
  * Download: http://hn273.users.phpclasses.org/browse/package/1569.html
  *
  * License: GNU LGPL (http://www.opensource.org/licenses/lgpl-license.html)
  *
  * This library is free software; you can redistribute it and/or
  * modify it under the terms of the GNU Lesser General Public
  * License as published by the Free Software Foundation; either
  * version 2.1 of the License, or (at your option) any later version.
  *
  * This library is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
  * Lesser General Public License for more details.
  *
  * You should have received a copy of the GNU Lesser General Public
  * License along with this library; if not, write to the Free Software
  * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  *
  **/


/**
  * This class generates a picture to use in forms that perform CAPTCHA test
  * (Completely Automated Public Turing to tell Computers from Humans Apart).
  * After the test form is submitted a key entered by the user in a text field
  * is compared by the class to determine whether it matches the text in the picture.
  *
  * The class is a fork of the original released at www.phpclasses.org
  * by Julien Pachet with the name ocr_captcha.
  *
  * The following enhancements were added:
  *
  * - Support to make it work with GD library before version 2
  * - Hacking prevention
  * - Optional use of Web safe colors
  * - Limit the number of users attempts
  * - Display an optional refresh link to generate a new picture with a different key
  *   without counting to the user attempts limit verification
  * - Support the use of multiple random TrueType fonts
  * - Control the output image by only three parameters: number of text characters
  *   and minimum and maximum size preserving the size proportion
  * - Preserve all request parameters passed to the page via the GET method,
  *   so the CAPTCHA test can be added to existing scripts with minimal changes
  * - Added a debug option for testing the current configuration
  *
  * All the configuration settings are passed to the class in an array when the object instance is initialized.
  *
  * The class only needs two function calls to be used: display_form() and validate_submit().
  *
  * The class comes with examplefiles.
  * If you don't have it: http://hn273.users.phpclasses.org/browse/package/1569.html
  *
  * ----------------------------------------------------------------------------
  *
  * HISTORY
  *
  *
  * changes in version 1.1:   (2004-April-16)
  *
  *  - added a new configuration-variable: maxrotation
  *
  *  - added a new configuration-variable: secretstring
  *
  *  - modified function get_try(): now ever returns a string of 16 chars
  *____
  *
  *
  * changes in version 1.2:   (2004-April-19)
  *
  *  - added a new configuration-variable: secretposition
  *
  *  - once more modified the function get_try(): generate a string of 32 chars length,
  *    where at secretposition is the number of current-try.
  *    Hopefully this is enough for hackprevention.
  *____
  *
  *
  * changes in version 1.3:   (2006-April-11)
  *
  *  - fixed a security-hole, what was discovered by Daniel Jagszent. Many thank's for
  *    testing, fixing and sharing it, Daniel!
  *    He has tested the class in a modified way, like it is described here:
  *    http://www.puremango.co.uk/cm_breaking_captcha_115.php
  *    It was possible to manually do the captcha-test, notice the public and private keys.
  *    In automated way this keys could send as long as the image-file exists!
  *    (with different other datas and independent from the new captcha-string!)
  *____
  *
  *
  * changes in version 1.4:   (2007-October-04)
  *
  *   - fixed get_gd_version to check extension_loaded and get the version directly from GD-library. (no more use of php_info())
  *     ( thanks to Jari Turkia and others )
  *
  *   - added display_form_part function. The general idea is to use HN-captcha as a part of an existing form.
  *     You can send these params to the function to get single-formparts:
  *     'image' | 'input' | 'text' | 'text_notvalid'
  *     The minimum required for the class to work is: 'image' and 'input' !
  *     Also a new examplefile comes with the package: hn_captcha.example_formpart.php
  *     ( thanks to Jari Turkia and all people who have posted to the supportforum )
  *
  *   - fixed use of Debug. Added param for DebugSwitch to constructor now.
  *     ( reported from and thanks to Mist Hill )
  *
  *   - added the ability to automatically scan the TTF_folder for available '.ttf'-files.
  *     To use this, you have to set the var TTF_range to (string)'AUTO', instead of passing and array with filenames.
  *     ( thanks to cYbercOsmOnauT (Tekin) )
  *
  *   - added new boolean Configuration-Param: use_only_md5
  *     If is set to TRUE, we only use chars 0-9 and A-F for Keygeneration,
  *     if is set to FALSE, the default, we use 2-9 (without 8) and A-Z (without B I O).
  *     Small range we get with md5($str), wide range we get with base64_encode(md5($str))
  *
  *   - modified the private key generator (function generate_private()) to replace some chars that may confuse the users, especially when using stylized fonts.
  *     ( thanks to Bill Price )
  *
  *   - added new string Configuration-Param: form_action_method
  *     Is only needed when working with function "Display_Form_Part".
  *     Value can be 'POST' or 'GET', default is 'POST'.
  *
  *   - added some Languages:
  *     - Italian,   Andrea Nicaretta
  *     - French,    Benoit Dausse
  *     - Finnish,   Jari Turkia
  *     ( thank you )
  *
  *   - packed some TrueTypeFonts for the package:
  *     - get them here: http://nogajski.de/horst/php/captcha/fonts.zip
  *       and unpack them with subfolder to the hn_captcha folder. Then the
  *       examples should run directly!
  *     - they are all from Tom7: http://fonts.tom7.com/
  *       please read his License http://fonts.tom7.com/legal/ or http://fonts.tom7.com/legal/readme.txt
  *       (the file is included in fonts-dir, too)
  *       ( many thanks Tom! )
  *
  *  ... and few other little changes and sanitizing.
  *
  *  ( ... and many thanks to all the people who have send me mails with nice words or ascii-art =:) )
  *____
  *
  *
  * changes in version 1.4.1:   (2007-October-21)
  *
  *   - added the ability to display a refresh button (to generate a new Captcha-ID if the current is unreadable)
  *     to the display_form_part function. It is _not_ added as a separate Form!
  *     Therefor I have:
  *
  *      - rewritten the constructor (rearranged some parts, modified Hackprevention-part)
  *
  *      - modified the display_form_part function: now you can send these params to the function to get
  *        the single-formparts: 'image' | 'input' | 'text' | 'text_notvalid' | 'refresh_button' | 'refresh_text'
  *        The minimum required for the class to work is: 'image' and 'input' !
  *
  *   - fixed some minor bugs related to Debug-Mode
  *
  *   - added Languages:
  *     - Dutch,     Paul Moers
  *     ( thank you )
  *____
  *
  *
  * changes in version 1.4.2:   (2007-November-12)
  *
  *   - added an additional Test if Freetype-Support is enabled. Some users have experienced that on some
  *     hosts the GD-Library was enabled, but not Freetype. Now the class checks for the function "ImageTTFText"
  *     in constructor. (Only when in Debug-Mode!)
  *____
  *
  *
  * changes in version 1.5.0:   (2007-December-24)
  *
  *   - created new classfile to support PHP 5: hn_captcha.class.php5
  *     therefor the old version for PHP 4 is moved into file hn_captcha.class.php4
  *     the file hn_captcha.class.php determines the running PHP-majorversion and includes the apropriate class-file.
  *
  *   - fixed a Bug with checking TrueTypeFiles in constructor, when passing an array of TTF-files
  *
  *____
  *
  **/

/**
  * Tabsize: 4
  *
  **/
use Ip\Internal\hn_captcha\HTML;
use Ip\Internal\hn_captcha\nothing;


/**
  * @shortdesc Class that generate a captcha-image with text and a form to fill in this text
  * @public
  * @author Horst Nogajski, (mail: coding@nogajski.de)
  * @version 1.5.0
  * @date 2007-December-24
  *
  **/
class HnCaptcha
{

    ////////////////////////////////
    //
    //    PUBLIC PARAMS
    //

        /**
          * @shortdesc Absolute path to a Tempfolder (with trailing slash!). This must be writeable for PHP and also accessible via HTTP, because the image will be stored there.
          * @type string
          * @public
          *
          **/
        public $tempfolder;

        /**
          * @shortdesc Absolute path to folder with TrueTypeFonts (with trailing slash!). This must be readable by PHP.
          * @type string
          * @public
          *
          **/
        public $TTF_folder;

        /**
          * @shortdesc A List with available TrueTypeFonts for random char-creation. CASE-SENSITIVE!!
          * @type mixed[array|string]
          * @public
          *
          **/
        public $TTF_RANGE  = array();

        /**
          * @shortdesc How many chars the generated text should have
          * @type integer
          * @public
          *
          **/
        public $chars        = 6;

        /**
          * @shortdesc If TRUE, only chars from 0-9 and A-F are used as the Keycode, otherwise nearly 0-9 and A-Z is used. (have a look at function 'generate_private')
          * @type boolean
          * @public
          *
          **/
        public $use_only_md5 = FALSE;

        /**
          * @shortdesc The minimum size a Char should have
          * @type integer
          * @public
          *
          **/
        public $minsize    = 20;

        /**
          * @shortdesc The maximum size a Char can have
          * @type integer
          * @public
          *
          **/
        public $maxsize    = 40;

        /**
          * @shortdesc The maximum degrees a Char should be rotated. Set it to 30 means a random rotation between -30 and 30.
          * @type integer
          * @public
          *
          **/
        public $maxrotation = 25;

        /**
          * @shortdesc Background noise On/Off (if is Off, a grid will be created)
          * @type boolean
          * @public
          *
          **/
        public $noise        = TRUE;

        /**
          * @shortdesc This will only use the 216 websafe color pallette for the image.
          * @type boolean
          * @public
          *
          **/
        public $websafecolors = FALSE;

        /**
          * @shortdesc Switches language, available are 'en' and 'de'. You can easily add more. Look in CONSTRUCTOR.
          * @type string
          * @public
          *
          **/
        public $lang        = "en";

        /**
          * @shortdesc If a user has reached this number of try's without success, he will moved to the $badguys_url
          * @type integer
          * @public
          *
          **/
        public $maxtry        = 3;

        /**
          * @shortdesc Gives the user the possibility to generate a new captcha-image.
          * @type boolean
          * @public
          *
          **/
        public $refreshlink = TRUE;

        /**
          * @shortdesc If a user has reached his maximum try's, he will located to this url.
          * @type boolean
          * @public
          *
          **/
        public $badguys_url = "/";

        /**
          * Number between 1 and 32
          *
          * @shortdesc Defines the position of 'current try number' in (32-char-length)-string generated by function get_try()
          * @type integer
          * @public
          *
          **/
        public $secretposition = 21;

        /**
          * @shortdesc The string is used to generate the md5-key.
          * @type string
          * @public
          *
          **/
        public $secretstring = "This is a very secret string. Nobody should know it, =:)";

        /**
          * @shortdesc Outputs configuration values for testing
          * @type boolean
          * @public
          *
          **/
        public $debug = FALSE;

        /**
          * @shortdesc Is only needed when working with function "Display_Form_Part", could be 'POST' or 'GET'
          * @type string
          * @public
          *
          **/
        public $form_action_method = 'POST';


    ////////////////////////////////
    //
    //    PRIVATE & PROTECTED PARAMS
    //


        private $lx;                   // width of picture

        private $ly;                   // height of picture

        private $jpegquality = 80;     // image quality

        private $noisefactor = 9;      // this will multiplyed with number of chars

        private $nb_noise;             // number of background-noise-characters

        private $TTF_file;             // holds the current selected TrueTypeFont

        private $public_K;

        private $private_K;

        private $key;                  // md5-key

        public $public_key;           // public key

        private $filename;             // filename of captcha picture

        private $gd_version;           // holds the Version Number of GD-Library

        private $current_try = 0;

        private $r;

        private $g;

        private $b;

        protected $msg1;

        protected $msg2;

        protected $buttontext;

        protected $refreshbuttontext;

        protected $QUERY_STRING;         // keeps the ($_GET) Querystring of the original Request



    ////////////////////////////////
    //
    //    CONSTRUCTOR
    //

        /**
          * @shortdesc Extracts the config array and generate needed params.
          * @private
          * @type void
          * @return nothing
          *
          **/
        public function __construct($config, $debug=FALSE, $secure=TRUE)
        {
            // Switch on/off Debugging
            //$this->debug = ($debug===TRUE || $debug===FALSE) ? $debug : FALSE;
            $this->debug = false;

            // Test for GD-Library(-Version)
            $this->gd_version = $this->get_gd_version(TRUE);
            if($this->gd_version === 0) die("There is no GD-Library-Support enabled. The Captcha-Class cannot be used!");
            if($this->debug)
            {
                echo "\n<br>-Captcha-Debug: The available GD-Library has version ".$this->get_gd_version();
                // Additional Test if Freetype is enabled, too
                if(!function_exists('ImageTTFText'))
                {
                    echo "\n<br>-Captcha-Debug: Uuups! There is no FreeType-Support on this host!";
                    echo "\n<br>-Captcha-Debug: GD-Library AND Freetype-Support has to be enabled to write Characters into pictures!";
                    die("\n<br>EXIT");
                }
            }


            // extracts config array
            if(is_array($config))
            {
                if($secure)
                {
                    if($this->debug) echo "\n<br>-Captcha-Debug: Extracts Config-Array in secure-mode!";
                    $valid = get_class_vars(get_class($this));
                    foreach($config as $k=>$v)
                    {
                        if(array_key_exists($k,$valid)) $this->$k = $v;
                    }
                }
                else
                {
                    if($this->debug) echo "\n<br>-Captcha-Debug: Extracts Config-Array in unsecure-mode!";
                    foreach($config as $k=>$v) $this->$k = $v;
                }
            }


            // set all messages
            // (if you add a new language, you also want to add a line to the function "notvalid_msg()" at the end of the class!)
            $usedchars = $this->use_only_md5 ? 'A..F' : 'A..Z';
            $this->usedchars = $usedchars;
            $this->messages = array(
                 'en'=>array(
                            'msg1'=>'Type the characters that you see in the box (<b>'.$this->chars.' characters</b>). The code can include characters <b>0..9</b> and <b>'.$usedchars.'</b>.',
                             'msg2'=>'I cannot read the characters. Generate a ',
                             'buttontext'=>'submit',
                             'refreshbuttontext'=>'new ID'
                            ),
                'de'=>array(
                            'msg1'=>'Bitte tragen Sie die <b>'.$this->chars.' Zeichen</b> in das Feld ein. Zeichen von <b>0..9</b> und <b>'.$usedchars.'</b> sind m�glich.',
                            'msg2'=>'Die Zeichen im Bild sind unleserlich. Generiere eine ',
                            'buttontext'=>'abschicken',
                            'refreshbuttontext'=>'neue ID'
                            ),
                'fr'=>array(
                            'msg1'=>'Vous devez lire et saisir les <b>'.$this->chars.' carat�res</b> pr�sent dans l\'image ci-dessus (<b>0..9</b> et <b>'.$usedchars.'</b>), dans le champ ci-dessous <br> et valider le formulaire.',
                            'msg2'=>'Les caract�res sont illisibles, merci de g�n�rer une nouvelle image.',
                            'buttontext'=>'valider',
                            'refreshbuttontext'=>'nouvelle ID'
                            ),
                'it'=>array(
                            'msg1'=>'Devi leggere e digitare i <b>'.$this->chars.' caratteri</b> tra <b>0..9</b> e <b>'.$usedchars.'</b>, e inviare il form.',
                            'msg2'=>'Oh no, non posso leggere questo. Genera un ',
                            'buttontext'=>'invia',
                            'refreshbuttontext'=>'nuovo ID'
                            ),
                'fi'=>array(
                            'msg1'=>'Kirjoita laatikossa lukeva varmistuskoodi (<b>'.$this->chars.' merkki�</b>). Koodi sis�lt�� merkkej� <b>0..9</b> ja <b>'.$usedchars.'</b>.',
                            'msg2'=>'En pysty lukemaan tuota. Generoi uusi ',
                            'buttontext'=>'L�het�',
                            'refreshbuttontext'=>'uusi ID'
                             ),
                'nl'=>array(
                       	    'msg1'=>'Geef de tekens in die u in het kader ziet (<b>'.$this->chars.' tekens</b>). De code kan de tekens <b>0..9</b> en <b>'.$usedchars.'</b> bevatten.',
                            'msg2'=>'Ik kan de tekens niet lezen. Genereer een ',
                            'buttontext'=>'verzenden',
                            'refreshbuttontext'=>'nieuw ID'
                            ),
		'tr'=>array(
			    'msg1'=>'Eğer kutusunda gördüğünüz karakterleri yazın (<b>'.$this->chars.' karakterler</b>). Kod <b>0..9</b> and <b>'.$usedchars.'</b> karakterlerini içerebilir.',
			    'msg2'=>'Karakterleri okuyamıyorum. Yeni bir ',
			    'buttontext'=>'gönder',
			    'refreshbuttontext'=>'yeni ID'
			   )
            );
            if(!isset($this->messages[$this->lang]) || !isset($this->messages[$this->lang]['msg1']) || !isset($this->messages[$this->lang]['msg2']) || !isset($this->messages[$this->lang]['buttontext']) || !isset($this->messages[$this->lang]['refreshbuttontext']))
            {
                $this->lang = 'en';
            }
            $this->msg1 = $this->sanitized_output($this->messages[$this->lang]['msg1']);
            $this->msg2 = $this->sanitized_output($this->messages[$this->lang]['msg2']);
            $this->buttontext = $this->sanitized_output($this->messages[$this->lang]['buttontext']);
            $this->refreshbuttontext = $this->sanitized_output($this->messages[$this->lang]['refreshbuttontext']);
            if($this->debug) echo "\n<br>-Captcha-Debug: Set messages to language: (".$this->lang.")";


            // Hackprevention
            if(
                (isset($_GET['maxtry']) || isset($_POST['maxtry']) || isset($_COOKIE['maxtry']))
                ||
                (isset($_GET['debug']) || isset($_POST['debug']) || isset($_COOKIE['debug']))
                )
            {
                $this->hack_prevention();
            }
            $_method = strtoupper($this->form_action_method)==='GET' ? '_GET' : '_POST';
            $is_refresh = isset($GLOBALS[$_method]['hncaptcha_refresh']) ? $GLOBALS[$_method]['hncaptcha_refresh'] : '';
            if($is_refresh !== '')
            {
                if($is_refresh !== $this->refreshbuttontext)
                {
                    $this->hack_prevention();
                }
                unset($GLOBALS[$_method]['hncaptcha_private_key']);
            }


            // check vars for maxtry, secretposition and min-max-size
            $this->maxtry = ($this->maxtry > 9 || $this->maxtry < 1) ? 3 : $this->maxtry;
            $this->secretposition = ($this->secretposition > 32 || $this->secretposition < 1) ? $this->maxtry : $this->secretposition;
            if($this->minsize > $this->maxsize)
            {
                $temp = $this->minsize;
                $this->minsize = $this->maxsize;
                $this->maxsize = $temp;
                if($this->debug) echo "<br>-Captcha-Debug: Oh dear! What do you think I mean with min and max? Switch minsize with maxsize.";
            }

            // sanitize pathes
            $this->tempfolder = str_replace(array('\\'), array('/'), $this->tempfolder);
            $this->tempfolder = (substr($this->tempfolder, -1) === '/') ? $this->tempfolder : $this->tempfolder . '/';
            if($this->debug) echo "\n<br>-Captcha-Debug: tempfolder is: (".$this->tempfolder.")";
            $this->TTF_folder = str_replace(array('\\'), array('/'), $this->TTF_folder);
            $this->TTF_folder = (substr($this->TTF_folder, -1) === '/') ? $this->TTF_folder : $this->TTF_folder . '/';
            if($this->debug)
            {
                if(is_readable($this->TTF_folder))
                {
                    echo "\n<br>-Captcha-Debug: TTF_folder is: (".$this->TTF_folder.")";
                }
                else
                {
                    echo "\n<br>-Captcha-Debug: TTF_folder is not readable! -(".$this->TTF_folder.")";
                }
            }
            if(!is_readable($this->TTF_folder)) die('Truetype-Directory is not readable!');


            // check TrueTypeFonts
            if(is_array($this->TTF_RANGE))
            {
                $TTF_TEMP = array();
                if($this->debug) echo "\n<br>-Captcha-Debug: Check given TrueType-Array! (".count($this->TTF_RANGE).")";
                for($T=0;$T<count($this->TTF_RANGE);$T++)
                {
                    if(is_readable($this->TTF_folder.$this->TTF_RANGE[$T]))
                    {
                        if($this->debug) echo "\n<br>-Captcha-Debug: add TrueTypeFile: (".$this->TTF_folder.$this->TTF_RANGE[$T].")";
                        $TTF_TEMP[] = $this->TTF_RANGE[$T];
                    }
                    else
                    {
                        if($this->debug) echo "\n<br>-Captcha-Debug: not found: TrueTypeFile-(".$this->TTF_folder.$this->TTF_RANGE[$T].")";
                    }
                }
                $this->TTF_RANGE = $TTF_TEMP;
                unset($TTF_TEMP);
                if($this->debug) echo "\n<br>-Captcha-Debug: Valid TrueType-files: (".count($this->TTF_RANGE).")";
                if(count($this->TTF_RANGE) < 1) die('No Truetypefont available for the CaptchaClass.');
            }
            else
            {
                if(strtoupper($this->TTF_RANGE)==='AUTO')
                {
                    $this->TTF_RANGE = array();
                    if($this->debug) echo "\n<br>-Captcha-Debug: Scans fontsdirectory for fontfiles!";
                    // Scan fontsdir for ttf-files
                    if($fonts_dir = opendir($this->TTF_folder))
                    {
                        while($file = @readdir($fonts_dir))
                        {
                            if(substr(strtolower($file), -4) != '.ttf')
                            {
                                continue;
                            }
                            if(is_readable($this->TTF_folder.$file))
                            {
                                if($this->debug) echo "\n<br>-Captcha-Debug: add TrueTypeFile: (".$this->TTF_folder.$file.")";
                                $this->TTF_RANGE[] = $file;
                            }
                        }
                        closedir($fonts_dir);
                    }
                    if($this->debug) echo "\n<br>-Captcha-Debug: Valid TrueType-files: (".count($this->TTF_RANGE).")";
                    if(count($this->TTF_RANGE) < 1) die('No Truetypefont available for the CaptchaClass.');
                }
                else
                {
                    if($this->debug) echo "\n<br>-Captcha-Debug: Check given TrueType-File! (".$this->TTF_RANGE.")";
                    if(!is_readable($this->TTF_folder.$this->TTF_RANGE)) die('No Truetypefont available for the CaptchaClass.');
                }
            }

            // select first TrueTypeFont
            $this->change_TTF();
            if($this->debug) echo "\n<br>-Captcha-Debug: Set current TrueType-File: (".$this->TTF_file.")";


            // get number of noise-chars for background if is enabled
            $this->nb_noise = $this->noise ? ($this->chars * $this->noisefactor) : 0;
            if($this->debug) echo "\n<br>-Captcha-Debug: Set number of noise characters to: (".$this->nb_noise.")";


            // set dimension of image
            $this->lx = ($this->chars + 1) * (int)(($this->maxsize + $this->minsize) / 1.5);
            $this->ly = (int)(2.4 * $this->maxsize);
            if($this->debug) echo "\n<br>-Captcha-Debug: Set image dimension to: (".$this->lx." x ".$this->ly.")";


            // keep params from original GET-request
            if($this->form_action_method !== 'GET')
            {
                $this->QUERY_STRING = strlen(trim(isset($_SERVER['QUERY_STRING'])? $_SERVER['QUERY_STRING'] : '')) > 0 ? '?'.strip_tags($_SERVER['QUERY_STRING']) : '';
                $refresh = $_SERVER['PHP_SELF'].$this->QUERY_STRING;
                if($this->debug) echo "\n<br>-Captcha-Debug: Keep this params from original GET-request: (".$this->QUERY_STRING.")";
            }


            // check Form_Vars
            $pub  = $this->get_form_var('hncaptcha_public_key');
            $priv = $this->get_form_var('hncaptcha_private_key');
            $try  = $this->get_form_var('hncaptcha');

            if($pub!==NULL)  $this->public_K = substr($pub,0,$this->chars);
            if($priv!==NULL) $this->private_K = substr($priv,0,$this->chars);
            $this->current_try = ($try===NULL) ? 0 : $this->get_try();

            if(!isset($GLOBALS[$_method]['hncaptcha_refresh'])) $this->current_try++;
            if($this->debug) echo "\n<br>-Captcha-Debug: Check {$this->form_action_method}-Vars, current try is: (".$this->current_try.")";


            // generate Keys
            $this->key = md5($this->secretstring);
            $this->public_key = substr(md5(uniqid(rand(),true)), 0, $this->chars);
            if($this->debug) echo "\n<br>-Captcha-Debug: Generate Keys, public key is: (".$this->public_key.")";

        }



    ////////////////////////////////
    //
    //    PUBLIC METHODS
    //

        /**
          *
          * @shortdesc displays a complete form with captcha-picture
          * @public
          * @type void
          * @return HTML-Output
          *
          **/
        public function display_form($only_body=FALSE)
        {
            $try = $this->get_try(FALSE);
            if($this->debug) echo "\n<br>-Captcha-Debug: Generate a string which contains current try: ($try)";
            $s = '';
            if(!$only_body)
            {
                $s .= '<div id="captcha">';
                $s .= '<form class="captcha" name="captcha1" action="'.$_SERVER['PHP_SELF'].$this->QUERY_STRING.'" method="POST">'."\n";
            }
            $s .= '<input type="hidden" name="hncaptcha" value="'.$try.'">'."\n";
            $s .= '<p class="captcha_notvalid">'.$this->notvalid_msg().'</p>';
            $s .= '<p class="captcha_1">'.$this->display_captcha()."</p>\n";
            $s .= '<p class="captcha_1">'.$this->msg1.'</p>';
            $s .= '<p class="captcha_1"><input class="captcha" type="text" name="hncaptcha_private_key" value="" maxlength="'.$this->chars.'" size="'.$this->chars.'">&nbsp;&nbsp;';
            if($this->refreshlink)
            {
                $s .= '<p class="captcha_2">'.$this->msg2;
                $s .= ' <input class="captcha_2" type="submit" name="hncaptcha_refresh" value="'.$this->refreshbuttontext.'">'."</p>\n";
            }
            $s .= '<input class="captcha" type="submit" value="'.$this->buttontext.'">'."</p>\n";
            if(!$only_body)
            {
                $s .= '</form>'."\n";
                $s .= '</div>';
            }
            if($this->debug) echo "\n<br>-Captcha-Debug: Output Form with captcha-image.<br><br>";
            return $s;
        }


        /**
          *
          * @shortdesc displays a form-part with captcha-picture
          * @public
          * @type void
          * @return HTML-Output
          *
          **/
        public function display_form_part($which='all')
        {
            $ret = '';
            $which = strtolower($which);

            if($which==='all')
            {
                $ret .= $this->display_form(TRUE);
                if($this->debug) echo "\n<br>-Captcha-Debug: Output Form-Part with captcha-image.<br><br>";
                return $ret;
            }

            if($which==='image')
            {
                $try = $this->get_try(FALSE);
                if($this->debug) echo "\n<br>-Captcha-Debug: Generate a string which contains current try: ($try)";
                $ret .= '<input type="hidden" name="hncaptcha" value="'.$try.'">'."\n";
                $ret .= $this->display_captcha()."\n";
            }

            if($which==='input')
            {
                $ret .= '<input class="captcha" type="text" name="hncaptcha_private_key" value="" maxlength="'.$this->chars.'" size="'.$this->chars.'">'."\n";
            }

            if($which==='text')
            {
                $ret .= $this->msg1."\n";
            }

            if($which==='text_notvalid')
            {
                $ret .= $this->notvalid_msg()."\n";
            }


            if($which==='refresh_text' || $which==='refreshtext')
            {
                $ret .= $this->msg2."\n";
            }

            if($which==='refresh_button' || $which==='refreshbutton')
            {
                $this->refreshlink = TRUE;
                $ret .= '<input class="captcha" type="submit" name="hncaptcha_refresh" value="'.$this->refreshbuttontext.'">'."\n";
            }


            if($this->debug) echo "\n<br>-Captcha-Debug: Output Form-Part: $which";
            return $ret;
        }


        /**
          *
          * @shortdesc validates POST-vars and return result
          * @public
          * @type integer
          * @return 0 = first call | 1 = valid submit | 2 = not valid | 3 = not valid and has reached maximum try's
          *
          **/
        public function validate_submit()
        {
            if($this->check_captcha($this->public_K,$this->private_K))
            {
                if($this->debug) echo "\n<br>-Captcha-Debug: Validating submitted form returns: (1)";
                return 1;
            }
            else
            {
                if($this->current_try > $this->maxtry)
                {
                    if($this->debug) echo "\n<br>-Captcha-Debug: Validating submitted form returns: (3)";
                    return 3;
                }
                elseif($this->current_try > 0)
                {
                    if($this->debug) echo "\n<br>-Captcha-Debug: Validating submitted form returns: (2)";
                    return 2;
                }
                else
                {
                    if($this->debug) echo "\n<br>-Captcha-Debug: Validating submitted form returns: (0)";
                    return 0;
                }
            }
        }



    ////////////////////////////////
    //
    //    PRIVATE METHODS
    //

        /** @private **/
        private function hack_prevention()
        {
            if($this->debug)
            {
                echo "\n<br>-Captcha-Debug: Buuh. You are a bad guy!<br><br>In production mode you would be redirected to this URL now: <a href=\"{$this->badguys_url}\">{$this->badguys_url}</a>!";
                exit(0);
            }
            else
            {
                if(isset($this->badguys_url) && !headers_sent())
                {
                    header('location: '.$this->badguys_url);
                }
            }
            die();
        }

        /** @private **/
        private function display_captcha($onlyTheImage=FALSE)
        {
            $this->make_captcha();
            $is = getimagesize($this->get_filename());
            $ret = "\n".'<img class="captchapict" src="'.$this->get_filename_url().'" '.$is[3].' alt="This is a captcha-picture. It is used to prevent mass-access by robots. (see: www.captcha.net)" title="">'."\n";
            return $onlyTheImage ? $ret : $this->public_key_input().$ret;
        }

        /** @private **/
        private function public_key_input()
        {
            return '<input type="hidden" name="hncaptcha_public_key" value="'.$this->public_key.'">';
        }

        /** @private **/
        public function make_captcha()
        {
            $private_key = $this->generate_private();
            if($this->debug) echo "\n<br>-Captcha-Debug: Generate private key: ($private_key)";

            // create Image and set the apropriate function depending on GD-Version & websafecolor-value
            if($this->gd_version >= 2 && !$this->websafecolors)
            {
                $func1 = 'imagecreatetruecolor';
                $func2 = 'imagecolorallocate';
            }
            else
            {
                $func1 = 'imageCreate';
                $func2 = 'imagecolorclosest';
            }
            $image = $func1($this->lx,$this->ly);
            if($this->debug) echo "\n<br>-Captcha-Debug: Generate ImageStream with: ($func1())";
            if($this->debug) echo "\n<br>-Captcha-Debug: For colordefinitions we use: ($func2())";


            // Set Backgroundcolor
            $this->random_color(224, 255);
            $back =  @imagecolorallocate($image, $this->r, $this->g, $this->b);
            @ImageFilledRectangle($image,0,0,$this->lx,$this->ly,$back);
            if($this->debug) echo "\n<br>-Captcha-Debug: We allocate one color for Background: (".$this->r."-".$this->g."-".$this->b.")";

            // allocates the 216 websafe color palette to the image
            if($this->gd_version < 2 || $this->websafecolors) $this->makeWebsafeColors($image);


            // fill with noise or grid
            if($this->nb_noise > 0)
            {
                // random characters in background with random position, angle, color
                if($this->debug) echo "\n<br>-Captcha-Debug: Fill background with noise: (".$this->nb_noise.")";
                for($i=0; $i < $this->nb_noise; $i++)
                {
                    srand((double)microtime()*1000000);
                    $size    = intval(rand((int)($this->minsize / 2.3), (int)($this->maxsize / 1.7)));
                    srand((double)microtime()*1000000);
                    $angle    = intval(rand(0, 360));
                    srand((double)microtime()*1000000);
                    $x        = intval(rand(0, $this->lx));
                    srand((double)microtime()*1000000);
                    $y        = intval(rand(0, (int)($this->ly - ($size / 5))));
                    $this->random_color(160, 224);
                    $color    = $func2($image, $this->r, $this->g, $this->b);
                    srand((double)microtime()*1000000);
                    $text    = chr(intval(rand(45,250)));
                    @ImageTTFText($image, $size, $angle, $x, $y, $color, $this->change_TTF(), $text);
                }
            }
            else
            {
                // generate grid
                if($this->debug) echo "\n<br>-Captcha-Debug: Fill background with x-gridlines: (".(int)($this->lx / (int)($this->minsize / 1.5)).")";
                for($i=0; $i < $this->lx; $i += (int)($this->minsize / 1.5))
                {
                    $this->random_color(160, 224);
                    $color    = $func2($image, $this->r, $this->g, $this->b);
                    @imageline($image, $i, 0, $i, $this->ly, $color);
                }
                if($this->debug) echo "\n<br>-Captcha-Debug: Fill background with y-gridlines: (".(int)($this->ly / (int)(($this->minsize / 1.8))).")";
                for($i=0 ; $i < $this->ly; $i += (int)($this->minsize / 1.8))
                {
                    $this->random_color(160, 224);
                    $color    = $func2($image, $this->r, $this->g, $this->b);
                    @imageline($image, 0, $i, $this->lx, $i, $color);
                }
            }

            // generate Text
            if($this->debug) echo "\n<br>-Captcha-Debug: Fill forground with chars and shadows: (".$this->chars.")";
            for($i=0, $x = intval(rand($this->minsize,$this->maxsize)); $i < $this->chars; $i++)
            {
                $text    = strtoupper(substr($private_key, $i, 1));
                srand((double)microtime()*1000000);
                $angle    = intval(rand(($this->maxrotation * -1), $this->maxrotation));
                srand((double)microtime()*1000000);
                $size    = intval(rand($this->minsize, $this->maxsize));
                srand((double)microtime()*1000000);
                $y        = intval(rand((int)($size * 1.5), (int)($this->ly - ($size / 7))));
                $this->random_color(0, 127);
                $color    =  $func2($image, $this->r, $this->g, $this->b);
                $this->random_color(0, 127);
                $shadow = $func2($image, $this->r + 127, $this->g + 127, $this->b + 127);
                @ImageTTFText($image, $size, $angle, $x + (int)($size / 15), $y, $shadow, $this->change_TTF(), $text);
                @ImageTTFText($image, $size, $angle, $x, $y - (int)($size / 15), $color, $this->TTF_file, $text);
                $x += (int)($size + ($this->minsize / 5));
            }
            @ImageJPEG($image, $this->get_filename(), $this->jpegquality);
            $res = file_exists($this->get_filename());
            if($this->debug) echo "\n<br>-Captcha-Debug: Save Image with quality [".$this->jpegquality."] as (".$this->get_filename().") returns: (".($res ? 'TRUE' : 'FALSE').")";
            @ImageDestroy($image);
            if($this->debug) echo "\n<br>-Captcha-Debug: Destroy Imagestream.";
            if(!$res) die('Unable to save captcha-image.');
        }

        /** @private **/
        private function makeWebsafeColors(&$image)
        {
            //$a = array();
            for($r = 0; $r <= 255; $r += 51)
            {
                for($g = 0; $g <= 255; $g += 51)
                {
                    for($b = 0; $b <= 255; $b += 51)
                    {
                        $color = imagecolorallocate($image, $r, $g, $b);
                        //$a[$color] = array('r'=>$r,'g'=>$g,'b'=>$b);
                    }
                }
            }
            if($this->debug) echo "\n<br>-Captcha-Debug: Allocate 216 websafe colors to image: (".imagecolorstotal($image).")";
            //return $a;
        }

        /** @private **/
        private function random_color($min,$max)
        {
            srand((double)microtime() * 1000000);
            $this->r = intval(rand($min,$max));
            srand((double)microtime() * 1000000);
            $this->g = intval(rand($min,$max));
            srand((double)microtime() * 1000000);
            $this->b = intval(rand($min,$max));
            //echo " (".$this->r."-".$this->g."-".$this->b.") ";
        }

        /** @private **/
        private function change_TTF()
        {
            if(is_array($this->TTF_RANGE))
            {
                srand((float)microtime() * 10000000);
                $key = array_rand($this->TTF_RANGE);
                $this->TTF_file = $this->TTF_folder.$this->TTF_RANGE[$key];
            }
            else
            {
                $this->TTF_file = $this->TTF_folder.$this->TTF_RANGE;
            }
            return $this->TTF_file;
        }

        /** @private **/
        private function check_captcha($public,$private)
        {
            $res = 'FALSE';
            // when check, destroy picture on disk
            if(file_exists($this->get_filename($public)))
            {
                $res = @unlink($this->get_filename($public)) ? 'TRUE' : 'FALSE';
                if($this->debug) echo "\n<br>-Captcha-Debug: Delete image (".$this->get_filename($public).") returns: ($res)";
                $res = (strtolower($private)===strtolower($this->generate_private($public))) ? 'TRUE' : 'FALSE';
                if($this->debug) echo "\n<br>-Captcha-Debug: Comparing public with private key returns: ($res)";
            }
            return $res==='TRUE' ? TRUE : FALSE;
        }
            /* OLD FUNCTION, without HotFix from Daniel Jagszent :
                function check_captcha($public,$private)
                {
                    // when check, destroy picture on disk
                    if(file_exists($this->get_filename($public)))
                    {
                        $res = @unlink($this->get_filename($public)) ? 'TRUE' : 'FALSE';
                        if($this->debug) echo "\n<br>-Captcha-Debug: Delete image (".$this->get_filename($public).") returns: ($res)";
                    }
                    $res = (strtolower($private)==strtolower($this->generate_private($public))) ? 'TRUE' : 'FALSE';
                    if($this->debug) echo "\n<br>-Captcha-Debug: Comparing public with private key returns: ($res)";
                    return $res == 'TRUE' ? TRUE : FALSE;
                }
            */

        /** @private **/
        public function get_filename($public='')
        {
            if($public==='') $public = $this->public_key;
            return $this->tempfolder.$public.'.jpg';
        }

        /** @private **/
        public function get_filename_url($public='')
        {
            if ($public === '') {
                $public = $this->public_key;
            }
            return 'file/tmp/'.$public.'.jpg';
        }

        /** @private **/
        private function get_form_var($varname)
        {
            if($this->form_action_method==='POST')
            {
                if(isset($_POST[$varname]))
                {
                    return strip_tags($_POST[$varname]);
                }
            }
            if($this->form_action_method==='GET')
            {
                if(isset($_GET[$varname]))
                {
                    return strip_tags($_GET[$varname]);
                }
            }
            return NULL;
        }

        /** @private **/
        private function get_try($in=TRUE)
        {
            $s = array();
            for($i = 1; $i <= $this->maxtry; $i++) $s[$i] = $i;

            if($in)
            {
                return (int)substr($this->get_form_var('hncaptcha'), (int)($this->secretposition - 1), 1);
            }
            else
            {
                $a = '';
                $b = '';
                for($i = 1; $i < $this->secretposition; $i++)
                {
                    srand((double)microtime()*1000000);
                    $a .= $s[intval(rand(1,$this->maxtry))];
                }
                for($i = 0; $i < (32 - $this->secretposition); $i++)
                {
                    srand((double)microtime()*1000000);
                    $b .= $s[intval(rand(1,$this->maxtry))];
                }
                return $a.$this->current_try.$b;
            }
        }

        /** @private **/
        private function get_gd_version($major=NULL)
        {
            if(extension_loaded('gd') && function_exists('gd_info'))
            {
                $stats = gd_info();
				// changed: 27.01.2008 horst
				// to match e.g. "bundled (2.0.28 compatible)" and also "2.0 or higher"
                //if(preg_match("/\(([\d\.]+)/", $stats['GD Version'], $matches))
                if(preg_match("/([\d\.]+)/", $stats['GD Version'], $matches))
                {
                    $gd_version_number = $matches[1];
                }
            }
            else
            {
                $gd_version_number = '0';
            }
            return $major!==NULL ? (int)substr($gd_version_number,0,1) : (string)$gd_version_number;
        }

        /** @private **/
        public function generate_private($public='')
        {
            if($public==='') $public = $this->public_key;
            if($this->use_only_md5)
            {
                $key = substr(md5($this->key.$public), 16 - (int)($this->chars / 2), $this->chars);
            }
            else
            {
                $key = substr(base64_encode(md5($this->key.$public)), 16 - (int)($this->chars / 2), $this->chars);
                $key = strtr($key, '0OoIi1B8+-_/=', 'WXxLL7452369H');
            }
            return $key;
        }

        /** @private **/
        private function sanitized_output($txt)
        {
            $trans = get_html_translation_table(HTML_ENTITIES);
            $txt = strtr($txt, $trans);
            return str_replace(array('&lt;','&gt;','&amp;nbsp;'), array('<','>','&nbsp;'), $txt);
        }

        /**
          *
          * @shortdesc returns a message if the form validation has failed
          * @private
          * @type string
          * @return string message or blankline as placeholder
          *
          **/
        private function notvalid_msg()
        {
            // blank line for all languages
            if($this->current_try == 1) return '&nbsp;<br>&nbsp;';

            // invalid try's: de
            if($this->lang == "de" && $this->current_try > 2 && $this->refreshlink) return $this->sanitized_output('Die Eingabe war nicht korrekt.<br>Tipp: Wenn Du die Zeichen nicht erkennen kannst, generiere neue mit dem Link unten!');
            if($this->lang == "de" && $this->current_try >= 2) return $this->sanitized_output('Die Eingabe war nicht korrekt. Bitte noch einmal versuchen:<br>&nbsp;');

            // invalid try's: fr
            if($this->lang == "fr" && $this->current_try > 2 && $this->refreshlink) return $this->sanitized_output('Saisie non valide. Veuillez essayer � nouveau:<br>Astuce: Si vous ne parvenez pas � lire les caract�res, vous pouvez g�n�rer une nouvelle image!');
            if($this->lang == "fr" && $this->current_try >= 2) return $this->sanitized_output('Saisie non valide. Veuillez essayer � nouveau:<br>&nbsp;');

            // invalid try's: fi
            if($this->lang == "fi" && $this->current_try > 2 && $this->refreshlink) return $this->sanitized_output('Ep�kelpo sy�te. Yrit� uudestaan:<br>Vihje: Jos et saa merkeist� selv��, generoi uusi koodi!');
            if($this->lang == "fi" && $this->current_try >= 2) return $this->sanitized_output('Ep�kelpo sy�te. Yrit� uudestaan:<br>&nbsp;');

            // invalid try's: nl
            if($this->lang == "nl" && $this->current_try > 2 && $this->refreshlink) return $this->sanitized_output('De ingevoerde code was onjuist. Probeer aub opnieuw:<br>Tip: Wanneer u de tekens niet kan lezen, kan u een nieuwe afbeelding genereren!');
            if($this->lang == "nl" && $this->current_try >= 2) return $this->sanitized_output('De ingevoerde code was onjuist. Probeer aub opnieuw:<br>&nbsp;');


            // THIS MUST BE THE LAST ENTRY IN FUNCTION, PLEASE ADD NEW LANGUAGES ABOVE THAT LINE!
            // invalid try's: en, AND THE DEFAULT, IF NO PART FOR A LANGUAGE IS DEFINED HERE, (BUT IN CONSTRUCTOR):
            if($this->current_try > 2 && $this->refreshlink) return $this->sanitized_output('No valid entry. Please try again:<br>Tip: If you cannot identify the chars, you can generate a new image!');
            if($this->current_try >= 2) return $this->sanitized_output('No valid entry. Please try again:<br>&nbsp;');
        }


} // END CLASS hn_CAPTCHA

?>
