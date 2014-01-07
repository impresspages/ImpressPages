<?php
/**
 * @package     Library

 *
 */

namespace Ip\Internal;




class FormatHelper
{

    /**
     * @param int $price in cents
     * @param string $currency three letter currency code
     * @param string $context
     * @param int $languageId
     * @return string
     */
    public static function formatPrice($price, $currency, $context, $languageId = null)
    {
        if ($languageId === null) {
            $languageId = ipContent()->getCurrentLanguage()->getId();
        }

        $data = array (
            'price' => $price,
            'currency' => $currency,
            'context' => $context
        );
        $formattedPrice = ipDispatcher()->job('Ip.formatCurrency', $data);
        if ($formattedPrice === NULL) {
            if (function_exists('numfmt_create') && function_exists('numfmt_format_currency')) {
                $language = \Ip\ServiceLocator::content()->getLanguage($languageId);
                $locale = str_replace('-', '_', $language->getCode());
                $fmt = numfmt_create( $locale, \NumberFormatter::CURRENCY );

                $formattedPrice = numfmt_format_currency($fmt, $price / 100, strtoupper($currency));
            } else {
                $formattedPrice = ($data['price']/100).' '.$data['currency'];
            }
        }
        return $formattedPrice;
    }


    public static function formatDate($unixTimestamp, $context = null, $languageId = null)
    {
        $data = array (
            'timestamp' => $context,
            'context' => $context
        );
        $formattedDate = ipDispatcher()->job('Ip.formatDate', $data);
        if ($formattedDate === NULL) {
            if (function_exists('datefmt_create') && function_exists('datefmt_format')) {
                if ($languageId === null) {
                    $languageId = ipContent()->getCurrentLanguage()->getId();
                }
                $language = \Ip\ServiceLocator::content()->getLanguage($languageId);
                if ($context == 'ipAdmin') {
                    $code = 'en';
                } else {
                    $code = $language->getCode();
                }
                $locale = str_replace('-', '_', $code);
                $fmt = datefmt_create( $locale, \IntlDateFormatter::SHORT, \IntlDateFormatter::NONE );
                $formattedDate = datefmt_format($fmt, $unixTimestamp);
            } else {
                $formattedDate = date('Y-m-d', $unixTimestamp);
            }
        }
        return $formattedDate;
    }

    public static function formatTime($unixTimestamp, $context = null, $languageId = null)
    {
        $data = array (
            'timestamp' => $context,
            'context' => $context
        );
        $formattedDate = ipDispatcher()->job('Ip.formatTime', $data);
        if ($formattedDate === NULL) {
            if (function_exists('datefmt_create') && function_exists('datefmt_format')) {
                if ($languageId === null) {
                    $languageId = ipContent()->getCurrentLanguage()->getId();
                }
                $language = \Ip\ServiceLocator::content()->getLanguage($languageId);
                if ($context == 'ipAdmin') {
                    $code = 'en';
                } else {
                    $code = $language->getCode();
                }
                $locale = str_replace('-', '_', $code);
                $fmt = datefmt_create( $locale, \IntlDateFormatter::NONE, \IntlDateFormatter::SHORT );
                $formattedDate = datefmt_format($fmt, $unixTimestamp);
            } else {
                $formattedDate = date('H:i', $unixTimestamp);
            }
        }
        return $formattedDate;
    }

    public static function formatDateTime($unixTimestamp, $context = null, $languageId = null)
    {
        $data = array (
            'timestamp' => $context,
            'context' => $context
        );
        $formattedDate = ipDispatcher()->job('Ip.formatDateTime', $data);
        if ($formattedDate === NULL) {
            if (function_exists('datefmt_create') && function_exists('datefmt_format')) {
                if ($languageId === null) {
                    $languageId = ipContent()->getCurrentLanguage()->getId();
                }
                $language = \Ip\ServiceLocator::content()->getLanguage($languageId);
                if ($context == 'ipAdmin') {
                    $code = 'en';
                } else {
                    $code = $language->getCode();
                }
                $locale = str_replace('-', '_', $code);
                $fmt = datefmt_create( $locale, \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT );
                $formattedDate = datefmt_format($fmt, $unixTimestamp);
            } else {
                $formattedDate = date('Y-m-d H:i', $unixTimestamp);
            }
        }
        return $formattedDate;
    }


}