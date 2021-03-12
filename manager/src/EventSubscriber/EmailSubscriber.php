<?php

namespace App\EventSubscriber;

use RuntimeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Email;

class EmailSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $from;

    /**
     * EmailSubscriber constructor.
     *
     * @param string $from
     */
    public function __construct(string $from)
    {
        $this->from = $from;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            MessageEvent::class => 'onMessage',
        ];
    }

    /**
     * @param MessageEvent $event
     */
    public function onMessage(MessageEvent $event)
    {
        $email = $event->getMessage();
        if (!$email instanceof Email) {
            throw new RuntimeException('The message type is not Email.');
        }
        $email->from($this->from);
    }
}
