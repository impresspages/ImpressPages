<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal;


class FormatHelper
{

    /**
     * @param int $bytes
     * @return string
     */
    public static function formatBytes($bytes)
    {
        $data = array(
            'bytes' => $bytes
        );

        $formattedBytes = ipJob('ipFormatBytes', $data);
        if ($formattedBytes === NULL) {

            $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
            $decimal = array('au', 'bn', 'bw', 'ch', 'cn', 'do', 'eg', 'gt', 'hk', 'hn', 'ie', 'il', 'in', 'jp', 'ke', 'kp', 'kr', 'lb', 'lk', 'mn', 'mo', 'mt', 'mx', 'my', 'ng', 'ni', 'np', 'nz', 'pa', 'ph', 'pk', 'sg', 'th', 'tw', 'tz', 'ug', 'uk', 'us', 'zw');

            $languageId = ipContent()->getCurrentLanguage()->getId();
            $language = \Ip\ServiceLocator::content()->getLanguage($languageId);
            $code = $language->getCode();

            for ($i = 0; $bytes >= 1024; $i++) {
                $bytes /= 1024;
                if ($i < 1) $bytes = round($bytes, 0);
                else $bytes = round($bytes, 1);
            }

            if (in_array($code, $decimal)) $formattedBytes = $bytes;
            else $formattedBytes = str_replace('.', ',', $bytes);

            $formattedBytes .= ' '.$sizes[$i];
        }

        return $formattedBytes;
    }

    /**
     * @param int $price Price in cents
     * @param string $currency Three letter currency code
     * @param string $context
     * @param int $languageId
     * @return string
     */
    public static function formatPrice($price, $currency, $context, $languageId = null)
    {
        if ($languageId === null) {
            $languageId = ipContent()->getCurrentLanguage()->getId();
        }

        $data = array(
            'price' => $price,
            'currency' => $currency,
            'context' => $context
        );

        $formattedPrice = ipJob('ipFormatPrice', $data);
        if ($formattedPrice === NULL) {
            if (function_exists('numfmt_create') && function_exists('numfmt_format_currency')) {
                $language = \Ip\ServiceLocator::content()->getLanguage($languageId);
                $locale = str_replace('-', '_', $language->getCode());
                $fmt = numfmt_create($locale, \NumberFormatter::CURRENCY);
                $formattedPrice = numfmt_format_currency($fmt, $price / 100, strtoupper($currency));
            } else {
                $formattedPrice = ($data['price'] / 100).' '.$data['currency'];
            }
        }

        return $formattedPrice;
    }

    /**
     * @param int $unixTimestamp
     * @param string $context
     * @param int $languageId
     * @return string
     */
    public static function formatDate($unixTimestamp, $context = null, $languageId = null)
    {
        $data = array(
            'timestamp' => $context,
            'context' => $context
        );

        $formattedDate = ipJob('ipFormatDate', $data);
        if ($formattedDate === NULL) {
            if (function_exists('datefmt_create') && function_exists('datefmt_format')) {
                if ($languageId === null) {
                    $languageId = ipContent()->getCurrentLanguage()->getId();
                }
                $language = \Ip\ServiceLocator::content()->getLanguage($languageId);
                if ($context == 'Ip-admin') {
                    $code = 'en';
                } else {
                    $code = $language->getCode();
                }
                $locale = str_replace('-', '_', $code);
                $fmt = datefmt_create($locale, \IntlDateFormatter::SHORT, \IntlDateFormatter::NONE);
                $formattedDate = datefmt_format($fmt, $unixTimestamp);
            } else {
                $formattedDate = date('Y-m-d', $unixTimestamp);
            }
        }

        return $formattedDate;
    }

    /**
     * @param int $unixTimestamp
     * @param string $context
     * @param int $languageId
     * @return string
     */
    public static function formatTime($unixTimestamp, $context = null, $languageId = null)
    {
        $data = array(
            'timestamp' => $context,
            'context' => $context
        );

        $formattedDate = ipJob('ipFormatTime', $data);
        if ($formattedDate === NULL) {
            if (function_exists('datefmt_create') && function_exists('datefmt_format')) {
                if ($languageId === null) {
                    $languageId = ipContent()->getCurrentLanguage()->getId();
                }
                $language = \Ip\ServiceLocator::content()->getLanguage($languageId);
                if ($context == 'Ip-admin') {
                    $code = 'en';
                } else {
                    $code = $language->getCode();
                }
                $locale = str_replace('-', '_', $code);
                $fmt = datefmt_create($locale, \IntlDateFormatter::NONE, \IntlDateFormatter::SHORT);
                $formattedDate = datefmt_format($fmt, $unixTimestamp);
            } else {
                $formattedDate = date('H:i', $unixTimestamp);
            }
        }

        return $formattedDate;
    }

    /**
     * @param int $unixTimestamp
     * @param string $context
     * @param int $languageId
     * @return string
     */
    public static function formatDateTime($unixTimestamp, $context = null, $languageId = null)
    {
        $data = array(
            'timestamp' => $context,
            'context' => $context
        );

        $formattedDate = ipJob('ipFormatDateTime', $data);
        if ($formattedDate === NULL) {
            if (function_exists('datefmt_create') && function_exists('datefmt_format')) {
                if ($languageId === null) {
                    $languageId = ipContent()->getCurrentLanguage()->getId();
                }
                $language = \Ip\ServiceLocator::content()->getLanguage($languageId);
                if ($context == 'Ip-admin') {
                    $code = 'en';
                } else {
                    $code = $language->getCode();
                }
                $locale = str_replace('-', '_', $code);
                $fmt = datefmt_create($locale, \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
                $formattedDate = datefmt_format($fmt, $unixTimestamp);
            } else {
                $formattedDate = date('Y-m-d H:i', $unixTimestamp);
            }
        }

        return $formattedDate;
    }

}
