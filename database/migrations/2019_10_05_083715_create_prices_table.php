<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('currency_id');

            $table->morphs('priceable');

            // The type of price (base price, sale, rrp or tax)
            // Tax allows us to store a base product level tax if we ever need it
            $table->enum('type', ['price', 'sale', 'rrp', 'tax'])
                ->default('price');

            $table->decimal('value', 15, 2);

            $table->timestampsTz();

            // Add a unique so we only have one price of a type for an item
            // We don't need the currency here as we only store the price in
            // one currency and then convert it when we need to.
            $table->unique(['priceable_type', 'priceable_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
}
