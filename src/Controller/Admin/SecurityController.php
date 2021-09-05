<?php

namespace App\Controller\Admin;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(
        '/admin/login',
        name: 'admin_login'
    )]
    public function login(AuthenticationUtils $authenticationUtils
    ): RedirectResponse|Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            '@EasyAdmin/page/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
                'page_title' => '<h1 class="text-center mb-3">Todolo CMS</h1>',
                'csrf_token_intention' => 'authenticate',
                'target_path' => $this->generateUrl('admin'),
                'username_parameter' => 'email',
                'password_parameter' => 'password',
            ]
        );
    }

    /**
     * @Route("/admin/logout", name="admin_logout")
     */
    public function logout()
    {
        throw new LogicException(
            'This method can be blank - it will be intercepted by the logout key on your firewall.'
        );
    }

}
