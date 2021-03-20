<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Model\Auth\UseCase\SignUpByEmail;
use DomainException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SignUpByEmailController extends AbstractController
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
     * SignUpByNetworkController constructor.
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
     * @Route("/signup", name="auth.signup.request")
     *
     * @param Request                       $request
     * @param SignUpByEmail\Request\Handler $handler
     * @return Response
     */
    public function request(
        Request $request,
        SignUpByEmail\Request\Handler $handler
    ): Response {
        $command = new SignUpByEmail\Request\Command();
        $form    = $this->createForm(SignUpByEmail\Request\Form::class, $command);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Check your email for the confirmation link.');

                return $this->redirectToRoute('home');
            } catch (DomainException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
                $this->logger->error($e->getMessage(), ['exception' => $e]);
            }
        }

        return $this->render('auth/signup_by_email.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/signup/{token}", name="auth.signup.confirm")
     * @param string                        $token
     * @param SignUpByEmail\Confirm\Handler $handler
     *
     * @return Response
     */
    public function confirm(string $token, SignUpByEmail\Confirm\Handler $handler): Response
    {
        $command = new SignUpByEmail\Confirm\Command($token);
        try {
            $handler->handle($command);
            $this->addFlash('success', 'Email is successfully confirmed.');
        } catch (DomainException $e) {
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }

        return $this->redirectToRoute('home');
    }
}
