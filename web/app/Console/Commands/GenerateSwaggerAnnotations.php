<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use ReflectionMethod;

class GenerateSwaggerAnnotations extends Command
{
    protected $signature = 'swagger:generate-annotations';
    protected $description = 'Tự động tạo Swagger annotations từ các routes';

    public function handle()
    {
        $routes = Route::getRoutes();

        foreach ($routes as $route) {
            $uri = $route->uri();
            $methods = implode(',', $route->methods());
            $action = $route->getActionName();

            if ($action === 'Closure') {
                $this->warn("Route closure detected: {$uri}");
                continue;
            }

            [$controller, $method] = explode('@', $action);
            $this->addSwaggerAnnotation($controller, $method, $uri, $methods);
        }

        $this->info('Swagger annotations generated successfully.');
    }

    private function addSwaggerAnnotation($controller, $method, $uri, $methods)
    {
        $controllerFile = (new \ReflectionClass($controller))->getFileName();
        $reflection = new ReflectionMethod($controller, $method);

        $annotation = <<<ANNOTATION
    /**
     * @OA\\Operation(
     *     path="/$uri",
     *     summary="Generated summary for $uri",
     *     tags={"Auto-Generated"},
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=400, description="Bad Request")
     * )
     */
ANNOTATION;

        $fileContent = file($controllerFile);
        $methodLine = $reflection->getStartLine() - 1;

        if (strpos($fileContent[$methodLine - 1], '@OA\\Operation') === false) {
            array_splice($fileContent, $methodLine - 1, 0, $annotation . PHP_EOL);
            file_put_contents($controllerFile, implode('', $fileContent));
            $this->info("Annotation added for $controller@$method");
        } else {
            $this->warn("Annotation already exists for $controller@$method");
        }
    }
}
