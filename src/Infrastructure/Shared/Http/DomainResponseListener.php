<?php

namespace App\Infrastructure\Shared\Http;

use App\Domain\Shared\Http\HttpResponseInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class DomainResponseListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => 'onKernelView',
        ];
    }

    public function onKernelView(ViewEvent $event): void
    {
        $result = $event->getControllerResult();

        if ($result instanceof HttpResponseInterface) {
            // If our controller returns a domain HttpResponseInterface,
            // convert it back to a Symfony Response
            if ($result instanceof SymfonyHttpResponse) {
                $event->setResponse($result->getSymfonyResponse());
            }
        }
    }
}