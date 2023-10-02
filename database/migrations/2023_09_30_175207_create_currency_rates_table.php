<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->string('currency_id', 10);
            $table->date('date');
            $table->decimal('rate', 10, 4);
            $table->string('base_currency_code')->default('RUR');

            $table->primary(['currency_id', 'date']);
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('currency_rates');
    }
};
