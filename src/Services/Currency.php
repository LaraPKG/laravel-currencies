<?php

declare(strict_types=1);

namespace LaraPKG\LaravelCurrencies\Services;

use LaraPKG\LaravelCurrencies\Models\Currency as CurrencyModel;
use LaraPKG\LaravelCurrencies\Contracts\CurrencyFormatterInterface;
use LaraPKG\LaravelCurrencies\Models\Price;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Converts and renders currency values
 *
 * @package App\Services
 */
class Currency
{
    /**
     * The service configuration
     *
     * @var array
     */
    protected array $config = [];

    /**
     * The users currency choice
     *
     * @var string
     */
    protected ?string $userCurrency = null;

    /**
     * The collection of currencies
     *
     * @var Collection
     */
    protected Collection $currencies;

    /**
     * The currency formatter instance
     *
     * @var CurrencyFormatterInterface
     */
    protected ?CurrencyFormatterInterface $formatter = null;

    /**
     * Create a new Currency instance
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        $this->load();
    }

    /**
     * Converts an amount to a currency from another one
     * If from is not specified the default currency is used
     * If to is not specified the users currency is used
     *
     * @param $amount
     * @param null $to
     * @param null $from
     * @param bool $format
     *
     * @return string
     */
    public function convert($amount, $to = null, $from = null, $format = true): string
    {
        // Get the currencies
        $from ??= $this->config('default');
        $to ??= $this->getUserCurrency();

        $fromRate = $this->getExchangeRate($from);
        $toRate = $this->getExchangeRate($to);

        $value = $from === $to
            ? $amount
            : ($amount * $toRate) / $fromRate;

        return $format
            ? $this->format($value, $to)
            : (string)round($value, 2, PHP_ROUND_HALF_UP);
    }

    /**
     * Formats a value into the desired currency
     *
     * @param $value
     * @param null $code
     * @param bool $includeSymbol
     *
     * @return string
     */
    public function format($value, $code = null, $includeSymbol = true): string
    {
        $code ??= $this->config('default');

        // Remove unnecessary characters
        $value = preg_replace('/[\s\',!]/', '', $value);

        return $this->getFormatter()->format((float)$value, $code);
    }

    /**
     * Converts a price model into the active currency
     *
     * @param Price $price
     *
     * @return Price
     */
    public function model(Price $price): Price
    {
        $code = $this->getUserCurrency();
        $currency = $this->getCurrency($code);

        if ($price->currency_code === $code) {
            return $price;
        }

        return Price::make([
            'currency_id' => $currency->id,
            'value' => $price->convert($code, false),
        ]);
    }

    /**
     * Updates a currencies exchange rate
     *
     * @param $currency
     * @param $fields
     *
     * @return void
     */
    public function update($currency, $fields): void
    {
        CurrencyModel::where('code', $currency)->update($fields);
    }

    /**
     * Sets the users currency
     *
     * @param $code
     */
    public function setUserCurrency($code): void
    {
        $this->userCurrency = strtoupper($code);
    }

    /**
     * Gets the users currency
     *
     * @return string
     */
    public function getUserCurrency(): string
    {
        return $this->userCurrency ?: $this->config('default');
    }

    /**
     * Returns whether or not a currency exists
     *
     * @param $code
     *
     * @return bool
     */
    public function hasCurrency($code): bool
    {
        return $this->getCurrency($code) !== null;
    }

    /**
     * Returns whether a currency with the supplied code is active
     *
     * @param $code
     *
     * @return bool
     */
    public function isActive($code): bool
    {
        return $code && $this->getCurrency($code);
    }

    /**
     * Gets the exchange rate for the supplied currency
     *
     * @param string $currency
     *
     * @return float
     */
    public function getExchangeRate(string $currency): float
    {
        return $this->getCurrency($currency)->exchange_rate;
    }

    /**
     * Gets a currency by currency code
     * If a code is blank, the users currency is used
     *
     * @param string $code
     *
     * @return CurrencyModel|null
     */
    public function getCurrency($code = null): ?CurrencyModel
    {
        $code ??= $this->getUserCurrency();

        return $this->getCurrencies()
            ->where('code', $code)
            ->last();
    }

    /**
     * Gets a currency by its id
     *
     * @param int $id
     *
     * @return CurrencyModel|null
     */
    public function getCurrencyById(int $id): ?CurrencyModel
    {
        return $this->getCurrencies()
            ->where('id', $id)
            ->first();
    }

    /**
     * Gets all currencies
     *
     * @return Collection
     */
    public function getCurrencies(): Collection
    {
        return $this->currencies;
    }

    /**
     * Gets the active currencies for the domain
     *
     * @return Collection
     */
    public function getActiveCurrencies(): Collection
    {
        // @todo return the currencies that are active for the current domain

        return $this->getCurrencies();
    }

    /**
     * Gets the currency formatter driver
     *
     * @return CurrencyFormatterInterface
     */
    public function getFormatter(): CurrencyFormatterInterface
    {
        if ($this->formatter === null && $this->config('formatter') !== null) {
            $config = $this->config('formatters.' . $this->config('formatter'), []);

            $class = Arr::pull($config, 'class');

            $this->formatter = new $class(array_filter($config));
        }

        return $this->formatter;
    }

    /**
     * Gets an item from the configuration array
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function config($key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * Loads the currencies and exchange rates into the service
     *
     * @return void
     */
    protected function load(): void
    {
        $this->currencies = CurrencyModel::all();
    }
}
