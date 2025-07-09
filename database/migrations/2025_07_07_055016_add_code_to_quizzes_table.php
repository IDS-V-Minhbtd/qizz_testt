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
        $table->string('code')->nullable()->after('name');
 

    });
}

public function down()
{
    Schema::table('quizzes', function (Blueprint $table) {
        $table->dropColumn('code');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */

};
