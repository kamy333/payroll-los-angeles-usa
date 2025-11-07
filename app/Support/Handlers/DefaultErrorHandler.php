<?php

declare(strict_types=1);

namespace App\Support\Handlers;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpException;
use Slim\Handlers\ErrorHandler;
use Slim\Interfaces\CallableResolverInterface;

class DefaultErrorHandler extends ErrorHandler
{
    public function __construct(
        CallableResolverInterface $callableResolver,
        ResponseFactoryInterface $responseFactory,
        protected bool $displayErrorDetails = false
    ) {
        parent::__construct($callableResolver, $responseFactory);
    }

    protected function respond(): ResponseInterface
    {
        if ($this->displayErrorDetails) {
            return parent::respond();
        }

        $exception = $this->exception;
        $statusCode = $exception instanceof HttpException ? $exception->getCode() : 500;
        $response = $this->responseFactory->createResponse($statusCode > 0 ? $statusCode : 500);

        $payload = [
            'error' => [
                'message' => 'Unexpected server error. Please try again later.',
            ],
        ];

        $response->getBody()->write(json_encode($payload, JSON_THROW_ON_ERROR));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
