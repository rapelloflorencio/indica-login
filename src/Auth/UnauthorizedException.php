<?php
namespace App\Auth;
use Slim\Middleware\TokenAuthentication\UnauthorizedExceptionInterface;
class UnauthorizedException extends \Exception implements UnauthorizedExceptionInterface
{
}