<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnswerTypeToQuestionsTable extends Migration
{
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('answer_type', ['multiple_choice', 'text_input'])->default('multiple_choice'); // Thêm trường answer_type
        });
    }

    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('answer_type'); // Xóa trường answer_type khi rollback
        });
    }
}
