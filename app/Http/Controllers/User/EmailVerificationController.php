<?php

namespace App\Http\Controllers\User;

use App\Application\User\VerificationUser\Validation\EmailVerificationRequest;
use App\Application\User\VerificationUser\VerificationUserCommand;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class EmailVerificationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/user/email-verification",
     *     summary="Enviar código de verificación de email",
     *     description="Envía un código de verificación al email del usuario para activar su cuenta",
     *     operationId="post-email-verification",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 description="Email del usuario a verificar",
     *                 example="user@example.com"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Código de verificación enviado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Código de verificación enviado a tu email"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="expires_in", type="integer", example=900),
     *                 @OA\Property(property="code_length", type="integer", example=6)
     *             )
     *         )
     *     )
     * )
     */
    public function __invoke(EmailVerificationRequest $request): JsonResponse
    {
        $command =  new VerificationUserCommand(
            $request->input('email')
        );

        $result = $this->mediator->send($command);
        return $this->apiResponse->call($result);
    }
}
