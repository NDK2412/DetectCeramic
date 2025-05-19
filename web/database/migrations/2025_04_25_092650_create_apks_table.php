<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateApksTable extends Migration
{
    public function up()
    {
        Schema::create('apks', function (Blueprint $table) {
            $table->id();
            $table->string('version'); // Phiên bản APK (VD: 1.0.0)
            $table->string('file_name'); // Tên tệp APK
            $table->string('file_path'); // Đường dẫn lưu trữ
            $table->timestamps(); // created_at và updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('apks');
    }
}