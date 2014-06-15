<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Repository;


class Filter
{
    public static function ipReflectionExtension($ext, $data)
    {
        if (empty($data['source']) || empty($data['options'])) {
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
                return $transform->getNewExtension($data['source'], $ext);
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
                return $transform->getNewExtension($data['source'], $ext);
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
                return $transform->getNewExtension($data['source'], $ext);
                break;
            case 'copy':

                return $ext;
                break;
            default:
                return $ext;
        }
    }

}
