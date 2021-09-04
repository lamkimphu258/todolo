<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserCreateType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use App\Service\Mailer;
use DateTimeImmutable;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        protected UserRepository $userRepository,
        protected VerifyEmailHelperInterface $helper
    ) {
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_todo_index');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @param LoginFormAuthenticator $authenticator
     * @param UserAuthenticatorInterface $userAuthenticator
     * @return Response
     */
    #[Route("/register", name: "app_register")]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        LoginFormAuthenticator $authenticator,
        UserAuthenticatorInterface $userAuthenticator,
        Mailer $mailer
    ): Response {
        $form = $this->createForm(UserCreateType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setAvatarFilename('avatar-placeholder.png');

            if ($form->get('agreeTerms')->getData()) {
                $user->setAgreeTermsAt(new DateTimeImmutable());
            }

            $this->userRepository->save($user);

            $mailer->sendVerificationEmail($user);

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new LogicException(
            'This method can be blank - it will be intercepted by the logout key on your firewall.'
        );
    }

    /**
     * @Route("/verify", name="registration_confirmation_route")
     */
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        // Do not get the User's Id or Email Address from the Request object
        try {
            $this->helper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('verify_email_error', $e->getReason());

            return $this->redirectToRoute('app_register');
        }

        $newUser = $this->userRepository->findByEmail($user->getEmail());
        $newUser->setVerifiedAt(new DateTimeImmutable());
        $this->userRepository->save($newUser);

        $this->addFlash('success', 'Your e-mail address has been verified.');

        return $this->redirectToRoute('app_home');
    }

    #[Route('reverify/email', name: 'app_resend_verification_email', methods: [Request::METHOD_GET])]
    public function resendVerificationEmail(Mailer $mailer): RedirectResponse
    {
        $currentUser = $this->userRepository->findByEmail($this->getUser()->getUserIdentifier());
        $mailer->sendVerificationEmail($currentUser);

        return $this->redirectToRoute('app_user_get', ['id' => $currentUser->getId()]);
    }
}
