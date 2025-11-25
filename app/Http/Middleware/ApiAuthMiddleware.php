<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        try {
            // Verificar si existe el token en el header
            $token = $request->bearerToken();

            if (!$token) {
                return $this->unauthorizedResponse('Token de autenticación requerido');
            }

            // Validar y autenticar el token
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return $this->unauthorizedResponse('Usuario no encontrado');
            }

            // Agregar el usuario al request para uso posterior
            $request->merge(['authenticated_user' => $user]);

            return $next($request);
        } catch (TokenExpiredException $e) {
            return $this->unauthorizedResponse('Token expirado', 'TOKEN_EXPIRED');
        } catch (TokenInvalidException $e) {
            return $this->unauthorizedResponse('Token inválido', 'TOKEN_INVALID');
        } catch (JWTException $e) {
            return $this->unauthorizedResponse('Error de autenticación', 'AUTH_ERROR');
        } catch (\Exception $e) {
            return $this->unauthorizedResponse('Error interno de autenticación', 'INTERNAL_ERROR');
        }
    }

    /**
     * Respuesta de error de autenticación estandarizada
     */
    private function unauthorizedResponse(string $message, string $errorCode = 'UNAUTHENTICATED'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'error_code' => $errorCode,
            'data' => null
        ], 401);
    }
}
