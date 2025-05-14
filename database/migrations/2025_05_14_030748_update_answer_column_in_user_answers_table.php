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
        // Thêm giá trị mặc định là null hoặc chuỗi rỗng cho cột 'answer'
        $table->string('answer')->nullable()->change();  // Nếu bạn muốn cho phép giá trị NULL
        // Hoặc nếu bạn muốn cột 'answer' có giá trị mặc định là chuỗi rỗng
        // $table->string('answer')->default('')->change();
    });
}

public function down()
{
    Schema::table('user_answers', function (Blueprint $table) {
        // Khôi phục lại trạng thái trước khi sửa đổi (nếu cần)
        $table->string('answer')->nullable(false)->change();  // Đảm bảo cột 'answer' không được phép là NULL
        // Hoặc bạn có thể xóa giá trị mặc định nếu cần
        // $table->string('answer')->default('')->change();
    });
}
};
