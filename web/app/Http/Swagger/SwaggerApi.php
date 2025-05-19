<?php

namespace App\Http\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="Ceramic Detection API",
 *         version="1.0.0",
 *         description="API for Ceramic Detection System"
 *     ),
 *     @OA\Server(
 *         url=L5_SWAGGER_CONST_HOST,
 *         description="API Server"
 *     ),
 *     @OA\SecurityScheme(
 *         securityScheme="bearerAuth",
 *         type="http",
 *         scheme="bearer",
 *         bearerFormat="JWT"
 *     )
 * )
 */
class SwaggerApi
{
    // Các method API sẽ được định nghĩa ở các controller tương ứng
}