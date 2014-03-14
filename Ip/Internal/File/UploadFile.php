<?php
/**
 * @package		Library
 *
 *
 */

namespace Ip\Internal\File;





/**
 * moves uploaded file to specified directory
 *
 * Usage:<br /><br />
 * $answer = ->upload(post_name, destiation_directory)
 * if($answer == UPLOAD_ERR_OK)<br />
 *   echo ->fileName;<br />
 * else<br />
 *   echo error<br />
 *
 * @package Library
 */

class UploadFile{
    /** @var string file name of successfully uploaded file */
    var $fileName;

    /** @var array array of forbidden file extensions*/
    var $disallow; //disalowed extensions
    /** @var array array of allowed extensions. If defined, all other extensions are forbidden */
    var $allowOnly; //allow only defined extensions.

    /** @access private */
    function __construct(){
        $fileName = null;
        $this->disallow = array('htaccess','php', 'php2','php3','php4','php5','php6','cfm','cfc','bat','exe','com','dll','vbs','js','reg','asis','phtm','phtml','pwml','inc','pl','py','jsp','asp','aspx','ascx','shtml','sh','cgi', 'cgi4', 'pcgi', 'pcgi5');
        $this->allowOnly = null;
    }

    /**
     * @param array $extensions allowed extensions. All other extensions becomes forbidden
     */
    function allowOnly($extensions){ //allow only files with folowing extensios. $extensions - array of extensions without dots.
        if(is_array($extensions)){
            $this->allowOnly = $extensions;
            $this->disallow = array();
        }else
        trigger_error("Array expected");
    }

    /**
     * @param array additional forbidden extensions. Specified extensions are added to already existing extensions.
     */
    function disalow($extensions){ //disalow additional extensions. $extensions - array of extensions without dots.
        if(is_array($extensions))
        $this->$disallow = array_merge($this->disallow, $extensions);
        else
        trigger_error("Array expected");
    }


    /**
     * Check if the file is correct.
     * @access private
     * @param string $post_name name of file input
     * @return int returns error code or UPLOAD_ERR_OK if no error.
     */
    function getError($postName){
        if(isset($_FILES[$postName]) && $_FILES[$postName]['error'] == UPLOAD_ERR_OK){
            if($this->supportedFile($postName)){
                if(is_uploaded_file($_FILES[$postName]['tmp_name']))
                return UPLOAD_ERR_OK;
                else
                return UPLOAD_ERR_NO_FILE;
            }else
            return UPLOAD_ERR_EXTENSION;
        }elseif(isset($_FILES[$postName]))
        return $_FILES[$postName]['error'];
        else
        return UPLOAD_ERR_NO_FILE;
    }



    /**
     * @param string $post_name
     * @param string $destDir typicaly IMAGE_URL or TMP_IMAGE_URL
     * @return int error code. UPLOAD_ERR_OK on success
     */
    function upload($postName, $destDir){
        $this->fileName = '';
        $answer['name'] = '';


        $error = $this->getError($postName);
        if($error == UPLOAD_ERR_OK){
            //if($this->is_uploaded($postName)){
            $newName = \Ip\Internal\File\Functions::genUnoccupiedName($_FILES[$postName]['name'], $destDir);
            if(move_uploaded_file ($_FILES[$postName]['tmp_name'], $destDir.$newName)){
                $this->fileName = $newName;
                return UPLOAD_ERR_OK;
            }else
            return UPLOAD_ERR_CANT_WRITE;
        }else
        return $error;


        return false;
    }

    /**
     * @access private
     */

    function supportedFile($postName){
        $fileName = $_FILES[$postName]['name'];
        $fileExtension = strtolower(substr($fileName, strrpos($fileName, '.') + 1));

        $answer = true;

        if(is_array($this->allowOnly)){
            $answer = false;
            foreach($this->allowOnly as $key => $extension){
                if($fileExtension == $extension)
                $answer = true;
            }
        }
        foreach($this->disallow as $key => $extension){
            if($fileExtension == $extension)
            $answer = false;
        }

        return $answer;
    }

     


}