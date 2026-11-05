<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Security;

/**
 * HTTP security headers, configured in Settings → Advanced → Security headers.
 */
class Model
{
    public static function frameOptions()
    {
        return array(
            'SAMEORIGIN' => __('Only this website (recommended)', 'Ip-admin', false),
            'DENY' => __('Nobody, not even this website', 'Ip-admin', false),
            '' => __('Anyone (don\'t send the header)', 'Ip-admin', false),
        );
    }

    public static function referrerPolicies()
    {
        return array(
            'strict-origin-when-cross-origin' => __('Send only the website address to other sites (recommended)', 'Ip-admin', false),
            'same-origin' => __('Send nothing to other sites', 'Ip-admin', false),
            'no-referrer' => __('Never send it', 'Ip-admin', false),
            '' => __('Don\'t send the header', 'Ip-admin', false),
        );
    }

    /**
     * The headers to send with the current response, as name => value.
     */
    public static function headers()
    {
        $headers = array();

        if (ipGetOption('Config.securityNosniff', 1)) {
            $headers['X-Content-Type-Options'] = 'nosniff';
        }

        $frame = (string)ipGetOption('Config.securityFrameOptions', 'SAMEORIGIN');
        if ($frame !== '' && array_key_exists($frame, self::frameOptions())) {
            $headers['X-Frame-Options'] = $frame;
        }

        $referrer = (string)ipGetOption('Config.securityReferrerPolicy', 'strict-origin-when-cross-origin');
        if ($referrer !== '' && array_key_exists($referrer, self::referrerPolicies())) {
            $headers['Referrer-Policy'] = $referrer;
        }

        $permissions = self::oneLine(ipGetOption('Config.securityPermissionsPolicy', 'camera=(), microphone=(), geolocation=()'));
        if ($permissions !== '') {
            $headers['Permissions-Policy'] = $permissions;
        }

        if (ipGetOption('Config.securityHsts', 0) && ipRequest()->isHttps()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000' . (ipGetOption('Config.securityHstsSubdomains', 0) ? '; includeSubDomains' : '');
        }

        $csp = self::oneLine(ipGetOption('Config.securityCsp', ''));
        if ($csp !== '' && self::isPublicPage()) {
            $name = ipGetOption('Config.securityCspReportOnly', 1) ? 'Content-Security-Policy-Report-Only' : 'Content-Security-Policy';
            $headers[$name] = $csp;
        }

        return $headers;
    }

    /**
     * The admin, the page editor, the theme preview and the login pages rely on inline scripts,
     * so a site's Content Security Policy isn't applied there.
     */
    public static function isPublicPage()
    {
        return !ipRoute()->isAdmin()
            && !ipIsManagementState()
            && !ipRequest()->getQuery('ipDesignPreview')
            && !ipAdminId()
            && !\Ip\Internal\Admin\Model::isLoginPage()
            && !\Ip\Internal\Admin\Model::isPasswordResetPage();
    }

    /**
     * Header values must be one line: line breaks could start a new header.
     */
    public static function oneLine($value)
    {
        return trim(preg_replace('/[\r\n]+/', ' ', (string)$value));
    }
}
