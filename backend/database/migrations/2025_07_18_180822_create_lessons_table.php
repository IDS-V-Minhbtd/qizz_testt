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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id(); // Khóa chính, tự động tăng
            $table->unsignedBigInteger('course_id'); // Khóa ngoại, liên kết với bảng courses
            $table->string('title'); // Tiêu đề bài học
            $table->text('content'); // Nội dung bài học
            $table->string('resource')->nullable(); // File upload tài liệu (nullable)
            $table->text('assignment')->nullable(); // Bài tập cho mỗi lesson (nullable)
            $table->string('youtube_url')->nullable(); // URL video Youtube (nullable)
            $table->integer('order_index')->default(0); // Thứ tự bài học trong khóa học
            $table->timestamps(); // created_at, updated_at

            // Ràng buộc khóa ngoại
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lessons');
    }
}; 