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
        $table->foreignId('catalog_id')->nullable()->constrained()->nullOnDelete();
        // Hoặc: $table->unsignedBigInteger('catalog_id')->nullable();
    });
}

public function down()
{
    Schema::table('quizzes', function (Blueprint $table) {
        $table->dropForeign(['catalog_id']);
        $table->dropColumn('catalog_id');
    });
}

};
