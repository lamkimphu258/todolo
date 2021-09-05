<?php

namespace App\Controller\Admin;

use App\Entity\Todo;
use App\Entity\User;
use App\Repository\TodoRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @param UserRepository $userRepository
     * @param TodoRepository $todoRepository
     */
    public function __construct(
        private UserRepository $userRepository,
        private TodoRepository $todoRepository,
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $totalUser = count($this->userRepository->findAll());
        $totalTodo = count($this->todoRepository->findAll());

        return $this->render(
            'admin/dashboards/main-dashboard.html.twig',
            [
                'totalUser' => $totalUser,
                'totalTodo' => $totalTodo,
            ]
        );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Todolo CMS')
            ->setTextDirection('ltr');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::section('Management'),
            MenuItem::linkToCrud('Users', 'fa fa-user', User::class),
            MenuItem::linkToCrud('Todos', 'fa fa-tasks', Todo::class),
        ];
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addCssFile('build/app.css');
    }
}
