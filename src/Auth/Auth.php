<?php
namespace App\Auth;
use App\Models\Entity\Usuario;
class Auth
{
    /**
     * It's only a validation example!
     * You should search user (on your database) by authorization token
     */
    public function validateToken($token)
    {
        if ($token != '$2y$10$AJi4JsNTXkoJar.RvC0r9eEXnvPtUKA74h1UqPAujPac8RChKmvb6') {
            /**
             * The throwable class must implement UnauthorizedExceptionInterface
             */
            throw new UnauthorizedException('Invalid Token');
        }

        
        return $token;
    }
}