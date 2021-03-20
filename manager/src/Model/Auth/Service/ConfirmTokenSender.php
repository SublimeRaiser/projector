<?php

declare(strict_types=1);

namespace App\Model\Auth\Service;

use App\Model\Auth\Entity\User\Email;
use Exception;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class ConfirmTokenSender
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ConfirmTokenSender constructor.
     *
     * @param MailerInterface $mailer
     * @param LoggerInterface $logger
     */
    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    /**
     * @param Email  $email
     * @param string $token
     *
     * @throws TransportExceptionInterface
     */
    public function send(Email $email, string $token): void
    {
        $email = (new TemplatedEmail())
            ->to($email->getValue())
            ->subject('Sign Up Confirmation')
            ->htmlTemplate('mail/auth/signup.html.twig')
            ->context([
                'token' => $token,
            ]);
        try {
            $this->mailer->send($email);
        } catch (Exception $e) {
            $message = 'Unable to send message.';
            $this->logger->error($message, ['exception' => $e]);
            throw new RuntimeException($message);
        }
    }
}
