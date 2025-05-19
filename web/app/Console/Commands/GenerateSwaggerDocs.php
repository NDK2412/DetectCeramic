<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateSwaggerDocs extends Command
{
    protected $signature = 'swagger:generate';
    protected $description = 'Generate Swagger documentation automatically';

    public function handle()
    {
        // Đường dẫn nơi Swagger sẽ tìm các file PHP
        $annotationsPath = base_path('app/Http/Controllers');
        $outputPath = base_path('public/docs/swagger.json');

        // Chạy lệnh của swagger-php để tạo tài liệu
        $command = "vendor/bin/openapi --output $outputPath $annotationsPath";

        $this->info("Generating Swagger documentation...");
        exec($command, $output, $resultCode);

        if ($resultCode === 0) {
            $this->info("Swagger documentation generated successfully at $outputPath");
        } else {
            $this->error("Failed to generate Swagger documentation. Please check your annotations.");
        }
    }
}
