<?php

declare(strict_types=1);

namespace LaraPKG\LaravelCurrencies\Database\Seeds;

use Illuminate\Database\Seeder;
use LaraPKG\LaravelCurrencies\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = config('intl.currencies');

        foreach ($currencies as $currencyArray) {
            Currency::updateOrCreate([
               'country' => $currencyArray[0],
               'currency' => $currencyArray[1],
               'code' => $currencyArray[2],
               'symbol' => $currencyArray[3],
            ]);
        }
    }
}
