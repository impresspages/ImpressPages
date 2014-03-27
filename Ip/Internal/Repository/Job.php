<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Repository;


class Job {

    /**
     * @param $info
     * @return array|null
     * @throws \Ip\Exception
     */
    public static function ipRouteAction_150($info)
    {
        if (ipRequest()->getRelativePath() != $info['relativeUri']) {
            return; //language specific url.
        }

        $fileDirUrl = ipFileUrl('file/');
        $curUrl = ipConfig()->baseUrl() . $info['relativeUri'];

        if (mb_strpos($curUrl, $fileDirUrl) !== 0) {
            return;
        }

        $reflection = mb_substr($curUrl, mb_strlen($fileDirUrl));

        $reflectionModel = ReflectionModel::instance();
        $reflectionRecord = $reflectionModel->getReflectionByReflection($reflection);
        if ($reflectionRecord) {
            $reflectionModel->createReflection($reflectionRecord['original'], $reflectionRecord['reflection'], json_decode($reflectionRecord['options'], true));
            if (is_file(ipFile('file/' . $reflection))) {
                //supply file route
                $result['page'] = new \Ip\Page($page);
                $result['plugin'] = 'Content';
                $result['controller'] = 'PublicController';
                $result['action'] = 'index';
                $result['urlParts'] = isset($urlParts[1]) ? explode('/', $urlParts[1]) : array();

                return $result;
            }
        }


    }


    public static function ipCreateReflection($data)
    {
        if (empty($data['source']) || empty($data['destination']) || empty($data['options']) || empty($data['options']['type'])) {
            return;
        }
        $options = $data['options'];


        switch($options['type']) {
            case 'crop':
                $requiredParams = array(
                    'x1', 'y1', 'x2', 'y2', 'width', 'height'
                );
                $missing = array_diff($requiredParams, array_keys($options));
                if ($missing) {
                    throw new TransformException("Missing required parameters: " . implode(', ', $missing));
                }
                if (isset($options['quality'])) {
                    $quality = $options['quality'];
                } else {
                    $quality = null;
                }
                $transform = new Transform\ImageCrop($options['x1'], $options['y1'], $options['x2'], $options['y2'], $options['width'], $options['height'], $quality);
                $transform->transform($data['source'], $data['destination']);
                return true;
                break;
            case 'center':
                $requiredParams = array(
                    'width', 'height'
                );
                $missing = array_diff($requiredParams, array_keys($options));
                if ($missing) {
                    throw new TransformException("Missing required parameters: " . implode(', ', $missing));
                }
                if (isset($options['quality'])) {
                    $quality = $options['quality'];
                } else {
                    $quality = null;
                }
                $transform = new Transform\ImageCropCenter($options['width'], $options['height'], $quality);
                $transform->transform($data['source'], $data['destination']);
                return true;
                break;
            case 'fit':
                $requiredParams = array(
                    'width', 'height'
                );
                $missing = array_diff($requiredParams, array_keys($options));
                if ($missing) {
                    throw new TransformException("Missing required parameters: " . implode(', ', $missing));
                }
                if (isset($options['quality'])) {
                    $quality = $options['quality'];
                } else {
                    $quality = null;
                }
                if (isset($options['forced'])) {
                    $forced = $options['forced'];
                } else {
                    $forced = false;
                }
                $transform = new Transform\ImageFit($options['width'], $options['height'], $quality, $forced);
                $transform->transform($data['source'], $data['destination']);
                return true;
                break;
            case 'copy':
                cp($data['source'], $data['destination']);
                return true;
                break;
            default:
                return;
        }


    }

}
