<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserUpdateType;
use App\Repository\UserRepository;
use App\Service\UploaderHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
class UserController extends AbstractController
{
    public function __construct(
        protected UserRepository $userRepository,
        protected UploaderHelper $helper,
        protected Filesystem $filesystem
    ) {
    }

    #[Route('/users/{id}', name: 'app_user_get')]
    public function index(): Response
    {
        $currentUser = $this->userRepository->findByEmail(
            $this->getUser()->getUserIdentifier()
        );
        $isVerified = !is_null($currentUser->getVerifiedAt());
        return $this->render(
            'user/profile.html.twig',
            ['isVerified' => $isVerified]
        );
    }

    #[Route(
        '/users/{id}/edit',
        name: 'app_user_update',
        methods: [Request::METHOD_POST, Request::METHOD_GET]
    )]
    public function update(Request $request, string $avatarsPath): Response
    {
        $form = $this->createForm(UserUpdateType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $userData = $form->getData();
            $user = $this->userRepository->findByEmail(
                $this->getUser()->getUserIdentifier()
            );

            $uploadedFile = $form->get('uploadedFile')->getData();

            if ($uploadedFile) {
                $newFilename = $this->helper->uploadUserImage($uploadedFile);
                if ($user->getAvatarFilename()) {
                    $this->filesystem->remove([$avatarsPath . '/' . $user->getAvatarFilename()]);
                }
                $user->setAvatarFilename($newFilename);
            }
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
