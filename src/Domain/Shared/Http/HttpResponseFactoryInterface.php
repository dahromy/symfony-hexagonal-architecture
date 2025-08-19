<?php

namespace App\Domain\Shared\Http;

interface HttpResponseFactoryInterface
{
    /**
     * Create an HTTP response
     *
     * @param mixed $data
     * @param int $statusCode
     * @param array $headers
     * @return HttpResponseInterface
     */
    public function create($data, int $statusCode = 200, array $headers = []): HttpResponseInterface;

    /**
     * Create a redirect response
     *
     * @param string $url
     * @param int $statusCode
     * @return HttpResponseInterface
     */
    public function redirect(string $url, int $statusCode = 302): HttpResponseInterface;

    /**
     * Create a rendered template response
     *
     * @param string $template
     * @param array $parameters
     * @param int $statusCode
     * @return HttpResponseInterface
     */
    public function render(string $template, array $parameters = [], int $statusCode = 200): HttpResponseInterface;
}