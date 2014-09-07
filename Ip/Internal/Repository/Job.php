<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Repository;


class Job
{

    /**
     * @param $info
     * @return array|null
     * @throws \Ip\Exception
     */
    public static function ipRouteAction_150($info)
    {
        $requestFile = ipFile('') . $info['relativeUri'];
        $fileDir = ipFile('file/');

        if (ipRequest()->getRelativePath() != $info['relativeUri']) {
            return null; //language specific url.
        }



        if (mb_strpos($requestFile, $fileDir) !== 0) {
            return null;
        }

        $reflection = mb_substr($requestFile, mb_strlen($fileDir));
        $reflection = urldecode($reflection);

        $reflectionModel = ReflectionModel::instance();
        $reflectionRecord = $reflectionModel->getReflectionByReflection($reflection);
        if ($reflectionRecord) {
            $reflectionModel->createReflection(
                $reflectionRecord['original'],
                $reflectionRecord['reflection'],
                json_decode($reflectionRecord['options'], true)
            );
            if (is_file(ipFile('file/' . $reflection))) {
                //supply file route

                $result['page'] = null;
                $result['plugin'] = 'Repository';
                $result['controller'] = 'PublicController';
                $result['action'] = 'download';

                return $result;
            }
        }


    }


    public static function ipCreateReflection($data)
    {
        if (empty($data['source']) || empty($data['destination']) || empty($data['options']) || empty($data['options']['type'])) {
            return null;
        }
        $options = $data['options'];


        switch ($options['type']) {
            case 'crop':
                $requiredParams = array(
                    'x1',
                    'y1',
                    'x2',
                    'y2',
                    'width',
                    'height'
                );
                $missing = array_diff($requiredParams, array_keys($options));
                if ($missing) {
                    throw new \Ip\Exception\Repository\Transform("Missing required parameters: " . implode(
                        ', ',
                        $missing
                    ));
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
                    'width',
                    'height'
                );
                $missing = array_diff($requiredParams, array_keys($options));
                if ($missing) {
                    throw new \Ip\Exception\Repository\Transform("Missing required parameters: " . implode(
                        ', ',
                        $missing
                    ));
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
                    'width',
                    'height'
                );
                $missing = array_diff($requiredParams, array_keys($options));
                if ($missing) {
                    throw new \Ip\Exception\Repository\Transform("Missing required parameters: " . implode(
                        ', ',
                        $missing
                    ));
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
            case 'width':
                $requiredParams = array(
                    'width'
                );
                $missing = array_diff($requiredParams, array_keys($options));
                if ($missing) {
                    throw new \Ip\Exception\Repository\Transform("Missing required parameters: " . implode(
                            ', ',
                            $missing
                        ));
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
                $transform = new Transform\ImageWidth($options['width'], $quality, $forced);
                $transform->transform($data['source'], $data['destination']);
                return true;
                break;
            case 'copy':
                copy($data['source'], $data['destination']);
                return true;
                break;
            default:
                return;
        }


    }

}
