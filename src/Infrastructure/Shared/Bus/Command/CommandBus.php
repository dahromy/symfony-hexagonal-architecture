<?php

namespace App\Infrastructure\Shared\Bus\Command;

use App\Infrastructure\Shared\Bus\MessageBusExceptionTrait;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

class CommandBus
{
    use MessageBusExceptionTrait;

    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messengerBusCommand)
    {
        $this->messageBus = $messengerBusCommand;
    }

    /**
     * @param CommandInterface $command
     *
     * @return void
     * @throws Throwable
     */
    public function handle(CommandInterface $command): void
    {
        try {
            $this->messageBus->dispatch($command);
        } catch (HandlerFailedException $e) {
            $this->throwException($e);
        }
    }
}
