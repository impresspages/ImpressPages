<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Repository;


class PublicController extends \Ip\Controller {
    public static function download()
    {

        $fileDirUrl = ipFileUrl('file/');
        $curUrl = ipRequest()->getUrl();

        if (mb_strpos($curUrl, $fileDirUrl) !== 0) {
            throw new \Ip\Exception('Access denied');
        }

        $file = mb_substr($curUrl, mb_strlen($fileDirUrl));

        if (empty($file)) {
            throw new \Ip\Exception('Required parameter is missing');
        }


        $absoluteSource = str_replace('\\', '/', realpath(ipFile('file/' . $file)));
        if (!$absoluteSource || !is_file($absoluteSource)) {
            throw new \Ip\Exception("File doesn't exist", TransformException::MISSING_FILE);
        }

        if (
            strpos($absoluteSource, str_replace('\\', '/',ipFile('file/'))) !== 0
            ||
            strpos($absoluteSource, str_replace('\\', '/',ipFile('file/secure'))) === 0
        ) {
            throw new \Exception("Requested file (".$file.") is outside of public dir");
        }


        $mime = \Ip\Internal\File\Functions::getMimeType($absoluteSource);

        $fsize = filesize($absoluteSource);


        // set headers
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
//        header("Content-Description: File Transfer");
//        header("Content-Type: $mtype");
        header('Content-type: ' . $mime);
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . $fsize);

        // download
        // @readfile($file_path);
        $file = @fopen($absoluteSource,"rb");
        if ($file) {
            while(!feof($file)) {
                print(fread($file, 1024*8));
                flush();
                if (connection_status()!=0) {
                    @fclose($file);
                    die();
                }
            }
            @fclose($file);
        }
        ipDb()->disconnect();
        exit;

    }

}
