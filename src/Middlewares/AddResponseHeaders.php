<?php

declare(strict_types=1);

namespace Kartalit\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AddResponseHeaders
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);

        // Add Content-Length header if not already added
        $size = $response->getBody()->getSize();
        if ($size !== null && !$response->hasHeader('Content-Length')) {
            $response = $response->withHeader("Content-Length", "" . $size);
        }

        return $response
            ->withHeader("Content-Language", "ca")
            ->withHeader("Content-Type", "application/json")
            ->withHeader("Date", gmdate("D, d M Y H:i:s T"));
    }
}
