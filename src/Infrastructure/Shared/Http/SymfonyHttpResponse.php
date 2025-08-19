<?php

namespace App\Infrastructure\Shared\Http;

use App\Domain\Shared\Http\HttpResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class SymfonyHttpResponse implements HttpResponseInterface
{
    private Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function getHeaders(): array
    {
        return $this->response->headers->all();
    }

    public function getContent(): string
    {
        return $this->response->getContent() ?: '';
    }

    /**
     * Get the underlying Symfony Response object
     * This method is used by the Symfony framework to send the response
     *
     * @return Response
     */
    public function getSymfonyResponse(): Response
    {
        return $this->response;
    }
}