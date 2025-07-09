<?php
// database/migrations/xxxx_xx_xx_create_catalogs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('catalogs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Add catalog_id to quizzes table
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('catalog_id')->nullable()->constrained('catalogs')->nullOnDelete();
        });
    }

    public function down(): void {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['catalog_id']);
            $table->dropColumn('catalog_id');
        });

        Schema::dropIfExists('catalogs');
    }
};
