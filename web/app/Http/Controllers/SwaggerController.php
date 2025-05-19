<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SwaggerController extends Controller
{
    // Đường dẫn file JSON để lưu trữ
    private $filePath;

    public function __construct()
    {
        $this->filePath = storage_path('swagger_data.json');
    }

    // Lưu dữ liệu từ SwaggerApi.php vào file JSON
    public function saveToJson()
    {
        try {
            // Lấy dữ liệu từ SwaggerApi.php
            $swaggerData = require base_path('app/Http/Swagger/SwaggerApi.php');

            // Lưu dữ liệu vào file JSON
            file_put_contents($this->filePath, json_encode($swaggerData, JSON_PRETTY_PRINT));

            return response()->json(['message' => 'Dữ liệu đã được lưu vào swagger_data.json'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi khi lưu dữ liệu: ' . $e->getMessage()], 500);
        }
    }

    // Đọc lại dữ liệu từ file JSON
    public function readFromJson()
    {
        try {
            // Kiểm tra file có tồn tại không
            if (!file_exists($this->filePath)) {
                return response()->json(['error' => 'File swagger_data.json không tồn tại'], 404);
            }

            // Đọc nội dung file JSON
            $data = json_decode(file_get_contents($this->filePath), true);

            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi khi đọc dữ liệu: ' . $e->getMessage()], 500);
        }
    }

    // Kiểm tra file JSON có tồn tại không
    public function checkFileExists()
    {
        try {
            if (file_exists($this->filePath)) {
                return response()->json(['message' => 'File swagger_data.json tồn tại'], 200);
            } else {
                return response()->json(['message' => 'File swagger_data.json không tồn tại'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi khi kiểm tra file: ' . $e->getMessage()], 500);
        }
    }
}
