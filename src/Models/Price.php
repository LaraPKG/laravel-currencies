<?php

namespace LaraPKG\LaravelCurrencies\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Price model
 *
 * @package App\Models
 *
 * @property Currency $currency
 * @property int $currency_id
 * @property string $currency_code
 * @property float $value
 */
class Price extends Model
{
    /**
     * Fillable model values
     *
     * @var array
     */
    public $fillable = [
        'currency_id',
        'value',
        'type',
    ];

    /**
     * Casted attributes
     *
     * @var array
     */
    public $casts = [
        'value' => 'float',
    ];

    /**
     * Converts a price into a different currency
     *
     * @param string $currency
     * @param bool $format
     *
     * @return string
     */
    public function convert($currency, $format = true): string
    {
        return currency($this->value, $currency, $this->currency_code, $format);
    }

    /**
     * Returns the formatted price value
     *
     * @return string
     */
    public function format()
    {
        return currency_format($this->value, $this->currency_code);
    }

    // No need for a belongs to relationship here, we load
    // all currencies in through a service
//    public function currency(): BelongsTo
//    {
//        return $this->belongsTo(Currency::class);
//    }

    /**
     * Retrieves the currency that the price is stored in
     *
     * @return Currency
     */
    public function getCurrencyAttribute(): Currency
    {
        return app('currency')->getCurrencyById($this->currency_id);
    }

    /**
     * Returns a currency code from the assigned currency
     *
     * @return string|null
     */
    public function getCurrencyCodeAttribute(): ?string
    {
        return $this->currency
            ? $this->currency->code
            : null;
    }

    /**
     * Retrieves a formatted price string
     *
     * @return string
     */
    public function getFormattedAttribute(): string
    {
        return $this->format();
    }

    /**
     * Get all of the owning priceable models
     *
     * @return MorphTo
     */
    public function priceable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Makes an empty price in the default currency
     *
     * @return mixed
     */
    public static function makeEmpty()
    {
        $currency = app('currency')->getCurrency();

        return self::make(['currency_id' => $currency->id, 'value' => 0]);
    }

    /**
     * Returns the array equivalent of the model.
     * Useful for debug, as it allows you to see the formatted price
     * and the currency it is stored in.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'currency_id' => $this->currency_id,
            'currency_code' => $this->currency_code,
            'value' => round($this->value, 2),
            'formatted' => $this->formatted,
        ];
    }
}
