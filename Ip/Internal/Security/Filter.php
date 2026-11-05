<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Security;

class Filter
{
    /**
     * Adds the security headers to every response ImpressPages sends: pages, the admin, JSON, redirects and 404s.
     * Files the web server serves directly (images, CSS, uploads) don't pass through here.
     */
    public static function ipSendResponse($response, $info = null)
    {
        if (!($response instanceof \Ip\Response) || !ipConfig()->database()) {
            return $response; // not installed yet: no settings to read
        }

        try {
            foreach (Model::headers() as $name => $value) {
                $response->addHeader($name . ': ' . $value);
            }
            if (ipGetOption('Config.securityHidePoweredBy', 1) && !headers_sent()) {
                header_remove('X-Powered-By');
            }
        } catch (\Exception $e) {
            ipLog()->error('Security.headersFailed: {message}', array('message' => $e->getMessage()));
        }

        return $response;
    }
}
