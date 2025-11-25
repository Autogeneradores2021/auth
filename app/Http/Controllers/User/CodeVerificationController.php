<?php

namespace App\Http\Controllers\User;

use App\Application\User\VerificationCode\Validation\VerificationCodeRequest;
use App\Application\User\VerificationCode\VerificationCodeCommand;
use App\Http\Controllers\Controller;


class CodeVerificationController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/v1/user/verification/code",
     *     tags={"User"},
     *     summary="Validar el código de verificación y obtener el token",
     *     description="Valida un código de verificación de email. Devuelve un token de un solo uso si tiene éxito.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","code"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="code", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Validation successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Código verificado correctamente"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="b2a1f3..."),
     *                 @OA\Property(property="token_expires_at", type="string", format="date-time", example="2025-10-22T04:00:00Z"),
     *                 @OA\Property(property="identifier", type="string", example="user@example.com"),
     *                 @OA\Property(property="type", type="string", example="email_verification")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Código incorrecto"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
     *
     * @param VerificationCodeRequest $request
     */
    public function __invoke(VerificationCodeRequest $request)
    {
        $command = new VerificationCodeCommand(
            email: $request->input('email'),
            code: $request->input('code')
        );

        $result = $this->mediator->send($command);
        return $this->apiResponse->call($result);
    }
}
