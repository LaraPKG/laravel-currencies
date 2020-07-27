<?php

declare(strict_types=1);

/**
 * Added for model properties (cannot type hint fillable, etc)
 *
 * @noinspection PhpMissingFieldTypeInspection
 */

namespace LaraPKG\LaravelCurrencies\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Currency
 * @package LaraPKG\LaravelCurrencies\Models
 *
 * @property int $id
 * @property string $country
 * @property string $currency
 * @property string $code
 * @property string $symbol
 * @property bool $currency_on_right
 * @property float $exchange_rate
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 */
class Currency extends Model
{
}
