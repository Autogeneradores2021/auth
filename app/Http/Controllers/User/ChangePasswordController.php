<?php

namespace App\Http\Controllers\User;

use App\Application\User\ChangePassword\ChangePasswordCommand;
use App\Application\User\ChangePassword\Validator\ChangePasswordRequest;
use App\Http\Controllers\Controller;
use BMCLibrary\Utils\Result;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 *  @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Ingrese el token JWT obtenido del endpoint de login."
 * )
 */
class ChangePasswordController extends Controller
{

    /**
     * @OA\Put(
     *     path="/api/v1/user/change-password",
     *     summary="Cambiar contraseña",
     *     description="Permite al usuario autenticado cambiar su contraseña",
     *     operationId="put-change-password",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos para cambio de contraseña",
     *         @OA\JsonContent(
     *             required={"old_password","new_password","new_password_confirmation"},
     *             @OA\Property(
     *                 property="old_password",
     *                 type="string",
     *                 format="password",
     *                 description="Contraseña actual del usuario",
     *                 example="MiPasswordActual123!"
     *             ),
     *             @OA\Property(
     *                 property="new_password",
     *                 type="string",
     *                 format="password",
     *                 description="Nueva contraseña (mín. 8 caracteres, debe incluir mayúsculas, minúsculas, números y símbolos)",
     *                 example="MiNuevaPassword456@"
     *             ),
     *             @OA\Property(
     *                 property="new_password_confirmation",
     *                 type="string",
     *                 format="password",
     *                 description="Confirmación de la nueva contraseña",
     *                 example="MiNuevaPassword456@"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contraseña cambiada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Contraseña actualizada exitosamente"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Contraseña actual incorrecta o no autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Contraseña actual incorrecta")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Los datos proporcionados no son válidos"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="new_password",
     *                     type="array",
     *                     @OA\Items(type="string", example="La nueva contraseña debe tener al menos 8 caracteres.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function __invoke(ChangePasswordRequest $request): JsonResponse
    {

        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return $this->apiResponse->call(
                Result::fail('Token JWT inválido o expirado', null, 401)
            );
        }

        $userId = (string) $user->id;

        $command = new ChangePasswordCommand(
            userId: $userId,
            oldPassword: $request->input('old_password'),
            newPassword: $request->input('new_password'),
        );

        $result = $this->mediator->send($command);
        return $this->apiResponse->call($result);
    }
}
