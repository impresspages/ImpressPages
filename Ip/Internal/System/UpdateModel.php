<?php

/**
 * @package   ImpressPages
 *
 *
 */


namespace Ip\Internal\System;


class UpdateModel
{



    private function getUpdateInfo()
    {
        if (!function_exists('curl_init')) {
            throw new UpdateException('CURL extension required');
        }

        $ch = curl_init();

        $curVersion = \Ip\ServiceLocator::storage()->get('Ip', 'version');

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 1800, // set this to 30 min so we dont timeout
            CURLOPT_URL => \Ip\Internal\System\Model::instance()->getImpressPagesAPIUrl(),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => 'module_group=service&module_name=communication&action=getUpdateInfo&curVersion=' . $curVersion
        );

        curl_setopt_array($ch, $options);

        $jsonAnswer = curl_exec($ch);

        $answer = json_decode($jsonAnswer, true);

        if ($answer === null || !isset($answer['status']) || $answer['status'] != 'success') {
            return false;
        }

        return $answer;
    }


}

