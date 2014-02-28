<?php
/**
 * @package		Library
 *
 *
 */
namespace Ip\Internal\File;




/**
 *
 * resizes and moves uploaded image to directory.
 * Usage:<br /><br />
 *		$answer = ->upload($postName, $widthT, $heightT, $destDir, $type, $forced, $quality);<br />
 *     if($answer == UPLOAD_ERR_OK)<br />
 *       echo ->fileName;<br />
 *     else<br />
 *       echo "error";<br />
 *
 * @package Library
 */
class UploadImage{
    /** @var file name of successfully uploaded image */
    var $fileName;

    /** @access private */
    function __construct(){
        $fileName = null;
    }


    /**
     * Check if the file is correct.
     * @access private
     * @param string $postName name of file input
     * @return int returns error code or UPLOAD_ERR_OK if no error.
     */
    function getError($postName){
        if(isset($_FILES[$postName]) && $_FILES[$postName]['error'] == UPLOAD_ERR_OK){
            if($this->supportedFile($postName)){
                return UPLOAD_ERR_OK;
            }else
            return UPLOAD_ERR_EXTENSION;
        }elseif(isset($_FILES[$postName]))
        return $_FILES[$postName]['error'];
        else
        return UPLOAD_ERR_NO_FILE;
    }

    /**
     * @param string $postName
     * @param int $widthT required width
     * @param int $heightT required height
     * @param string $destDir typicaly IMAGE_URL or TMP_IMAGE_URL
     * @param string $type
     * Available types:
     *  fit - resize to fit
     *  crop - crop image if it don't fit
     *  width - resize to width
     *  height - resize to height
     * @param bool $forced if true, resizes image even if she fits to specified size (is smaller than required)
     * @param int $quality from 0 (biggest compression) to  100 (best quality)
     * @return int error code. UPLOAD_ERR_OK on success
     */
    function upload($postName, $widthT, $heightT, $destDir, $type, $forced, $quality){
        $this->fileName = '';
        $answer['name'] = '';


        $error = $this->getError($postName);
        if($error == UPLOAD_ERR_OK){
            $imageSize = getimagesize($_FILES[$postName]['tmp_name']);
            if ($this->resizeRequired($imageSize[0], $imageSize[1], $widthT, $heightT, $type, $forced)) {
                $memory_success = $this->getMemmoryNeeded($imageSize);
                if(!$memory_success)
                return UPLOAD_ERR_INI_SIZE;
                if($image = $this->createImage($postName)){
                    if($forced || $widthT < $imageSize[0] || $heightT < $imageSize[1])
                    $image = $this->resize($image, $widthT, $heightT, $imageSize[0], $imageSize[1], $type);
                     
                    //generate unocupied file name
                    $newName = $_FILES[$postName]['name'];
                    if($_FILES[$postName]['type'] == "image/gif")
                    $newName = substr($newName, -4, 4).'.png'; //gif are converted top PNG
                    $newName = \Ip\Internal\File\Functions::genUnoccupiedName($newName, $destDir);

                    switch ($_FILES[$postName]['type']) {
                        case 'image/gif':
                        case 'image/png':
                            //png quality is from 0 (no compression) to 9
                            $tmpQuality = $quality/10;
                            $tmpQuality = 9 - $tmpQuality;
                            if($tmpQuality < 0)
                            $tmpQuality = 0;
                            if (imagepng($image, $destDir.$newName, $tmpQuality)) {
                                $this->fileName = $newName;
                                return UPLOAD_ERR_OK;
                            } else {
                                return UPLOAD_ERR_CANT_WRITE;
                            }
                            break;
                        case 'image/pjpeg':
                        case 'image/jpeg':
                        default:
                            if(imagejpeg($image, $destDir.$newName, $quality)){
                                $this->fileName = $newName;
                                return UPLOAD_ERR_OK;
                            }else{
                                return UPLOAD_ERR_CANT_WRITE;
                            }
                            break;
                    }
                }else{
                    return UPLOAD_ERR_CANT_WRITE;
                }
            } else {
                $newName = \Ip\Internal\File\Functions::genUnoccupiedName($_FILES[$postName]['name'], $destDir);
                copy($_FILES[$postName]['tmp_name'], $destDir.$newName);
                $this->fileName = $newName;
                return UPLOAD_ERR_OK;
            }

        }else{
            return $error;
        }
        return false;
    }

    public function resizeRequired($widthS, $heightS, $widthT, $heightT, $type, $forced) {
        switch($type){
            case 'fit':
                if ($forced) {
                    return $widthS != $widthT || $heightS != $heightT;
                } else {
                    return $widthS > $widthT || $heightS > $heightT;
                }
                break;
            case 'crop':
                if ($forced) {
                    return $widthS != $widthT || $heightS != $heightT;
                } else {
                    return $widthS > $widthT || $heightS > $heightT;
                }
                break;
            case 'width':
                if ($forced) {
                    return $widthS != $widthT;
                } else {
                    return $widthS > $widthT;
                }
                break;
            case 'height':
                if ($forced) {
                    return $heightS != $heightT;
                } else {
                    return $heightS > $heightT;
                }
                break;
        }
    }

    /**
     * @access private
     */
    function resize($image, $widthDest, $heightDest, $widthSource, $heightSource, $type){

        $dest_proportion = $widthDest / $heightDest;
        $sourceProportion = (double)$widthSource / (double)$heightSource;


         
         
        switch($type){
            case 'fit':
                if($sourceProportion > $dest_proportion){
                    $width_skirtumas = 0;
                    $height_skirtumas = ($heightDest - $widthDest/($sourceProportion))/2;
                }else{
                    $width_skirtumas = ($widthDest - $heightDest*($sourceProportion))/2;
                    $height_skirtumas = 0;
                }

                if($height_skirtumas == 0 && $width_skirtumas != 0)
                $widthDest = $heightDest * $sourceProportion;
                elseif($height_skirtumas != 0 && $width_skirtumas == 0){
                    $heightDest = $widthDest / $sourceProportion;
                }

                $imageNew = imagecreatetruecolor($widthDest, $heightDest);
                imagealphablending($imageNew, false);
                imagesavealpha($imageNew,true);
                $color = imagecolorallocatealpha($imageNew, 255, 255, 255, 127);
                imagefilledrectangle ( $imageNew, 0, 0, $widthDest, $heightDest, $color );
                imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $widthDest, $heightDest, $widthSource, $heightSource);
                break;
            case 'crop':
                if($sourceProportion > $dest_proportion){
                    $width_skirtumas = ($widthSource - $heightSource*($dest_proportion))/2;
                    $height_skirtumas = 0;
                }else{
                    $width_skirtumas = 0;
                    $height_skirtumas = ($heightSource - $widthSource/$dest_proportion)/2;
                }

                $imageNew = imagecreatetruecolor($widthDest, $heightDest);
                imagealphablending($imageNew, false);
                imagesavealpha($imageNew,true);
                $color = imagecolorallocatealpha($imageNew, 255, 255, 255, 127);
                imagefilledrectangle ( $imageNew, 0, 0, $widthDest, $heightDest, $color );
                imagecopyresampled($imageNew, $image, 0, 0, $width_skirtumas, $height_skirtumas, $widthDest, $heightDest, $widthSource-$width_skirtumas*2, $heightSource-$height_skirtumas*2);
                break;
            case 'width':

                $heightTmp = $widthDest/$sourceProportion;

                $imageNew = imagecreatetruecolor($widthDest, $heightTmp);
                imagealphablending($imageNew, false);
                imagesavealpha($imageNew,true);
                $color = imagecolorallocatealpha($imageNew, 255, 255, 255, 127);
                imagefilledrectangle ( $imageNew, 0, 0, $widthDest, $heightTmp, $color );
                imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $widthDest, $heightTmp, $widthSource, $heightSource);

                if($heightTmp > $heightDest){
                    $image = $imageNew;
                    $imageNew = imagecreatetruecolor($widthDest, $heightDest);
                    $color = imagecolorallocate ($imageNew, 255, 255, 255 );
                    imagefilledrectangle ( $imageNew, 0, 0, $widthDest, $heightDest, $color );
                    imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $widthDest, $heightDest, $widthDest, $heightDest);
                }
                break;
            case 'height':
                $widthTmp = $heightDest*$sourceProportion;

                $imageNew = imagecreatetruecolor($widthTmp, $heightDest);
                imagealphablending($imageNew, false);
                imagesavealpha($imageNew,true);
                $color = imagecolorallocatealpha($imageNew, 255, 255, 255, 127);
                imagefilledrectangle ( $imageNew, 0, 0, $widthTmp, $heightDest, $color );
                imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $widthTmp, $heightDest, $widthSource, $heightSource);

                if($widthTmp > $widthDest){
                    $image = $imageNew;
                    $imageNew = imagecreatetruecolor($widthDest, $heightDest);
                    $color = imagecolorallocate ($imageNew, 255, 255, 255 );
                    imagefilledrectangle ( $imageNew, 0, 0, $widthDest, $heightDest, $color );
                    imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $widthDest, $heightDest, $widthDest, $heightDest);
                }
                break;

        }
         

        return $imageNew;

    }


    /**
     * @access private
     */
    function genName($postName, $destDir){
        $newName = basename($_FILES[$postName]['name']);
        $newName = substr($newName, 0, strrpos($newName, ".") );
        $spec = array("?", "-", "+", " ", "<", ">", "(", ")", "/", "\\", "&", ".", ",", "!", ":", "\"", "?", "|");
        $newName = str_replace($spec, "_", $newName);
        if($newName == "")
        $newName = "image_";
        if (file_exists($destDir.$newName.'.jpg')){
            $i = 1;
            while(file_exists($destDir.$newName.'_'.$i.'.jpg')){
                $i++;
            }
            $newName = $newName.'_'.$i;
        }
        $newName .= ".jpg";
        return $newName;
    }



    /**
     * @access private
     */
    function createImage($postName){

        $image = false;
        switch ($_FILES[$postName]['type']) {
            case 'image/jpeg':
            case 'image/pjpeg':
                $image = imagecreatefromjpeg($_FILES[$postName]['tmp_name']);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($_FILES[$postName]['tmp_name']);
                imageAlphaBlending($image, false);
                imageSaveAlpha($image, true);
                break;
            case 'image/png':
                $image = imagecreatefrompng($_FILES[$postName]['tmp_name']);
                imageAlphaBlending($image, false);
                imageSaveAlpha($image, true);
                break;
        }

        return $image;
    }

    /**
     * @access private
     */
    function supportedFile($postName){
        return(($_FILES[$postName]['type'] == "image/jpeg") ||
        ($_FILES[$postName]['type'] == "image/pjpeg") ||
        ($_FILES[$postName]['type'] == "image/png") ||
        ($_FILES[$postName]['type'] == "image/gif"));
    }
    /**
     * @access private
     */
    function getMemmoryNeeded($image_info){
        if(!isset($image_info['channels']) || !$image_info['channels'])
        $image_info['channels'] = 4;
        if(!isset($image_info['bits']) || !$image_info['bits'])
        $image_info['bits'] = 8;

        $bytesRequired = round(($image_info[0] * $image_info[1] * $image_info['bits'] * $image_info['channels'] / 8 + Pow(2, 16)) * 1.65);

        return \Ip\Internal\System\Helper\SystemInfo::allocateMemory($bytesRequired);
    }

}


