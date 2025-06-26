<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API Documentation",
 *     description="Dokumentasi API",
 *
 *     @OA\Contact(
 *         email="arbyusman24@gmail.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityRequirement(
 *     name="bearerAuth"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Masukkan token dengan format **Bearer &lt;token&gt;**"
 * )
 */
class SwaggerController extends Controller
{
    //
}
