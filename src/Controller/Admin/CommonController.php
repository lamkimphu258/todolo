<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommonController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route('/admin', name: 'admin_main_dashboard',methods: [Request::METHOD_GET])]
    public function home(): Response
    {
        return $this->render('admin/dashboards/main-dashboard.html.twig');
    }
}
