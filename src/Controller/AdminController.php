<?php

namespace App\Controller;

use Symfony\{
    Bundle\FrameworkBundle\Controller\AbstractController,
    Component\HttpFoundation\Response,
    Component\Routing\Annotation\Route,
};

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController {

    #[Route('/', name: 'home')]
    public function index(): Response {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
