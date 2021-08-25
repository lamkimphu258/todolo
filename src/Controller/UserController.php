<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserUpdateType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
class UserController extends AbstractController
{
    public function __construct(
        protected UserRepository $userRepository
    ) {
    }

    #[Route('/users/{id}', name: 'app_user_get')]
    public function index(): Response
    {
        return $this->render('user/profile.html.twig');
    }

    #[Route(
        '/users/{id}/edit',
        name: 'app_user_update',
        methods: [Request::METHOD_POST, Request::METHOD_GET]
    )]
    public function update(Request $request): Response
    {
        $form = $this->createForm(UserUpdateType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $userData = $form->getData();
            $user = $this->userRepository->findByEmail(
                $this->getUser()->getUserIdentifier()
            );
            $user->setEmail($userData->getEmail());

            $this->userRepository->save($user);

            return $this->redirectToRoute('app_user_get', [
                'id' => $request->get('id'),
            ]);
        }

        return $this->render('user/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
