<?php

namespace App\Controller;

use App\Form\Type\TodoType;
use App\Repository\TodoRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        '',
        name: 'todo-index',
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

            return $this->redirectToRoute('todo-index');
        }

        $todos = $this->toDoRepository->findAll();

        return $this->render('todos/create.html.twig', [
            'form' => $form->createView(),
            'todos' => $todos,
        ]);
    }

    #[Route(
        '/{slug}',
        name: 'todo-delete',
        methods: [Request::METHOD_GET]
    )]
    public function delete(string $slug) {
        $todo = $this->toDoRepository->findOneBySlug($slug);
        $this->toDoRepository->delete($todo);

        $this->addFlash('success', 'Deleted todo successfully');
        return $this->redirectToRoute('todo-index');
    }
}
