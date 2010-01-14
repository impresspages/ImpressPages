<?php
/**
  * PHP-Class hn_captcha_X1 Version 1.0, released 19-Apr-2004
  * is an extension for PHP-Class hn_captcha.
  * It adds a garbage-collector. (Useful, if you cannot use cronjobs.)
  * Author: Horst Nogajski, coding@nogajski.de
  *
  * $Id: hn_captcha.class.x1.php4,v 1.2 2007/12/23 23:23:45 horst Exp $
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
  * ----------------------------------------------------------------------------
  *
  * HISTORY
  *
  *
  * changes in version 1.1:   (2007-October-07)
  *
  *   - adapted the constructor to the new main-class Version 1.4
  *____
  *
  **/


/**
  * Tabsize: 4
  *
  **/



require_once('./hn_captcha.class.php');



/**
  * This class is an extension for hn_captcha-class. It adds a garbage-collector!
  *
  * Normally all used images will be deleted automatically. But everytime a user
  * doesn't finish a request one image stays as garbage in tempfolder.
  * With this extension you can collect & trash this.
  *
  * You can specify:
  * - when the garbage-collector should run, (default = after 100 calls)
  * - the maxlifetime for images, (default is 600, = 10 minutes)
  * - a filename-prefix for the captcha-images (default = 'hn_captcha_')
  * - absolute filename for a textfile which stores the current counter-value
  *   (default is $tempfolder.'hn_captcha_counter.txt')
  *
  * The classextension needs the filename-prefix to identify lost images
  * also if the tempfolder is shared with other scripts.
  *
  * If an error occures (with counting or trash-file-deleting), the class sets
  * the variable $classhandle->garbage_collector_error to TRUE.
  * You can check this in your scripts and if is TRUE, you might execute
  * an email-notification or something else.
  *
  *
  * @shortdesc Class that adds a garbage-collector to the class hn_captcha
  * @public
  * @author Horst Nogajski, (mail: coding@nogajski.de)
  * @version 1.1
  * @date 2007-Octobre-07
  *
  **/
class hn_captcha_X1 extends hn_captcha
{

    ////////////////////////////////
    //
    //    PUBLIC PARAMS
    //

        /**
          * @shortdesc You optionally can specify an absolute filename for the counter. If is not specified, the class use the tempfolder and the default_basename.
          * @public
          * @type string
          *
          **/
        var $counter_filename        = '';

        /**
          * @shortdesc This is used as prefix for the picture filenames, so we can identify them also if we share the tempfolder with other programs.
          * @public
          * @type string
          *
          **/
        var $prefix                    = 'hn_captcha_';

        /**
          * @shortdesc The garbage-collector will started once when the class was called that number times.
          * @public
          * @type integer
          *
          **/
        var $collect_garbage_after    = 100;

        /**
          * @shortdesc Only trash files which are older than this number of seconds.
          * @public
          * @type integer
          *
          **/
        var $maxlifetime            = 600;

        /**
          * @shortdesc This becomes TRUE if the counter doesn't work or if trashfiles couldn't be deleted.
          * @public
          * @type boolean
          *
          **/
        var $garbage_collector_error    = FALSE;



    ////////////////////////////////
    //
    //    PRIVATE PARAMS
    //

        /** @private **/
        var $counter_fn_default_basename = 'hn_captcha_counter.txt';




    ////////////////////////////////
    //
    //    CONSTRUCTOR
    //

        /**
          * @shortdesc This calls the constructor of main-class for extracting the config array and generating all needed params. Additionally it control the garbage-collector.
          * @public
          * @type void
          * @return nothing
          *
          **/
        function hn_captcha_X1($config, $debug=FALSE, $secure=TRUE)
        {
            // Call Constructor of main-class
            $this->hn_captcha($config, $debug, $secure);


            // specify counter-filename
            if($this->counter_filename == '') $this->counter_filename = $this->tempfolder.$this->counter_fn_default_basename;
            if($this->debug) echo "\n<br>-Captcha-Debug: The counterfilename is (".$this->counter_filename.")";


            // retrieve last counter-value
            $test = $this->txt_counter($this->counter_filename);

            // set and retrieve current counter-value
            $counter = $this->txt_counter($this->counter_filename,TRUE);


            // check if counter works correct
            if(($counter !== FALSE) && ($counter - $test == 1))
            {
                // Counter works perfect, =:)
                if($this->debug) echo "\n<br>-Captcha-Debug: Current counter-value is ($counter). Garbage-collector should start at (".$this->collect_garbage_after.")";

                // check if garbage-collector should run
                if($counter >= $this->collect_garbage_after)
                {
                    // Reset counter
                    if($this->debug) echo "\n<br>-Captcha-Debug: Reset the counter-value. (0)";
                    $this->txt_counter($this->counter_filename,TRUE,0);

                    // start garbage-collector
                    $this->garbage_collector_error = $this->collect_garbage() ? FALSE : TRUE;
                    if($this->debug && $this->garbage_collector_error) echo "\n<br>-Captcha-Debug: ERROR! SOME TRASHFILES COULD NOT BE DELETED! (Set the garbage_collector_error to TRUE)";
                }

            }
            else
            {
                // Counter-ERROR!
                if($this->debug) echo "\n<br>-Captcha-Debug: ERROR! NO COUNTER-VALUE AVAILABLE! (Set the garbage_collector_error to TRUE)";
                $this->garbage_collector_error = TRUE;
            }
        }


    ////////////////////////////////
    //
    //    PRIVATE METHODS
    //


        /**
          * @shortdesc Store/Retrieve a counter-value in/from a textfile. Optionally count it up or store a (as third param) specified value.
          * @private
          * @type integer
          * @return counter-value
          *
          **/
        function txt_counter($filename,$add=FALSE,$fixvalue=FALSE)
        {
            if(is_file($filename) ? TRUE : touch($filename))
            {
                if(is_readable($filename) && is_writable($filename))
                {
                    $fp = @fopen($filename, "r");
                    if($fp)
                    {
                        $counter = (int)trim(fgets($fp));
                        fclose($fp);

                        if($add)
                        {
                            if($fixvalue !== FALSE)
                            {
                                $counter = (int)$fixvalue;
                            }
                            else
                            {
                                $counter++;
                            }
                            $fp = @fopen($filename, "w");
                            if($fp)
                            {
                                fputs($fp,$counter);
                                fclose($fp);
                                return $counter;
                            }
                            else return FALSE;
                        }
                        else
                        {
                            return $counter;
                        }
                    }
                    else return FALSE;
                }
                else return FALSE;
            }
            else return FALSE;
        }


        /**
          * @shortdesc Scanns the tempfolder for jpeg-files with nameprefix used by the class and trash them if they are older than maxlifetime.
          * @private
          *
          **/
        function collect_garbage()
        {
            $OK = FALSE;
            $captchas = 0;
            $trashed = 0;
            if($handle = @opendir($this->tempfolder))
            {
                $OK = TRUE;
                while(false !== ($file = readdir($handle)))
                {
                    if(!is_file($this->tempfolder.$file)) continue;
                    // check for name-prefix, extension and filetime
                    if(substr($file,0,strlen($this->prefix)) == $this->prefix)
                    {
                        if(strrchr($file, ".") == ".jpg")
                        {
                            $captchas++;
                            if((time() - filemtime($this->tempfolder.$file)) >= $this->maxlifetime)
                            {
                                $trashed++;
                                $res = @unlink($this->tempfolder.$file);
                                if(!$res) $OK = FALSE;
                            }
                        }
                    }
                }
                closedir($handle);
            }
            if($this->debug) echo "\n<br>-Captcha-Debug: There are ($captchas) captcha-images in tempfolder, where ($trashed) are seems to be lost.";
            return $OK;
        }


        /** @private **/
        function get_filename($public='')
        {
            if($public==='') $public = $this->public_key;
            return $this->tempfolder.$this->prefix.$public.'.jpg';
        }


        /** @private **/
        function get_filename_url($public='')
        {
            if($public==='') $public = $this->public_key;
            return str_replace($_SERVER['DOCUMENT_ROOT'],'',$this->tempfolder).$this->prefix.$public.'.jpg';
        }


} // END CLASS hn_CAPTCHA_X1

?>
