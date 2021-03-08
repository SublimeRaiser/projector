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
     * @var TemplatedEmail
     */
    private $templatedEmail;

    /**
     * @var array
     */
    private $from;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ConfirmTokenSender constructor.
     *
     * @param MailerInterface $mailer
     * @param TemplatedEmail  $templatedEmail
     * @param array           $from
     * @param LoggerInterface $logger
     */
    public function __construct(
        MailerInterface $mailer,
        TemplatedEmail $templatedEmail,
        array $from,
        LoggerInterface $logger
    ) {
        $this->mailer         = $mailer;
        $this->templatedEmail = $templatedEmail;
        $this->from           = $from;
        $this->logger         = $logger;
    }

    /**
     * @param Email      $email
     * @param ResetToken $token
     *
     * @throws TransportExceptionInterface
     */
    public function send(Email $email, ResetToken $token): void
    {
        $email = $this->templatedEmail
            ->from(...$this->from)
            ->to($email->getValue())
            ->subject('Password Reset')
            ->htmlTemplate('mail/auth/reset_password.html.twig')
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
