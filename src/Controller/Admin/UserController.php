<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Type\Admin\UserCreateType;
use App\Repository\UserRepository;
use App\Service\UploaderHelper;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserController extends AbstractController
{
    /**
     * @param UserRepository $userRepository
     * @param UploaderHelper $uploaderHelper
     */
    public function __construct(
        private UserRepository $userRepository,
        private UploaderHelper $uploaderHelper
    )
    {
    }

    /**
     * @param Request $request
     * @param SluggerInterface $slugger
     * @param UserPasswordHasherInterface $passwordHasher
     * @return RedirectResponse|Response
     */
    #[Route(
        '/admin/user/create',
        name: 'admin_user_create',
        methods: [
            Request::METHOD_GET,
            Request::METHOD_POST
        ]
    )]
    public function createUser(
        Request $request,
        SluggerInterface $slugger,
        UserPasswordHasherInterface $passwordHasher
    ): RedirectResponse|Response {
        $form = $this->createForm(UserCreateType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = new User();

            /** @var UploadedFile $avatarFile */
            $avatarFile = $form->get('avatar')->getData();

            if ($avatarFile) {
                $newFilename = $this->uploaderHelper->uploadUserImage($avatarFile);

                $user->setAvatarFilename($newFilename);
            }

            $user->setEmail($form->get('email')->getData())
                ->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                )
                ->setAgreeTermsAt(new DateTimeImmutable())
                ->setSubscribeToNewsletter(false);

            $this->userRepository->save($user);

            return $this->redirectToRoute('admin_user_create');
        }

        return $this->render(
            'admin/user/create.html.twig',
            ['form' => $form->createView()]
        );
    }
}
