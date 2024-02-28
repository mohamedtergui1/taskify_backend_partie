<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class Controller
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @OA\Info(
     *    title="Swagger with Laravel",
     *    version="1.0.0",
     * )
     */

    /**
     * @OA\SecurityScheme(
     *     type="http",
     *     securityScheme="bearerAuth",
     *     scheme="bearer",
     *     bearerFormat="JWT"
     * )
     */

    /**
     * A sample endpoint description.
     *
     * @return \Illuminate\Http\Response
     *
     * @OA\Get(
     *     path="/api/sample",
     *     tags={"Sample"},
     *     summary="Sample endpoint summary",
     *     description="Sample endpoint description",
     *     security={ {"bearerAuth": {} } },
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     )
     * )
     */
 
}
