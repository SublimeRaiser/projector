<?php

declare(strict_types=1);

namespace App\Model\Auth\Service;

use App\Model\Auth\Entity\User\Email;
use App\Model\Auth\Entity\User\ResetToken;
use Exception;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class ResetTokenSender
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
     * @param Email      $email
     * @param ResetToken $token
     *
     * @throws TransportExceptionInterface
     */
    public function send(Email $email, ResetToken $token): void
    {
        $message = (new TemplatedEmail())
            ->to($email->getValue())
            ->subject('Password Reset')
            ->htmlTemplate('mail/auth/reset_password.html.twig')
            ->context([
                'token' => $token->getValue(),
            ]);
        try {
            $this->mailer->send($message);
        } catch (Exception $e) {
            $exceptionMessage = 'Unable to send message.';
            $this->logger->error($exceptionMessage, ['exception' => $e, 'email' => $email->getValue()]);
            throw new RuntimeException($exceptionMessage);
        }
    }
}
