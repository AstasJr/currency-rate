<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->string('code')->unique();
            $table->string('name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('currencies');
    }
};
