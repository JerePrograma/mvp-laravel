<?php

namespace App\Http\Controllers\API;

/**
 * @OA\Info(
 *     title="MVP Laravel API",
 *     version="1.0.0",
 *     description="Documentación Swagger de la API CRUD de MVP Laravel"
 * )
 *
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Servidor local"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class ApiInfo
{
    // Clase vacía
}
