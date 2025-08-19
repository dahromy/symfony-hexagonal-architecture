<?php

namespace App\Domain\Shared\Http;

interface FlashMessageServiceInterface
{
    /**
     * Add a flash message
     *
     * @param string $type
     * @param string $message
     * @return void
     */
    public function add(string $type, string $message): void;
}