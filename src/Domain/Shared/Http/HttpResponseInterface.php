<?php

namespace App\Domain\Shared\Http;

interface HttpResponseInterface
{
    /**
     * Get the response status code
     *
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * Get response headers
     *
     * @return array
     */
    public function getHeaders(): array;

    /**
     * Get response content
     *
     * @return string
     */
    public function getContent(): string;
}