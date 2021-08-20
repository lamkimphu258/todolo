<?php

namespace App\Controller;

use App\Repository\TodoRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TodoType;

#[Route('/todos')]
class TodoController extends AbstractController
{
    public function __construct(
        protected TodoRepository $toDoRepository
    ) {
    }

    /**
     * @param Request $request
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route(
        '/',
        name: 'todo-create',
        methods: [Request::METHOD_GET, Request::METHOD_POST]
    )]
    public function create(Request $request): Response
    {
        $form = $this->createForm(TodoType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $todo = $form->getData();
            $this->toDoRepository->save($todo);
            $this->addFlash('success', 'Todo Created');

            return $this->redirectToRoute('todo-create');
        }

        return $this->render('todos/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
