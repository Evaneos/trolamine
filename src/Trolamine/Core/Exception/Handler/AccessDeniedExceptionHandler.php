<?php
namespace Trolamine\Core\Exception\Handler;

use \Pyrite\Exception\ExceptionHandler;

class AccessDeniedExceptionHandler implements ExceptionHandler {

    public function handleException(\Exception $exception, \Pyrite\Response\ResponseBag $responseBag)
    {
        $responseBag->setResultCode(403);

        return $responseBag;
    }
}