<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Model\Auth\UseCase\ResetPassword;
use App\ReadModel\Auth\UserFetcher;
use DomainException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as Route;
use Symfony\Contracts\Translation\TranslatorInterface;


class ResetPasswordController extends AbstractController
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ResetPasswordController constructor.
     *
     * @param LoggerInterface     $logger
     * @param TranslatorInterface $translator
     */
    public function __construct(LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->logger     = $logger;
        $this->translator = $translator;
    }

    /**
     * @Route("/reset-password", name="auth.reset_password.request")
     *
     * @param Request                       $request
     * @param ResetPassword\Request\Handler $handler
     *
     * @return Response
     */
    public function request(
        Request $request,
        ResetPassword\Request\Handler $handler
    ): Response {
        $command = new ResetPassword\Request\Command();
        $form    = $this->createForm(ResetPassword\Request\Form::class, $command);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Check your email for the reset link.');

                return $this->redirectToRoute('home');
            } catch (DomainException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
                $this->logger->error($e->getMessage(), ['exception' => $e]);
            }
        }

        return $this->render('auth/reset_password/request.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reset-password/{token}", name="auth.reset_password.reset")
     *
     * @param string                      $token
     * @param Request                     $request
     * @param ResetPassword\Reset\Handler $handler
     * @param UserFetcher                 $users
     *
     * @return Response
     */
    public function reset(
        string $token,
        Request $request,
        ResetPassword\Reset\Handler $handler,
        UserFetcher $users
    ): Response {
        if (!$users->existsByResetToken($token)) {
            $this->addFlash('error', 'Invalid or already used token.');

            return $this->redirectToRoute('home');
        }

        $command = new ResetPassword\Reset\Command($token);
        $form    = $this->createForm(ResetPassword\Reset\Form::class, $command);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Password is successfully changed.');

                return $this->redirectToRoute('home');
            } catch (DomainException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
                $this->logger->error($e->getMessage(), ['exception' => $e]);
            }
        }

        return $this->render('auth/reset_password/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
