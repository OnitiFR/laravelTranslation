<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->string('uuid')->unique()->primary();
            $table->string('class')->index();
            $table->string('class_uuid')->index();
            $table->string('champ')->index();
            $table->string('lang')->index();
            $table->string('traduction');

            $table->unique(['class','class_uuid','champ','lang']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translations');
    }
}
