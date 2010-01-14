<?php

/*A4NmN*/

  require_once('hn_captcha.class.php');
    $CAPTCHA_INIT = array(

            // string: absolute path (with trailing slash!) to a php-writeable tempfolder which is also accessible via HTTP!
            'tempfolder'     => $_SERVER['DOCUMENT_ROOT'].'/images/tmp/',

            // string: absolute path (in filesystem, with trailing slash!) to folder which contain your TrueType-Fontfiles.
            'TTF_folder'     => dirname(__FILE__).'/fonts/',

            // mixed (array or string): basename(s) of TrueType-Fontfiles, OR the string 'AUTO'. AUTO scanns the TTF_folder for files ending with '.ttf' and include them in an Array.
            // Attention, the names have to be written casesensitive!
            //'TTF_RANGE'    => 'NewRoman.ttf',
            //'TTF_RANGE'    => 'AUTO',
            //'TTF_RANGE'    => array('actionj.ttf','bboron.ttf','epilog.ttf','fresnel.ttf','lexo.ttf','tetanus.ttf','thisprty.ttf','tomnr.ttf'),
            'TTF_RANGE'    => 'AUTO',

            'chars'          => 5,       // integer: number of chars to use for ID
            'minsize'        => 20,      // integer: minimal size of chars
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

  $captcha = new hn_captcha($CAPTCHA_INIT, TRUE);
echo $captcha->generate_private('01e7f').'aaa';
//echo $captcha->private_key;  
  
    $captcha->make_captcha();
    $is = getimagesize($captcha->get_filename());
    $ret = "\n".'<img class="captchapict" src="'.$captcha->get_filename_url().'" '.$is[3].' alt="This is a captcha-picture. It is used to prevent mass-access by robots. (see: www.captcha.net)" title="">'."\n";
    //echo $onlyTheImage ? $ret : $captcha->public_key_input().$ret;
echo $ret;

/*

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

*/

?>