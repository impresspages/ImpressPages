<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal;


class FormatHelper
{

    /**
     * @param $bytes
     * @param $context
     * @param $precision
     * @param null $languageCode
     * @return mixed|null|string
     */
    public static function formatBytes($bytes, $context, $precision, $languageCode = null)
    {
        $data = array(
            'bytes' => $bytes,
            'context' => $context,
            'precision' => $precision,
            'languageCode' => $languageCode
        );

        $formattedBytes = ipJob('ipFormatBytes', $data);

        if ($formattedBytes !== null) {
            return $formattedBytes;
        }


        $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        $decimal = array(
            'au',
            'bn',
            'bw',
            'ch',
            'cn',
            'do',
            'eg',
            'gt',
            'hk',
            'hn',
            'ie',
            'il',
            'in',
            'jp',
            'ke',
            'kp',
            'kr',
            'lb',
            'lk',
            'mn',
            'mo',
            'mt',
            'mx',
            'my',
            'ng',
            'ni',
            'np',
            'nz',
            'pa',
            'ph',
            'pk',
            'sg',
            'th',
            'tw',
            'tz',
            'ug',
            'uk',
            'us',
            'zw'
        );

        if ($languageCode === null) {
            $languageCode = ipContent()->getCurrentLanguage()->getCode();
        }

        for ($i = 0; $bytes >= 1024; $i++) {
            $bytes /= 1024;
            if ($i < 1) {
                $bytes = round($bytes, 0);
            } else {
                $bytes = round($bytes, $precision);
            }
        }

        if (in_array(strtolower($languageCode), $decimal)) {
            $formattedBytes = $bytes;
        } else {
            $formattedBytes = str_replace('.', ',', $bytes);
        }

        $formattedBytes .= ' ' . $sizes[$i];

        return $formattedBytes;
    }

    /**
     * @param int $price Price in cents
     * @param string $currency Three letter currency code
     * @param string $context
     * @param string $languageCode
     * @return string
     */
    public static function formatPrice($price, $currency, $context, $languageCode = null)
    {
        if ($languageCode === null) {
            $languageCode = ipContent()->getCurrentLanguage()->getCode();
        }

        $data = array(
            'price' => $price,
            'currency' => $currency,
            'context' => $context
        );

        $formattedPrice = ipJob('ipFormatPrice', $data);
        if ($formattedPrice === null) {
            if (function_exists('numfmt_create') && function_exists('numfmt_format_currency')) {
                $locale = str_replace('-', '_', $languageCode);
                $fmt = numfmt_create($locale, \NumberFormatter::CURRENCY);
                $formattedPrice = numfmt_format_currency($fmt, $price / 100, strtoupper($currency));
                if ($formattedPrice !== false && $formattedPrice != 'NaN') {
                    return $formattedPrice;
                }
            }

            $formattedPrice = round(($data['price'] / 100), 2) . ' ' . $data['currency'];
        }

        return $formattedPrice;
    }

    /**
     * @param int $unixTimestamp
     * @param string $context
     * @param int $languageCode
     * @return string
     */
    public static function formatDate($unixTimestamp, $context = null, $languageCode = null)
    {
        $data = array(
            'timestamp' => $unixTimestamp,
            'context' => $context
        );

        $formattedDate = ipJob('ipFormatDate', $data);
        if ($formattedDate === null) {
            if (function_exists('datefmt_create') && function_exists('datefmt_format')) {
                if ($languageCode === null) {
                    $languageCode = ipContent()->getCurrentLanguage()->getCode();
                }
                if ($context == 'Ip-admin') {
                    $code = 'en';
                } else {
                    $code = $languageCode;
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
     * @param int $languageCode
     * @return string
     */
    public static function formatTime($unixTimestamp, $context = null, $languageCode = null)
    {
        $data = array(
            'timestamp' => $unixTimestamp,
            'context' => $context
        );

        $formattedDate = ipJob('ipFormatTime', $data);
        if ($formattedDate === null) {
            if (function_exists('datefmt_create') && function_exists('datefmt_format')) {
                if ($languageCode === null) {
                    $languageCode = ipContent()->getCurrentLanguage()->getId();
                }
                if ($context == 'Ip-admin') {
                    $code = 'en';
                } else {
                    $code = $languageCode;
                }
                $locale = str_replace('-', '_', $code);
                $fmt = datefmt_create(
                    $locale,
                    \IntlDateFormatter::NONE,
                    \IntlDateFormatter::SHORT,
                    date_default_timezone_get()
                );
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
     * @param int $languageCode
     * @return string
     */
    public static function formatDateTime($unixTimestamp, $context = null, $languageCode = null)
    {
        $data = array(
            'timestamp' => $unixTimestamp,
            'context' => $context
        );

        $formattedDate = ipJob('ipFormatDateTime', $data);
        if ($formattedDate === null) {
            if (function_exists('datefmt_create') && function_exists('datefmt_format')) {
                if ($languageCode === null) {
                    if ($context == 'Ip-admin') {
                        $code = ipConfig()->adminLocale();
                    } else {
                        $languageCode = ipContent()->getCurrentLanguage()->getId();
                        $code = $languageCode;
                    }
                }
                $locale = str_replace('-', '_', $languageCode);
                $fmt = datefmt_create(
                    $locale,
                    \IntlDateFormatter::SHORT,
                    \IntlDateFormatter::SHORT,
                    date_default_timezone_get()
                );
                $formattedDate = datefmt_format($fmt, $unixTimestamp);
            } else {
                $formattedDate = date('Y-m-d H:i', $unixTimestamp);
            }
        }

        return $formattedDate;
    }

}
