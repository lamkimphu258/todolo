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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        protected UserRepository $userRepository
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

            $mailer->sendWelcomeMessage($user);

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
}
