<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\Type\TodoType;
use App\Form\Type\TodoUpdateType;
use App\Repository\TodoRepository;
use App\Repository\UserRepository;
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
        protected TodoRepository $toDoRepository,
        protected UserRepository $userRepository
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
        name: 'app_todo_index',
        methods: [Request::METHOD_GET, Request::METHOD_POST]
    )]
    public function index(Request $request): Response
    {
        $form = $this->createForm(TodoType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $todo = $form->getData();
            /** @var Todo $todo */
            foreach ($this->getUser()->getTodos() as $userTodo) {
                if ($userTodo->getName() === $todo->getName()) {
                    $this->addFlash('error', 'You already have this todo');
                    return $this->redirectToRoute('app_todo_index');
                }
            }
            $todo->setAuthor($this->getUser());
            $this->toDoRepository->save($todo);
            $this->addFlash('success', 'Todo Created');

            return $this->redirectToRoute('app_todo_index');
        }

        $todos = $this->userRepository->findAllTodosBelongsTo($this->getUser());

        return $this->render('todos/index.html.twig', [
            'form' => $form->createView(),
            'todos' => $todos,
        ]);
    }

    #[Route(
        '/{slug}/delete',
        name: 'app_todo_delete',
        methods: [Request::METHOD_GET]
    )]
    public function delete(string $slug)
    {
        $todo = $this->toDoRepository->findOneBySlug($slug);

        if ($todo->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You do not own this todo.');
        }

        $this->toDoRepository->delete($todo);

        $this->addFlash('success', 'Deleted todo successfully');
        return $this->redirectToRoute('app_todo_index');
    }

    #[Route('/{slug}/update', name: 'app_todo_update')]
    public function update(Request $request)
    {
        $todo = $this->toDoRepository->findOneBySlug($request->get('slug'));

        $form = $this->createForm(
            TodoUpdateType::class,
            $todo
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $todoData = $form->getData();
            $todo->setName($todoData->getName());

            $this->toDoRepository->save($todo);

            return $this->redirectToRoute('app_todo_index');
        }

        return $this->render('todos/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
