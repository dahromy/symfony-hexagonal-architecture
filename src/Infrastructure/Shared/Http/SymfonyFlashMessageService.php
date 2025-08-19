<?php

namespace App\Infrastructure\Shared\Http;

use App\Domain\Shared\Http\FlashMessageServiceInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SymfonyFlashMessageService implements FlashMessageServiceInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function add(string $type, string $message): void
    {
        $session = $this->requestStack->getSession();
        if ($session) {
            $session->getFlashBag()->add($type, $message);
        }
    }
}