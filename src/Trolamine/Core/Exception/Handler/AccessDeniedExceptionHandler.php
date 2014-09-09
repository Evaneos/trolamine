<?php
namespace Trolamine\Core\Exception\Handler;

use \Pyrite\Exception\ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Trolamine\Core\Authentication\AnonymousAuthenticationToken;
use Trolamine\Layer\AuthenticationLayer;

class AccessDeniedExceptionHandler implements ExceptionHandler
{

    public function handleException(\Exception $exception, \Pyrite\Response\ResponseBag $responseBag)
    {
        $authentication = $responseBag->get(AuthenticationLayer::VAR_NAME);

        if ($authentication === null || $authentication instanceof AnonymousAuthenticationToken) {
            throw new HttpException(401, $exception->getMessage(), $exception);
        }

        throw new AccessDeniedHttpException($exception->getMessage(), $exception);
    }
}
