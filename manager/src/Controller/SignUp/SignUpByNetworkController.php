<?php

declare(strict_types=1);

namespace App\Controller\SignUp;

use App\Model\User\UseCase\SignUpByEmail;
use DomainException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SignUpByNetworkController extends AbstractController
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SignUpByNetworkController constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/signup", name="signup.request")
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
                $this->addFlash('success', 'Confirm your email address.');

                return $this->redirectToRoute('home');
            } catch (DomainException $e) {
                $this->addFlash('error', $e->getMessage());
                $this->logger->error($e->getMessage(), ['exception' => $e]);
            }
        }

        return $this->render('signup/signup_by_email.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/signup/{token}", name="signup.confirm")
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
            $this->addFlash('error', $e->getMessage());
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }

        return $this->redirectToRoute('home');
    }
}
