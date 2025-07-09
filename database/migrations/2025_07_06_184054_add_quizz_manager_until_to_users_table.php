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
// database/migrations/xxxx_add_quizz_manager_until_to_users_table.php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->timestamp('quizz_manager_until')->nullable()->after('role');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('quizz_manager_until');
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */

};
