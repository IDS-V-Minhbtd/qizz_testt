<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
{
    Schema::table('quizzes', function (Blueprint $table) {
        $table->string('catalog')->nullable()->after('description'); // hoặc after('quizz_code') tùy bạn
    });
}

public function down()
{
    Schema::table('quizzes', function (Blueprint $table) {
        $table->dropColumn('catalog');
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */

};
