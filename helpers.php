<?php

declare(strict_types=1);

use LaraPKG\LaravelCurrencies\Models\Price;

if (!function_exists('currency')) {
    /**
     * Converts a supplied amount into currency
     *
     * @param float $amount
     * @param string $to
     * @param string $from
     * @param bool $format
     *
     * @return string
     */
    function currency($amount, $to = null, $from = null, $format = true)
    {
        return app('currency')->convert($amount, $to, $from, $format);
    }
}

if (!function_exists('currency_format')) {
    /**
     * Formats a supplied number in the desired currency
     *
     * @param float $amount
     * @param string $currency
     * @param bool $includeSymbol
     *
     * @return string
     */
    function currency_format($amount, $currency, $includeSymbol = true): string
    {
        return app('currency')->format($amount, $currency, $includeSymbol);
    }
}

if (!function_exists('currency_price')) {
    /**
     * Converts a price model into the current active currency
     *
     * @param Price $price
     *
     * @return Price
     */
    function currency_price(Price $price): Price
    {
        return app('currency')->model($price);
    }
}