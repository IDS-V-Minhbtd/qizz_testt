<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnswerTypeToQuestionsTable extends Migration
{
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('answer_type', ['multiple_choice', 'text_input','true_flase'])->default('multiple_choice');
            // $table->string('type')->nullable(); // Nếu thực sự cần cột type
        });
    }

    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('answer_type');
            // $table->dropColumn('type');
        });
    }
}
