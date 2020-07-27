<?php

declare(strict_types=1);

namespace LaraPKG\LaravelCurrencies\Contracts;

/**
 * CurrencyFormatterInterface
 *
 * @package App\Contracts
 */
interface CurrencyFormatterInterface
{
    /**
     * Formats the value into the desired currency
     *
     * @param float $value
     * @param string $code
     *
     * @return string
     */
    public function format($value, $code = null): string;
}
