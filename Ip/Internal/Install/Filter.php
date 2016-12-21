<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Install;

class Filter {
    public static function ipSendResponse($response)
    {
        if (is_object($response) && method_exists($response, 'setTitle')) {
            $response->setTitle(__('ImpressPages installation wizard', 'Install'));
        }
        return $response;
    }
}
