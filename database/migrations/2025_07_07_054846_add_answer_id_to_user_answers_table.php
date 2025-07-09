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
    Schema::table('user_answers', function (Blueprint $table) {
        $table->unsignedBigInteger('answer_id')->nullable()->after('question_id');
    });
}

public function down()
{
    Schema::table('user_answers', function (Blueprint $table) {
        $table->dropColumn('answer_id');
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */

};
