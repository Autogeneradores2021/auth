<?php

namespace App\Http\Controllers\User;

use App\Application\User\CreateUser\CreateUserCommand;
use App\Application\User\CreateUser\Validator\CreateUserRequest;
use App\Http\Controllers\Controller;

class CreateUserController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/v1/user",
     *     summary="Crear nuevo usuario",
     *     tags={"User"},
     *     operationId="post-create-user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","last_name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Juan"),
     *             @OA\Property(property="last_name", type="string", example="Pérez"),
     *             @OA\Property(property="email", type="string", format="email", example="juan@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="MiPassword123!"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="MiPassword123!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuario creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuario creado exitosamente"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Los datos proporcionados no son válidos"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function __invoke(CreateUserRequest $request)
    {
        $command = new CreateUserCommand(
            name: $request->name,
            lastName: $request->last_name,
            email: $request->email,
            password: $request->password
        );

        $result = $this->mediator->send($command);
        return $this->apiResponse->call($result);
    }
}
